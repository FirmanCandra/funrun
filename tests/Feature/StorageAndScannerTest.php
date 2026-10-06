<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageAndScannerTest extends TestCase
{
    use RefreshDatabase;

    private function createTestEvent(string $title = 'Test Marathon'): Event
    {
        return Event::create([
            'title' => $title,
            'date' => '2026-09-15',
            'location' => 'Jakarta',
            'quota' => 5000,
        ]);
    }

    public function test_storage_fallback_route_serves_disk_public_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('proofs/test-proof.jpg', 'fake-image-binary-data');

        $response = $this->get('/storage/proofs/test-proof.jpg');

        $response->assertStatus(200);
        $this->assertEquals('fake-image-binary-data', $response->streamedContent());
    }

    public function test_storage_fallback_returns_404_for_missing_file(): void
    {
        Storage::fake('public');

        $response = $this->get('/storage/proofs/non-existent-image.jpg');

        $response->assertStatus(404);
    }

    public function test_order_proof_url_helper_generates_clean_storage_url(): void
    {
        $user = User::factory()->create();
        $event = $this->createTestEvent();

        $order = Order::create([
            'order_code' => Order::generateCode(),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_amount' => 150000,
            'payment_method' => 'Transfer BCA',
            'payment_status' => Order::STATUS_WAITING,
            'proof_of_payment' => 'proofs/sample_proof.jpg',
        ]);

        $this->assertStringContainsString('/storage/proofs/sample_proof.jpg', $order->proofUrl());

        // Also test if stored with redundant 'storage/' prefix
        $order->proof_of_payment = 'storage/proofs/sample_proof.jpg';
        $this->assertStringContainsString('/storage/proofs/sample_proof.jpg', $order->proofUrl());
    }

    public function test_admin_scanner_page_can_be_accessed_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.scanner'));

        $response->assertStatus(200);
        $response->assertSee('Check-in Scanner E-Ticket');
        $response->assertSee('html5-qrcode.min.js');
    }

    public function test_admin_can_scan_valid_ticket_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create();
        $event = $this->createTestEvent('Marathon 2026');

        $order = Order::create([
            'order_code' => Order::generateCode(),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_amount' => 150000,
            'payment_status' => Order::STATUS_PAID,
        ]);

        $participant = Participant::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'fullname' => 'John Doe',
            'phone' => '081234567890',
            'category' => '5K',
        ]);

        $ticket = Ticket::create([
            'order_id' => $order->id,
            'participant_id' => $participant->id,
            'ticket_code' => 'ST-TEST-0001',
            'qr_code' => 'ST-TEST-0001',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.scan'), [
            'qr_code' => 'ST-TEST-0001',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'participant' => 'John Doe',
            'ticket_code' => 'ST-TEST-0001',
        ]);

        $this->assertEquals('checked-in', $ticket->fresh()->status);
    }

    public function test_admin_scan_rejects_duplicate_check_in(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create();
        $event = $this->createTestEvent();

        $order = Order::create([
            'order_code' => Order::generateCode(),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_amount' => 150000,
            'payment_status' => Order::STATUS_PAID,
        ]);

        $participant = Participant::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'fullname' => 'Jane Doe',
            'phone' => '081234567891',
            'category' => '10K',
        ]);

        $ticket = Ticket::create([
            'order_id' => $order->id,
            'participant_id' => $participant->id,
            'ticket_code' => 'ST-TEST-0002',
            'qr_code' => 'ST-TEST-0002',
            'status' => 'checked-in',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.scan'), [
            'qr_code' => 'ST-TEST-0002',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonFragment(['participant' => 'Jane Doe']);
    }

    public function test_admin_scan_handles_qr_url_format(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $user = User::factory()->create();
        $event = $this->createTestEvent();

        $order = Order::create([
            'order_code' => Order::generateCode(),
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_amount' => 150000,
            'payment_status' => Order::STATUS_PAID,
        ]);

        $participant = Participant::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'fullname' => 'Alice Runner',
            'phone' => '081234567892',
            'category' => '21K',
        ]);

        $ticket = Ticket::create([
            'order_id' => $order->id,
            'participant_id' => $participant->id,
            'ticket_code' => 'ST-URL-9999',
            'qr_code' => 'ST-URL-9999',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.scan'), [
            'qr_code' => 'https://setiket.id/ticket/ST-URL-9999',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'participant' => 'Alice Runner',
            'ticket_code' => 'ST-URL-9999',
        ]);
    }
}
