<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_google_button(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Masuk dengan Google');
        $response->assertSee(route('auth.google'));
    }

    public function test_register_page_renders_google_button(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSee('Daftar dengan Google');
        $response->assertSee(route('auth.google'));
    }

    public function test_google_redirect_warns_when_not_configured(): void
    {
        config(['services.google.client_id' => '']);
        config(['services.google.client_secret' => '']);

        $response = $this->get('/auth/google');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_with_error_redirects_to_login(): void
    {
        $response = $this->get('/auth/google/callback?error=access_denied&error_description=User+cancelled');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_callback_creates_and_authenticates_new_user(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        config(['services.google.client_secret' => 'test-client-secret']);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-123456');
        $abstractUser->shouldReceive('getName')->andReturn('Budi Google');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);
        $abstractUser->shouldReceive('getEmail')->andReturn('budi@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Budi Google', $user->name);
        $this->assertSame('google-id-123456', $user->google_id);
        $this->assertSame(User::ROLE_USER, $user->role);
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        config(['services.google.client_secret' => 'test-client-secret']);

        $existing = User::factory()->create([
            'email' => 'existing@gmail.com',
            'google_id' => null,
            'role' => User::ROLE_USER,
        ]);

        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-id-789');
        $abstractUser->shouldReceive('getName')->andReturn('Existing User');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);
        $abstractUser->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar2.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($existing);

        $existing->refresh();
        $this->assertSame('google-id-789', $existing->google_id);
    }
}
