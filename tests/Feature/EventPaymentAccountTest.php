<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPaymentAccount;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tiap event punya rekening tujuan transfer sendiri. Hanya super admin yang
 * boleh mengubahnya; admin event hanya melihat untuk mencocokkan bukti masuk.
 */
class EventPaymentAccountTest extends TestCase
{
    use RefreshDatabase;

    private Event $voltRhythm;

    private Event $stepUpFest;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->voltRhythm = $this->makeEvent('VOLT RHYTHM 2026', '1110001111', 'Panitia Volt');
        $this->stepUpFest = $this->makeEvent('STEP UP FEST 2026', '2220002222', 'Panitia StepUp');
    }

    private function makeEvent(string $title, ?string $accountNumber = null, ?string $holder = null): Event
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

        if ($accountNumber) {
            EventPaymentAccount::create([
                'event_id' => $event->id,
                'bank_name' => 'Transfer Bank BCA',
                'account_number' => $accountNumber,
                'account_holder' => $holder,
            ]);
        }

        return $event;
    }

    private function orderPayload(Event $event, ?int $accountId = null): array
    {
        return [
            'event_id' => $event->id,
            'payment_account_id' => $accountId ?? $event->paymentAccounts()->value('id'),
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
            'participants' => [[
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
            ]],
        ];
    }

    // ===== Hanya super admin yang boleh mengubah rekening =====

    public function test_super_admin_can_add_an_account(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post('/admin/payment-accounts', [
            'event_id' => $this->voltRhythm->id,
            'bank_name' => 'E-Wallet DANA',
            'account_number' => '081234567890',
            'account_holder' => 'Bendahara Volt',
        ])->assertRedirect();

        $this->assertSame(2, $this->voltRhythm->paymentAccounts()->count());
    }

    public function test_event_admin_cannot_add_an_account(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->post('/admin/payment-accounts', [
            'event_id' => $this->voltRhythm->id,
            'bank_name' => 'Rekening Pribadi',
            'account_number' => '999',
            'account_holder' => 'Admin Nakal',
        ])->assertForbidden();

        $this->assertSame(1, $this->voltRhythm->paymentAccounts()->count());
    }

    public function test_event_admin_cannot_change_an_account_number(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $account = $this->voltRhythm->paymentAccounts()->first();

        $this->actingAs($admin)->put('/admin/payment-accounts/'.$account->id, [
            'bank_name' => 'Transfer Bank BCA',
            'account_number' => '000000',
            'account_holder' => 'Admin Nakal',
        ])->assertForbidden();

        $this->assertSame('1110001111', $account->refresh()->account_number);
    }

    public function test_event_admin_cannot_delete_an_account(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();
        $account = $this->voltRhythm->paymentAccounts()->first();

        $this->actingAs($admin)->delete('/admin/payment-accounts/'.$account->id)->assertForbidden();

        $this->assertNotNull($account->fresh());
    }

    public function test_participant_cannot_open_the_accounts_page(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/payment-accounts')->assertForbidden();
    }

    // ===== Admin event boleh melihat rekening event-nya saja =====

    public function test_event_admin_sees_only_their_own_event_accounts(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->get('/admin/payment-accounts')
            ->assertOk()
            ->assertSee('1110001111')
            ->assertDontSee('2220002222');
    }

    public function test_event_admin_cannot_peek_at_another_event_via_query_string(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)->get('/admin/payment-accounts?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertSee('1110001111')
            ->assertDontSee('2220002222');
    }

    public function test_super_admin_can_view_any_event_accounts(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/admin/payment-accounts?event_id='.$this->stepUpFest->id)
            ->assertOk()
            ->assertSee('2220002222');
    }

    // ===== Pengaruh ke halaman pembelian =====

    public function test_purchase_form_shows_only_the_events_own_accounts(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get('/register-event?event_id='.$this->voltRhythm->id)
            ->assertOk()
            ->assertSee('1110001111')
            ->assertSee('Panitia Volt')
            ->assertDontSee('2220002222');
    }

    public function test_event_without_accounts_cannot_sell_tickets(): void
    {
        $buyer = User::factory()->create();
        $event = $this->makeEvent('TANPA REKENING 2026');

        $this->actingAs($buyer)->get('/register-event?event_id='.$event->id)
            ->assertOk()
            ->assertSee('Pendaftaran belum dibuka');

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($event, 999))
            ->assertSessionHasErrors('payment_account_id');

        $this->assertSame(0, Order::count());
    }

    public function test_buyer_cannot_pick_an_account_from_another_event(): void
    {
        $buyer = User::factory()->create();
        $foreignAccount = $this->stepUpFest->paymentAccounts()->value('id');

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, $foreignAccount))
            ->assertSessionHasErrors('payment_account_id');

        $this->assertSame(0, Order::count());
    }

    public function test_inactive_account_cannot_be_used(): void
    {
        $buyer = User::factory()->create();
        $account = $this->voltRhythm->paymentAccounts()->first();
        $account->update(['is_active' => false]);

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, $account->id))
            ->assertSessionHasErrors('payment_account_id');
    }

    // ===== Pesanan menyimpan salinan rekening =====

    public function test_order_stores_a_snapshot_of_the_account_used(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm))
            ->assertSessionHasNoErrors();

        $order = Order::first();

        $this->assertSame('Transfer Bank BCA', $order->payment_method);
        $this->assertSame('1110001111', $order->payment_account_number);
        $this->assertSame('Panitia Volt', $order->payment_account_holder);
        $this->assertSame($this->voltRhythm->paymentAccounts()->value('id'), $order->payment_account_id);
    }

    public function test_snapshot_survives_the_account_number_being_changed_later(): void
    {
        $buyer = User::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->voltRhythm));
        $order = Order::first();
        $account = $this->voltRhythm->paymentAccounts()->first();

        // Super admin mengganti nomor rekening sesudah pesanan masuk.
        $this->actingAs($superAdmin)->put('/admin/payment-accounts/'.$account->id, [
            'bank_name' => 'Transfer Bank BCA',
            'account_number' => '5555555555',
            'account_holder' => 'Bendahara Baru',
            'is_active' => '1',
        ])->assertRedirect();

        // Pesanan lama tetap menunjukkan ke mana pembeli sebenarnya transfer.
        $this->assertSame('1110001111', $order->refresh()->payment_account_number);
        $this->assertSame('5555555555', $account->refresh()->account_number);
    }

    public function test_snapshot_survives_the_account_being_deleted(): void
    {
        $buyer = User::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->voltRhythm));
        $order = Order::first();
        $account = $this->voltRhythm->paymentAccounts()->first();

        $this->actingAs($superAdmin)->delete('/admin/payment-accounts/'.$account->id)->assertRedirect();

        $order->refresh();
        $this->assertNull($order->payment_account_id);
        $this->assertSame('1110001111', $order->payment_account_number);
        $this->assertStringContainsString('1110001111', $order->paymentTargetLabel());
    }

    // ===== Bukti transfer terlihat admin event =====

    public function test_admin_sees_the_proof_and_the_target_account_in_the_queue(): void
    {
        $buyer = User::factory()->create();
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->voltRhythm));
        $order = Order::first();

        $this->assertNotNull($order->proof_of_payment);
        Storage::disk('public')->assertExists($order->proof_of_payment);

        $this->actingAs($admin)->get('/admin/orders')
            ->assertOk()
            ->assertSee('Lihat Bukti')
            ->assertSee('1110001111')
            ->assertSee('Panitia Volt');
    }

    public function test_buyer_sees_the_target_account_on_their_order_page(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->voltRhythm));
        $order = Order::first();

        $this->actingAs($buyer)->get('/orders/'.$order->id)
            ->assertOk()
            ->assertSee('1110001111')
            ->assertSee('Panitia Volt');
    }
}
