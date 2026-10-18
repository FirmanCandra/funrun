<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with(
                'error',
                'Fitur Login Google belum dikonfigurasi. Silakan tambahkan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET pada file .env.'
            );
        }

        try {
            if (class_exists(Socialite::class)) {
                return Socialite::driver('google')->redirect();
            }
        } catch (\Throwable $e) {
            Log::warning('Socialite redirect failed, using native OAuth: ' . $e->getMessage());
        }

        // Native OAuth fallback
        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $redirectUri = config('services.google.redirect') ?: url('/auth/google/callback');

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Handle callback from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with(
                'error',
                'Proses login dengan Google dibatalkan atau terjadi kesalahan: ' . $request->query('error_description', $request->query('error'))
            );
        }

        $googleUser = null;

        // 1. Coba lewat Socialite
        try {
            if (class_exists(Socialite::class)) {
                $socialiteUser = Socialite::driver('google')->user();
                if ($socialiteUser) {
                    $googleUser = (object) [
                        'id' => $socialiteUser->getId(),
                        'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname(),
                        'email' => $socialiteUser->getEmail(),
                        'avatar' => $socialiteUser->getAvatar(),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::info('Socialite callback handling failed, attempting native exchange: ' . $e->getMessage());
        }

        // 2. Fallback Native Token Exchange jika Socialite gagal/tidak tersedia
        if (! $googleUser && $request->has('code')) {
            try {
                $code = $request->query('code');
                $clientId = config('services.google.client_id');
                $clientSecret = config('services.google.client_secret');
                $redirectUri = config('services.google.redirect') ?: url('/auth/google/callback');

                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ]);

                if ($response->successful()) {
                    $tokenData = $response->json();
                    $accessToken = $tokenData['access_token'] ?? null;

                    if ($accessToken) {
                        $userinfoResponse = Http::withToken($accessToken)
                            ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                        if ($userinfoResponse->successful()) {
                            $info = $userinfoResponse->json();
                            $googleUser = (object) [
                                'id' => $info['sub'] ?? null,
                                'name' => $info['name'] ?? null,
                                'email' => $info['email'] ?? null,
                                'avatar' => $info['picture'] ?? null,
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Native Google OAuth exchange error: ' . $e->getMessage());
            }
        }

        if (! $googleUser || empty($googleUser->email)) {
            return redirect()->route('login')->with(
                'error',
                'Gagal mengambil informasi profil dari akun Google Anda. Silakan coba kembali.'
            );
        }

        // 3. Hubungkan ke Database User
        try {
            $hasGoogleIdCol = Schema::hasColumn('users', 'google_id');
            $hasAvatarCol = Schema::hasColumn('users', 'avatar');

            $user = null;
            if ($hasGoogleIdCol && !empty($googleUser->id)) {
                $user = User::where('google_id', $googleUser->id)->first();
            }

            if (! $user) {
                $user = User::where('email', $googleUser->email)->first();
            }

            if ($user) {
                // Update Google ID dan Avatar jika belum terpasang
                $updated = false;
                if ($hasGoogleIdCol && empty($user->google_id) && !empty($googleUser->id)) {
                    $user->google_id = $googleUser->id;
                    $updated = true;
                }
                if ($hasAvatarCol && empty($user->avatar) && !empty($googleUser->avatar)) {
                    $user->avatar = $googleUser->avatar;
                    $updated = true;
                }
                if (empty($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $updated = true;
                }
                if ($updated) {
                    $user->save();
                }
            } else {
                // Buat user baru
                $userData = [
                    'name' => $googleUser->name ?: 'Pengguna Google',
                    'email' => $googleUser->email,
                    'role' => User::ROLE_USER,
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ];

                if ($hasGoogleIdCol) {
                    $userData['google_id'] = $googleUser->id;
                }
                if ($hasAvatarCol) {
                    $userData['avatar'] = $googleUser->avatar;
                }

                $user = User::create($userData);
            }

            Auth::login($user, remember: true);
            $request->session()->regenerate();

            if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true)) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('dashboard'));

        } catch (\Throwable $e) {
            Log::error('Google login user processing error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('login')->with(
                'error',
                'Terjadi kesalahan saat memproses data akun: ' . $e->getMessage()
            );
        }
    }
}
