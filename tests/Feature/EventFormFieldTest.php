<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventFormField;
use App\Models\EventPaymentAccount;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Formulir pendaftaran bisa berbeda tiap event, diatur oleh admin yang
 * menangani event tersebut.
 */
class EventFormFieldTest extends TestCase
{
    use RefreshDatabase;

    private Event $voltRhythm;

    private Event $stepUpFest;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->voltRhythm = $this->makeEvent('VOLT RHYTHM 2026');
        $this->stepUpFest = $this->makeEvent('STEP UP FEST 2026');
    }

    private function makeEvent(string $title): Event
    {
        $event = Event::create([
            'title' => $title,
            'date' => '2026-09-15',
            'location' => 'Jakarta',
            'quota' => 5000,
        ]);

        EventCategory::create([
            'event_id' => $event->id,
            'name' => '5K Night Run',
            'code' => '5K',
            'bib_code' => 'NR',
            'price' => 150000,
        ]);

        EventPaymentAccount::create([
            'event_id' => $event->id,
            'bank_name' => 'Transfer Bank BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Panitia '.$title,
        ]);

        return $event;
    }

    /**
     * Payload pembelian satu tiket, dengan data core lengkap.
     */
    private function orderPayload(Event $event, array $overrides = [], array $custom = []): array
    {
        $participant = array_merge([
            'fullname' => 'Budi Peserta',
            'nik' => '1234567890123456',
            'phone' => '08123456789',
            'dob' => '2000-01-01',
            'gender' => 'male',
            'address' => 'Jl. Merdeka 1',
            'city' => 'Jakarta',
            'jersey_size' => 'L',
            'emergency_contact' => 'Ibu - 08987654321',
            'category' => '5K',
        ], $overrides);

        if ($custom) {
            $participant['custom'] = $custom;
        }

        return [
            'event_id' => $event->id,
            'payment_account_id' => $event->paymentAccounts()->value('id'),
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
            'participants' => [$participant],
        ];
    }

    private function addCustomField(Event $event, array $attributes = []): EventFormField
    {
        return EventFormField::create(array_merge([
            'event_id' => $event->id,
            'key' => 'nama_komunitas',
            'label' => 'Nama Komunitas Lari',
            'type' => EventFormField::TYPE_TEXT,
            'is_core' => false,
            'enabled' => true,
            'required' => false,
            'sort_order' => 100,
        ], $attributes));
    }

    // ===== Akses panel pengaturan formulir =====

    public function test_event_admin_can_open_the_form_builder_for_their_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->get('/admin/form-fields')
            ->assertOk()
            ->assertSee('VOLT RHYTHM 2026')
            ->assertDontSee('STEP UP FEST 2026');
    }

    public function test_event_admin_cannot_switch_to_another_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        // Meski event_id lain dipaksakan lewat query string, yang dibuka tetap event sendiri.
        $this->actingAs($admin)->get('/admin/form-fields?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertSee('VOLT RHYTHM 2026')
            ->assertDontSee('STEP UP FEST 2026');
    }

    public function test_super_admin_can_choose_any_event(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/admin/form-fields?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertSee('STEP UP FEST 2026');
    }

    public function test_user_cannot_open_the_form_builder(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/form-fields')->assertForbidden();
    }

    public function test_core_fields_are_seeded_on_first_visit(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->assertSame(0, EventFormField::where('event_id', $this->voltRhythm->id)->count());

        $this->actingAs($admin)->get('/admin/form-fields')->assertOk();

        $this->assertSame(
            count(EventFormField::CORE_FIELDS),
            EventFormField::where('event_id', $this->voltRhythm->id)->where('is_core', true)->count()
        );
    }

    // ===== Mengelola field =====

    public function test_admin_can_add_a_custom_field(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/form-fields', [
            'event_id' => $this->voltRhythm->id,
            'label' => 'Nama Komunitas Lari',
            'type' => EventFormField::TYPE_TEXT,
            'required' => '1',
        ])->assertRedirect();

        $field = EventFormField::where('event_id', $this->voltRhythm->id)->where('is_core', false)->first();

        $this->assertNotNull($field);
        $this->assertSame('Nama Komunitas Lari', $field->label);
        $this->assertSame('nama_komunitas_lari', $field->key);
        $this->assertTrue($field->required);
    }

    public function test_custom_field_key_does_not_collide_with_core_columns(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/form-fields', [
            'label' => 'city',
            'type' => EventFormField::TYPE_TEXT,
        ]);

        $field = EventFormField::where('event_id', $this->voltRhythm->id)->where('is_core', false)->first();

        // Diberi awalan supaya tidak menimpa kolom participants.city.
        $this->assertSame('custom_city', $field->key);
    }

    public function test_admin_cannot_edit_a_field_belonging_to_another_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $foreignField = $this->addCustomField($this->stepUpFest);

        $this->actingAs($admin)
            ->put('/admin/form-fields/'.$foreignField->id, ['label' => 'Dibajak'])
            ->assertForbidden();

        $this->assertSame('Nama Komunitas Lari', $foreignField->refresh()->label);
    }

    public function test_admin_cannot_delete_a_field_belonging_to_another_event(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $foreignField = $this->addCustomField($this->stepUpFest);

        $this->actingAs($admin)
            ->delete('/admin/form-fields/'.$foreignField->id)
            ->assertForbidden();

        $this->assertNotNull($foreignField->fresh());
    }

    public function test_core_fields_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        EventFormField::ensureCoreFields($this->voltRhythm->id);

        $coreField = EventFormField::where('event_id', $this->voltRhythm->id)->where('key', 'city')->first();

        $this->actingAs($admin)->delete('/admin/form-fields/'.$coreField->id);

        $this->assertNotNull($coreField->fresh());
    }

    // ===== Pengaruh ke form pembelian =====

    public function test_disabled_core_field_disappears_from_the_purchase_form(): void
    {
        $buyer = User::factory()->create();
        EventFormField::ensureCoreFields($this->voltRhythm->id);
        EventFormField::where('event_id', $this->voltRhythm->id)->where('key', 'address')->update(['enabled' => false]);

        $this->actingAs($buyer)->get('/register-event?event_id='.$this->voltRhythm->id)
            ->assertOk()
            ->assertDontSee('participants[0][address]', false);
    }

    public function test_order_succeeds_without_a_core_field_that_was_disabled(): void
    {
        $buyer = User::factory()->create();
        EventFormField::ensureCoreFields($this->voltRhythm->id);
        EventFormField::where('event_id', $this->voltRhythm->id)
            ->whereIn('key', ['address', 'emergency_contact'])
            ->update(['enabled' => false]);

        $payload = $this->orderPayload($this->voltRhythm);
        unset($payload['participants'][0]['address'], $payload['participants'][0]['emergency_contact']);

        $this->actingAs($buyer)->post('/register-event', $payload)
            ->assertSessionHasNoErrors();

        $participant = Participant::first();
        $this->assertNotNull($participant);
        $this->assertNull($participant->address);
    }

    public function test_core_field_made_optional_can_be_left_empty(): void
    {
        $buyer = User::factory()->create();
        EventFormField::ensureCoreFields($this->voltRhythm->id);
        EventFormField::where('event_id', $this->voltRhythm->id)->where('key', 'city')->update(['required' => false]);

        $payload = $this->orderPayload($this->voltRhythm, ['city' => '']);

        $this->actingAs($buyer)->post('/register-event', $payload)->assertSessionHasNoErrors();
        $this->assertNull(Participant::first()->city);
    }

    public function test_core_field_that_stays_required_still_blocks_an_empty_value(): void
    {
        $buyer = User::factory()->create();
        EventFormField::ensureCoreFields($this->voltRhythm->id);

        $payload = $this->orderPayload($this->voltRhythm, ['city' => '']);

        $this->actingAs($buyer)->post('/register-event', $payload)
            ->assertSessionHasErrors('participants.0.city');
    }

    public function test_custom_field_answer_is_saved(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm);

        $this->actingAs($buyer)->post(
            '/register-event',
            $this->orderPayload($this->voltRhythm, [], ['nama_komunitas' => 'Lari Pagi Squad'])
        )->assertSessionHasNoErrors();

        $this->assertSame('Lari Pagi Squad', Participant::first()->custom_data['nama_komunitas']);
    }

    public function test_required_custom_field_blocks_an_empty_order(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm, ['required' => true]);

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm))
            ->assertSessionHasErrors('participants.0.custom.nama_komunitas');

        $this->assertSame(0, Participant::count());
    }

    public function test_required_consent_must_be_accepted(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm, [
            'key' => 'pernyataan_sehat',
            'label' => 'Saya menyatakan sehat dan siap mengikuti lomba',
            'type' => EventFormField::TYPE_CONSENT,
            'required' => true,
        ]);

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [], ['pernyataan_sehat' => '0']))
            ->assertSessionHasErrors('participants.0.custom.pernyataan_sehat');

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [], ['pernyataan_sehat' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertTrue(Participant::first()->custom_data['pernyataan_sehat']);
    }

    public function test_number_field_rejects_non_numeric_input(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm, [
            'key' => 'target_finish',
            'label' => 'Target Waktu Finish (menit)',
            'type' => EventFormField::TYPE_NUMBER,
            'required' => true,
        ]);

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [], ['target_finish' => 'cepat']))
            ->assertSessionHasErrors('participants.0.custom.target_finish');
    }

    public function test_field_config_is_isolated_per_event(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm, ['required' => true]);

        // Event lain tidak ikut menanyakan field itu.
        $this->actingAs($buyer)->get('/register-event?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertDontSee('Nama Komunitas Lari');

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->stepUpFest))
            ->assertSessionHasNoErrors();
    }

    public function test_custom_field_appears_on_the_purchase_form(): void
    {
        $buyer = User::factory()->create();
        $this->addCustomField($this->voltRhythm, ['help_text' => 'Kosongkan kalau tidak tergabung komunitas']);

        $this->actingAs($buyer)->get('/register-event?event_id='.$this->voltRhythm->id)
            ->assertOk()
            ->assertSee('Nama Komunitas Lari')
            ->assertSee('Kosongkan kalau tidak tergabung komunitas')
            ->assertSee('participants[0][custom][nama_komunitas]', false);
    }

    public function test_custom_answers_are_included_in_the_csv_export(): void
    {
        $buyer = User::factory()->create();
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $this->addCustomField($this->voltRhythm);

        $this->actingAs($buyer)->post(
            '/register-event',
            $this->orderPayload($this->voltRhythm, [], ['nama_komunitas' => 'Lari Pagi Squad'])
        );

        $csv = $this->actingAs($admin)->get('/admin/export-csv')->streamedContent();

        $this->assertStringContainsString('Nama Komunitas Lari', $csv);
        $this->assertStringContainsString('Lari Pagi Squad', $csv);
    }
}
