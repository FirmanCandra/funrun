<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function event(): Event
    {
        return Event::create([
            'title' => 'Test Run 2026',
            'date' => '2026-09-15',
            'location' => 'Jakarta',
            'quota' => 5000,
        ]);
    }

    public function test_user_is_redirected_to_participant_dashboard_after_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_admin_is_redirected_to_admin_panel_after_login(): void
    {
        $admin = User::factory()->admin($this->event()->id)->create();

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_is_redirected_to_admin_panel_after_login(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->post('/login', [
            'email' => $superAdmin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_cannot_access_participant_dashboard(): void
    {
        $admin = User::factory()->admin($this->event()->id)->create();

        $this->actingAs($admin)->get('/dashboard')->assertForbidden();
    }

    public function test_admin_cannot_access_super_admin_only_pages(): void
    {
        $admin = User::factory()->admin($this->event()->id)->create();

        $this->actingAs($admin)->get('/admin/admins')->assertForbidden();
        $this->actingAs($admin)->get('/admin/events')->assertForbidden();
    }

    public function test_super_admin_can_access_super_admin_only_pages(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/admin/admins')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/events')->assertOk();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_public_registration_always_creates_a_user_role(): void
    {
        $this->post('/register', [
            'name' => 'Peserta Baru',
            'email' => 'peserta@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertSame(
            User::ROLE_USER,
            User::where('email', 'peserta@example.com')->value('role')
        );
    }

    public function test_participant_dashboard_only_lists_own_orders(): void
    {
        $event = $this->event();
        $mine = User::factory()->create();
        $other = User::factory()->create();

        foreach ([[$mine, 'Punya Saya'], [$other, 'Punya Orang Lain']] as $i => [$owner, $name]) {
            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => $owner->id,
                'event_id' => $event->id,
                'total_amount' => 150000,
                'payment_method' => 'Transfer Bank BCA',
                'payment_status' => Order::STATUS_WAITING,
            ]);

            $participant = Participant::create([
                'user_id' => $owner->id,
                'event_id' => $event->id,
                'fullname' => $name,
                'phone' => '08123456789',
                'nik' => str_pad((string) $i, 16, '0', STR_PAD_LEFT),
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

        $this->actingAs($mine)->get('/dashboard')
            ->assertOk()
            ->assertSee('Punya Saya')
            ->assertDontSee('Punya Orang Lain');
    }
}
