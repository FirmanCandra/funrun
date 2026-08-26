<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'plain_password', 'role', 'event_id'])]
#[Hidden(['password', 'plain_password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SUPER_ADMIN = 'super_admin';

    /**
     * Semua role yang dikenal aplikasi.
     *
     * @var list<string>
     */
    public const ROLES = [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Cek apakah user memiliki salah satu role yang diberikan.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Admin event maupun super admin — keduanya boleh masuk panel admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN);
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Halaman tujuan setelah login, sesuai role.
     */
    public function homeRoute(): string
    {
        return $this->isAdmin() ? route('admin.dashboard') : route('dashboard');
    }
}
