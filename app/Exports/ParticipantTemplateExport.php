<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ParticipantTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $coreFields;
    protected $customFields;

    public function __construct(array $coreFields = [], $customFields = [])
    {
        $this->coreFields = $coreFields;
        $this->customFields = $customFields;
    }

    public function array(): array
    {
        $row = [
            'Budi Santoso',
            'budi@email.com',
        ];

        if (in_array('nik', $this->coreFields)) $row[] = '3374012345678901';
        if (in_array('city', $this->coreFields)) $row[] = 'Semarang';
        if (in_array('phone', $this->coreFields)) $row[] = '081234567890';
        if (in_array('category', $this->coreFields)) $row[] = '5K';
        if (in_array('jersey_size', $this->coreFields)) $row[] = 'L';
        if (in_array('medical_history', $this->coreFields)) $row[] = 'Tidak ada';

        foreach ($this->customFields as $field) {
            $row[] = 'Contoh Jawaban';
        }

        $row[] = ''; // Kolom Password dikosongkan agar Admin bisa isi sendiri, atau dibiarkan agar sistem buat otomatis

        return [$row];
    }

    public function headings(): array
    {
        $header = [
            'Nama Lengkap',
            'Email',
        ];

        if (in_array('nik', $this->coreFields)) $header[] = 'NIK';
        if (in_array('city', $this->coreFields)) $header[] = 'Asal Kota';
        if (in_array('phone', $this->coreFields)) $header[] = 'No WhatsApp';
        if (in_array('category', $this->coreFields)) $header[] = 'Kategori';
        if (in_array('jersey_size', $this->coreFields)) $header[] = 'Ukuran T-Shirt';
        if (in_array('medical_history', $this->coreFields)) $header[] = 'Riwayat Penyakit';

        foreach ($this->customFields as $field) {
            $header[] = $field->label;
        }

        $header[] = 'Password';

        return $header;
    }
}
