<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->status === 'checked-in';
    }

    /**
     * Tiket sudah terbit dan bisa ditunjukkan saat check-in.
     */
    public function isIssued(): bool
    {
        return in_array($this->status, ['valid', 'checked-in'], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'valid' => 'Tiket Aktif',
            'checked-in' => 'Sudah Check-in',
            default => 'Menunggu Verifikasi',
        };
    }
}
