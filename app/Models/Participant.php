<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        // Jawaban field tambahan yang didefinisikan admin event.
        'custom_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    /**
     * Nama yang dicetak di tiket dan daftar peserta.
     *
     * Admin boleh mematikan field nama. Kalau begitu, tiket memakai nama
     * pemilik akun pemesan supaya tidak ada tiket tanpa identitas sama sekali.
     */
    public function displayName(): string
    {
        return $this->fullname ?: ($this->user->name ?? 'Peserta');
    }

    /**
     * Jawaban satu field tambahan, sudah diformat untuk ditampilkan.
     */
    public function customAnswer(EventFormField $field): string
    {
        $value = $this->custom_data[$field->key] ?? null;

        if ($field->type === EventFormField::TYPE_CONSENT) {
            return $value ? 'Ya' : 'Tidak';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        return (string) $value;
    }
}
