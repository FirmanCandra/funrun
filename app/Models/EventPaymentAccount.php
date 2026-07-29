<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Rekening tujuan transfer milik sebuah event.
 *
 * Hanya super admin yang boleh menambah, mengubah, atau menghapusnya.
 * Admin event hanya bisa melihat — dia yang mencocokkan bukti transfer masuk,
 * jadi perlu tahu nomornya, tapi tidak boleh mengalihkan aliran uang.
 */
class EventPaymentAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_account_id');
    }

    /**
     * Rekening aktif milik sebuah event, sudah terurut.
     */
    public static function activeFor(int $eventId)
    {
        return static::where('event_id', $eventId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * E-wallet ditampilkan sebagai "No. HP", rekening bank sebagai "No. Rek".
     */
    public function isEwallet(): bool
    {
        return (bool) preg_match('/e-?wallet|dana|gopay|ovo|linkaja|shopeepay|qris/i', $this->bank_name);
    }

    public function numberLabel(): string
    {
        return $this->isEwallet() ? 'No. HP' : 'No. Rek';
    }
}
