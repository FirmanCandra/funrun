<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Satu baris field pada formulir pendaftaran sebuah event.
 */
class EventFormField extends Model
{
    use HasFactory;

    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_NUMBER = 'number';

    public const TYPE_DATE = 'date';

    public const TYPE_CONSENT = 'consent';

    /** Tipe yang boleh dipilih admin saat membuat field tambahan. */
    public const CUSTOM_TYPES = [
        self::TYPE_TEXT => 'Teks singkat',
        self::TYPE_TEXTAREA => 'Teks panjang',
        self::TYPE_NUMBER => 'Angka',
        self::TYPE_DATE => 'Tanggal',
        self::TYPE_CONSENT => 'Persetujuan (centang)',
    ];

    /**
     * Field bawaan yang boleh diatur admin.
     *
     * Kolomnya sudah ada di tabel `participants`, jadi tipenya tidak bisa
     * diubah — hanya label, wajib/tidak, aktif/tidak, dan urutannya.
     */
    public const CORE_FIELDS = [
        'fullname' => ['label' => 'Nama Lengkap', 'type' => self::TYPE_TEXT, 'required' => true, 'sort' => 10],
        'nik' => ['label' => 'NIK (16 Digit)', 'type' => self::TYPE_TEXT, 'required' => true, 'sort' => 20],
        'phone' => ['label' => 'Nomor WhatsApp', 'type' => self::TYPE_TEXT, 'required' => true, 'sort' => 30],
        'category' => ['label' => 'Kategori Lomba', 'type' => 'select', 'required' => true, 'sort' => 40],
        'dob' => ['label' => 'Tanggal Lahir', 'type' => self::TYPE_DATE, 'required' => true, 'sort' => 50],
        'gender' => ['label' => 'Jenis Kelamin', 'type' => 'select', 'required' => true, 'sort' => 60],
        'jersey_size' => ['label' => 'Ukuran T-Shirt', 'type' => 'select', 'required' => true, 'sort' => 70],
        'emergency_contact' => ['label' => 'Kontak Darurat (Nama - No. Telp)', 'type' => self::TYPE_TEXT, 'required' => true, 'sort' => 80],
        'address' => ['label' => 'Alamat Lengkap', 'type' => self::TYPE_TEXTAREA, 'required' => true, 'sort' => 90],
        'city' => ['label' => 'Asal Kota/Kabupaten', 'type' => self::TYPE_TEXT, 'required' => true, 'sort' => 100],
        'medical_history' => ['label' => 'Riwayat Penyakit', 'type' => self::TYPE_TEXTAREA, 'required' => false, 'sort' => 110],
    ];

    /**
     * Field bawaan yang memengaruhi cara kerja sistem kalau dimatikan.
     * Dipakai panel admin untuk menampilkan peringatannya.
     */
    public const CORE_FIELD_NOTES = [
        'nik' => 'Dipakai mencegah satu orang mendapat dua tiket. Kalau dimatikan, pengecekan itu ikut mati.',
        'phone' => 'Dipakai mengirim e-ticket lewat WhatsApp. Kalau dimatikan, notifikasi tidak terkirim.',
        'category' => 'Menentukan harga dan kode BIB. Kalau dimatikan, semua peserta otomatis memakai kategori pertama event ini.',
        'fullname' => 'Dicetak di e-ticket. Kalau dimatikan, tiket memakai nama pemilik akun pemesan.',
    ];

    /** Pilihan tetap untuk field core bertipe select. */
    public const SELECT_OPTIONS = [
        'gender' => ['male' => 'Laki-laki', 'female' => 'Perempuan'],
        'jersey_size' => ['S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL', '3XL' => '3XL', '4XL' => '4XL', '5XL' => '5XL'],
    ];

    protected $guarded = [];

    protected $casts = [
        'is_core' => 'boolean',
        'enabled' => 'boolean',
        'required' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function isCustom(): bool
    {
        return ! $this->is_core;
    }

    /**
     * Field yang mengisi lebar penuh di form (bukan setengah kolom).
     */
    public function isFullWidth(): bool
    {
        return in_array($this->type, [self::TYPE_TEXTAREA, self::TYPE_CONSENT], true);
    }

    /**
     * Pastikan seluruh field bawaan punya barisnya untuk event ini.
     *
     * Dipanggil sebelum form ditampilkan maupun sebelum validasi, supaya event
     * lama yang dibuat sebelum fitur ini ada tetap punya konfigurasi standar.
     */
    public static function ensureCoreFields(int $eventId): void
    {
        $existing = static::where('event_id', $eventId)
            ->where('is_core', true)
            ->pluck('key')
            ->all();

        foreach (self::CORE_FIELDS as $key => $definition) {
            if (in_array($key, $existing, true)) {
                continue;
            }

            static::create([
                'event_id' => $eventId,
                'key' => $key,
                'label' => $definition['label'],
                'type' => $definition['type'],
                'is_core' => true,
                'enabled' => true,
                'required' => $definition['required'],
                'sort_order' => $definition['sort'],
            ]);
        }
    }

    /**
     * Field aktif untuk sebuah event, sudah terurut.
     */
    public static function activeFor(int $eventId)
    {
        static::ensureCoreFields($eventId);

        return static::where('event_id', $eventId)
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * Buat key unik dari label, mis. "Nama Komunitas" -> "nama_komunitas".
     */
    public static function makeKey(int $eventId, string $label): string
    {
        $base = Str::slug($label, '_') ?: 'field';

        // Jangan sampai bertabrakan dengan nama kolom bawaan.
        if (array_key_exists($base, self::CORE_FIELDS)) {
            $base = 'custom_'.$base;
        }

        $key = $base;
        $suffix = 2;
        while (static::where('event_id', $eventId)->where('key', $key)->exists()) {
            $key = $base.'_'.$suffix;
            $suffix++;
        }

        return $key;
    }
}
