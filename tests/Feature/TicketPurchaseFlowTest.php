<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPaymentAccount;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Alur beli tiket: user memesan satu atau banyak tiket → pesanan masuk ke admin
 * yang ditugaskan super admin untuk event tersebut → admin memvalidasi sekali →
 * seluruh tiket dalam pesanan itu terbit.
 */
class TicketPurchaseFlowTest extends TestCase
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

        EventCategory::insert([
            ['event_id' => $event->id, 'name' => '5K Night Run', 'code' => '5K', 'bib_code' => 'NR', 'price' => 150000],
            ['event_id' => $event->id, 'name' => '10K Challenger', 'code' => '10K', 'bib_code' => 'CH', 'price' => 250000],
        ]);

        // Tanpa rekening tujuan, event tidak menerima pesanan sama sekali.
        EventPaymentAccount::create([
            'event_id' => $event->id,
            'bank_name' => 'Transfer Bank BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Panitia '.$title,
        ]);

        return $event;
    }

    /**
     * Data satu peserta untuk payload form.
     */
    private function participantPayload(string $name, string $nik, string $category = '5K'): array
    {
        return [
            'fullname' => $name,
            'nik' => $nik,
            'phone' => '08123456789',
            'dob' => '2000-01-01',
            'gender' => 'male',
            'address' => 'Jl. Merdeka 1',
            'city' => 'Jakarta',
            'jersey_size' => 'L',
            'emergency_contact' => 'Ibu - 08987654321',
            'category' => $category,
        ];
    }

    private function orderPayload(Event $event, array $participants): array
    {
        return [
            'event_id' => $event->id,
            'payment_account_id' => $event->paymentAccounts()->value('id'),
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
            'participants' => $participants,
        ];
    }

    /**
     * Membuat pesanan langsung di database, tanpa lewat HTTP.
     */
    private function placeOrder(User $buyer, Event $event, int $tickets = 1, string $name = 'Peserta'): Order
    {
        $order = Order::create([
            'order_code' => Order::generateCode(),
            'user_id' => $buyer->id,
            'event_id' => $event->id,
            'total_amount' => 150000 * $tickets,
            'payment_method' => 'Transfer Bank BCA',
            'payment_account_id' => $event->paymentAccounts()->value('id'),
            'payment_account_number' => '1234567890',
            'payment_status' => Order::STATUS_WAITING,
            'proof_of_payment' => 'proofs/bukti.jpg',
        ]);

        for ($i = 1; $i <= $tickets; $i++) {
            $participant = Participant::create([
                'user_id' => $buyer->id,
                'event_id' => $event->id,
                'fullname' => $tickets === 1 ? $name : "{$name} {$i}",
                'phone' => '08123456789',
                'nik' => str_pad((string) random_int(1, 9999999999999999), 16, '0', STR_PAD_LEFT),
                'city' => 'Jakarta',
                'address' => 'Jl. Test',
                'jersey_size' => 'L',
                'emergency_contact' => '08987654321',
                'category' => '5K',
            ]);

            Ticket::create([
                'participant_id' => $participant->id,
                'order_id' => $order->id,
                'ticket_code' => 'ST-'.$event->id.'-5K-NR-'.str_pad((string) $participant->id, 4, '0', STR_PAD_LEFT),
                'status' => 'pending',
            ]);
        }

        return $order->load('tickets.participant');
    }

    // ===== Pembelian wajib login sebagai peserta =====

    public function test_guest_cannot_open_purchase_form(): void
    {
        $this->get('/register-event?event_id='.$this->voltRhythm->id)
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_buy_tickets(): void
    {
        $admin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->actingAs($admin)
            ->get('/register-event?event_id='.$this->voltRhythm->id)
            ->assertForbidden();
    }

    // ===== Pembelian satu tiket =====

    public function test_user_can_buy_a_single_ticket(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [
                $this->participantPayload('Budi Peserta', '1234567890123456'),
            ]))
            ->assertRedirect();

        $order = Order::first();

        $this->assertSame($buyer->id, $order->user_id);
        $this->assertSame($this->voltRhythm->id, $order->event_id);
        $this->assertSame(Order::STATUS_WAITING, $order->payment_status);
        $this->assertSame('150000.00', $order->total_amount);
        $this->assertCount(1, $order->tickets);
    }

    public function test_order_cannot_reference_a_non_existent_event(): void
    {
        $buyer = User::factory()->create();

        $payload = $this->orderPayload($this->voltRhythm, [
            $this->participantPayload('Budi', '1234567890123456'),
        ]);
        $payload['event_id'] = 9999;

        $this->actingAs($buyer)
            ->post('/register-event', $payload)
            ->assertSessionHasErrors('event_id');

        $this->assertSame(0, Order::count());
    }

    // ===== Pembelian lebih dari satu tiket =====

    public function test_user_can_buy_several_tickets_with_different_categories_in_one_order(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [
                $this->participantPayload('Budi Santoso', '1111111111111111', '5K'),
                $this->participantPayload('Ani Lestari', '2222222222222222', '10K'),
                $this->participantPayload('Citra Dewi', '3333333333333333', '5K'),
            ]))
            ->assertRedirect();

        $order = Order::with('tickets.participant')->first();

        // Satu pesanan, tiga tiket, satu bukti transfer.
        $this->assertCount(3, $order->tickets);
        $this->assertSame(1, Order::count());
        $this->assertNotNull($order->proof_of_payment);

        // Total = 150.000 + 250.000 + 150.000
        $this->assertSame('550000.00', $order->total_amount);

        $names = $order->tickets->map(fn ($t) => $t->participant->fullname)->sort()->values()->all();
        $this->assertSame(['Ani Lestari', 'Budi Santoso', 'Citra Dewi'], $names);

        // Tiap tiket punya kode unik.
        $codes = $order->tickets->pluck('ticket_code');
        $this->assertCount(3, $codes->unique());
    }

    public function test_duplicate_nik_within_the_same_order_is_rejected(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [
                $this->participantPayload('Budi', '1234567890123456'),
                $this->participantPayload('Budi Kembar', '1234567890123456'),
            ]))
            ->assertSessionHasErrors('participants.1.nik');

        $this->assertSame(0, Order::count());
        $this->assertSame(0, Participant::count());
    }

    public function test_nik_already_registered_for_the_event_is_rejected(): void
    {
        $buyer = User::factory()->create();

        Participant::create([
            'user_id' => $buyer->id,
            'event_id' => $this->voltRhythm->id,
            'fullname' => 'Sudah Terdaftar',
            'nik' => '9999999999999999',
            'phone' => '08123456789',
            'city' => 'Jakarta',
            'address' => 'Jl. Test',
            'jersey_size' => 'L',
            'emergency_contact' => '08987654321',
            'category' => '5K',
        ]);

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, [
                $this->participantPayload('Orang Lain', '9999999999999999'),
            ]))
            ->assertSessionHasErrors('participants.0.nik');

        $this->assertSame(0, Order::count());
    }

    public function test_same_nik_may_register_for_a_different_event(): void
    {
        $buyer = User::factory()->create();
        $nik = '8888888888888888';

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->voltRhythm, [
            $this->participantPayload('Budi', $nik),
        ]))->assertSessionHasNoErrors();

        $this->actingAs($buyer)->post('/register-event', $this->orderPayload($this->stepUpFest, [
            $this->participantPayload('Budi', $nik),
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Order::count());
    }

    public function test_order_cannot_exceed_the_ticket_limit(): void
    {
        $buyer = User::factory()->create();

        $participants = [];
        for ($i = 0; $i <= 10; $i++) {
            $participants[] = $this->participantPayload('Peserta '.$i, str_pad((string) $i, 16, '0', STR_PAD_LEFT));
        }

        $this->actingAs($buyer)
            ->post('/register-event', $this->orderPayload($this->voltRhythm, $participants))
            ->assertSessionHasErrors('participants');

        $this->assertSame(0, Order::count());
    }

    // ===== Pesanan masuk ke admin event yang bersangkutan =====

    public function test_event_admin_only_sees_orders_for_their_own_event(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();

        $this->placeOrder(User::factory()->create(), $this->voltRhythm, 1, 'Pembeli Volt');
        $this->placeOrder(User::factory()->create(), $this->stepUpFest, 1, 'Pembeli StepUp');

        $this->actingAs($voltAdmin)->get('/admin/orders')
            ->assertOk()
            ->assertSee('Pembeli Volt')
            ->assertDontSee('Pembeli StepUp');
    }

    public function test_super_admin_sees_orders_from_every_event(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->placeOrder(User::factory()->create(), $this->voltRhythm, 1, 'Pembeli Volt');
        $this->placeOrder(User::factory()->create(), $this->stepUpFest, 1, 'Pembeli StepUp');

        $this->actingAs($superAdmin)->get('/admin/orders')
            ->assertOk()
            ->assertSee('Pembeli Volt')
            ->assertSee('Pembeli StepUp');
    }

    public function test_pending_order_count_is_scoped_to_the_admins_event(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->placeOrder(User::factory()->create(), $this->voltRhythm, 3);
        $this->placeOrder(User::factory()->create(), $this->stepUpFest);
        $this->placeOrder(User::factory()->create(), $this->stepUpFest);

        // Dihitung per pesanan, bukan per tiket.
        $this->assertSame(1, AdminController::pendingOrdersCount($voltAdmin));
        $this->assertSame(3, AdminController::pendingOrdersCount($superAdmin));
    }

    // ===== Validasi oleh admin =====

    public function test_one_approval_issues_every_ticket_in_the_order(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder(User::factory()->create(), $this->voltRhythm, 3);

        $this->actingAs($voltAdmin)
            ->post('/admin/orders/'.$order->id.'/approve')
            ->assertRedirect();

        $order->refresh()->load('tickets');

        $this->assertSame(Order::STATUS_PAID, $order->payment_status);
        $this->assertSame($voltAdmin->id, $order->verified_by);
        $this->assertNotNull($order->verified_at);

        $this->assertCount(3, $order->tickets);
        foreach ($order->tickets as $ticket) {
            $this->assertSame('valid', $ticket->status);
            $this->assertNotNull($ticket->qr_code);
        }
    }

    public function test_event_admin_cannot_approve_an_order_from_another_event(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder(User::factory()->create(), $this->stepUpFest, 2);

        $this->actingAs($voltAdmin)
            ->post('/admin/orders/'.$order->id.'/approve')
            ->assertForbidden();

        $this->assertSame(Order::STATUS_WAITING, $order->refresh()->payment_status);
        foreach ($order->tickets as $ticket) {
            $this->assertSame('pending', $ticket->refresh()->status);
        }
    }

    public function test_admin_can_reject_an_order_with_a_reason(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder(User::factory()->create(), $this->voltRhythm, 2);

        $this->actingAs($voltAdmin)
            ->post('/admin/orders/'.$order->id.'/reject', ['rejection_reason' => 'Nominal tidak sesuai.'])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_REJECTED, $order->payment_status);
        $this->assertSame('Nominal tidak sesuai.', $order->rejection_reason);
    }

    public function test_rejecting_requires_a_reason(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder(User::factory()->create(), $this->voltRhythm);

        $this->actingAs($voltAdmin)
            ->post('/admin/orders/'.$order->id.'/reject', [])
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame(Order::STATUS_WAITING, $order->refresh()->payment_status);
    }

    public function test_event_admin_cannot_check_in_a_ticket_from_another_event(): void
    {
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder(User::factory()->create(), $this->stepUpFest);
        $order->tickets->first()->update(['status' => 'valid', 'qr_code' => 'QR-TEST']);

        $this->actingAs($voltAdmin)
            ->post('/admin/scan', ['qr_code' => 'QR-TEST'])
            ->assertOk()
            ->assertJson(['success' => false]);

        $this->assertSame('valid', $order->tickets->first()->refresh()->status);
    }

    // ===== Peserta melihat hasilnya =====

    public function test_buyer_dashboard_shows_order_status(): void
    {
        $buyer = User::factory()->create();
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder($buyer, $this->voltRhythm, 2);

        $this->actingAs($buyer)->get('/dashboard')
            ->assertOk()
            ->assertSee($order->order_code)
            ->assertSee('Menunggu Verifikasi');

        $this->actingAs($voltAdmin)->post('/admin/orders/'.$order->id.'/approve');

        $this->actingAs($buyer)->get('/dashboard')
            ->assertOk()
            ->assertSee('Lunas');
    }

    public function test_tickets_page_lists_only_issued_tickets_of_the_logged_in_user(): void
    {
        $buyer = User::factory()->create();
        $other = User::factory()->create();
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();

        $mine = $this->placeOrder($buyer, $this->voltRhythm, 2, 'Tiket Saya');
        $pending = $this->placeOrder($buyer, $this->voltRhythm, 1, 'Masih Menunggu');
        $someoneElse = $this->placeOrder($other, $this->voltRhythm, 1, 'Punya Orang Lain');

        // Hanya pesanan pertama yang disetujui.
        $this->actingAs($voltAdmin)->post('/admin/orders/'.$mine->id.'/approve');
        $this->actingAs($voltAdmin)->post('/admin/orders/'.$someoneElse->id.'/approve');

        $this->actingAs($buyer)->get('/tickets')
            ->assertOk()
            ->assertSee('Tiket Saya 1')
            ->assertSee('Tiket Saya 2')
            ->assertDontSee('Masih Menunggu')
            ->assertDontSee('Punya Orang Lain');
    }

    public function test_buyer_cannot_open_someone_elses_order(): void
    {
        $buyer = User::factory()->create();
        $intruder = User::factory()->create();
        $order = $this->placeOrder($buyer, $this->voltRhythm, 2);

        $this->actingAs($intruder)->get('/orders/'.$order->id)->assertForbidden();
        $this->actingAs($buyer)->get('/orders/'.$order->id)->assertOk();
    }

    public function test_buyer_can_reupload_proof_after_rejection(): void
    {
        $buyer = User::factory()->create();
        $voltAdmin = User::factory()->admin($this->voltRhythm->id)->create();
        $order = $this->placeOrder($buyer, $this->voltRhythm);

        $this->actingAs($voltAdmin)
            ->post('/admin/orders/'.$order->id.'/reject', ['rejection_reason' => 'Bukti buram.']);

        $this->actingAs($buyer)
            ->post('/orders/'.$order->id.'/proof', ['proof' => UploadedFile::fake()->image('baru.jpg')])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame(Order::STATUS_WAITING, $order->payment_status);
        $this->assertNull($order->rejection_reason);
    }

    public function test_buyer_cannot_upload_proof_for_someone_elses_order(): void
    {
        $buyer = User::factory()->create();
        $intruder = User::factory()->create();
        $order = $this->placeOrder($buyer, $this->voltRhythm);

        $this->actingAs($intruder)
            ->post('/orders/'.$order->id.'/proof', ['proof' => UploadedFile::fake()->image('palsu.jpg')])
            ->assertForbidden();
    }
}
