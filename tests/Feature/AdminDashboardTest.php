<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_dashboard()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/dashboard');
        $response->assertOk();
    }

    public function test_event_admin_can_access_dashboard()
    {
        $event = Event::create([
            'title' => 'Test Event',
            'date' => '2026-10-25',
            'location' => 'Jakarta',
            'quota' => 1000,
        ]);

        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();
    }

    public function test_admin_without_event_can_access_dashboard()
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'event_id' => null,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk();
    }

    public function test_dashboard_with_orders_and_filtering()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $event = Event::create([
            'title' => 'Rock Festival 2026',
            'date' => '2026-11-10',
            'location' => 'Bandung',
            'quota' => 500,
        ]);

        $buyer = User::factory()->create(['role' => 'user']);

        $order = Order::create([
            'order_code' => 'ORD-TEST-001',
            'user_id' => $buyer->id,
            'event_id' => $event->id,
            'total_amount' => 250000,
            'payment_method' => 'BCA',
            'payment_status' => Order::STATUS_PAID,
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/dashboard?event_id=' . $event->id);
        $response->assertOk();
        $response->assertSee('Rock Festival 2026');
        $response->assertSee('ORD-TEST-001');
    }

    public function test_dashboard_with_non_existent_event_filter()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/dashboard?event_id=99999');
        $response->assertOk();
    }

    public function test_clear_cache_route_and_emergency_flush()
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/clear-cache');
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $emergencyResponse = $this->get('/flush-cache?key=setiket-clear');
        $emergencyResponse->assertOk();
        $emergencyResponse->assertSee('Cache aplikasi');

        $unauthorizedFlush = $this->get('/flush-cache?key=wrong');
        $unauthorizedFlush->assertNotFound();
    }
}
