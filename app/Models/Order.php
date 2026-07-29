<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Satu pesanan = satu kali transfer, berisi satu atau banyak tiket.
 */
class Order extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting_verification';

    public const STATUS_PAID = 'paid';

    public const STATUS_REJECTED = 'rejected';

    protected $guarded = [];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Peserta di dalam pesanan ini, lewat tiketnya.
     */
    public function participants()
    {
        return $this->hasManyThrough(
            Participant::class,
            Ticket::class,
            'order_id',      // FK di tickets
            'id',            // PK di participants
            'id',            // PK di orders
            'participant_id' // FK di tickets menuju participants
        );
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isWaiting(): bool
    {
        return $this->payment_status === self::STATUS_WAITING;
    }

    public function isRejected(): bool
    {
        return $this->payment_status === self::STATUS_REJECTED;
    }

    public function statusLabel(): string
    {
        return match ($this->payment_status) {
            self::STATUS_PAID => 'Lunas',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    /**
     * Kode pesanan yang mudah dibaca, mis. ORD-20260729-K3XZ9A.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (static::where('order_code', $code)->exists());

        return $code;
    }
}
