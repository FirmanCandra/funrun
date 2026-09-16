<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $participants;
    protected $customFields;
    protected $coreFields;

    public function __construct($participants, $customFields, $coreFields = [])
    {
        $this->participants = $participants;
        $this->customFields = $customFields;
        $this->coreFields = $coreFields;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->participants;
    }

    public function headings(): array
    {
        $header = [
            'ID', 'Nama Lengkap',
        ];
        
        if (in_array('nik', $this->coreFields)) $header[] = 'NIK';
        if (in_array('city', $this->coreFields)) $header[] = 'Asal Kota';
        $header[] = 'Email';
        if (in_array('phone', $this->coreFields)) $header[] = 'No. WhatsApp';
        if (in_array('category', $this->coreFields)) $header[] = 'Kategori';
        if (in_array('jersey_size', $this->coreFields)) $header[] = 'Ukuran T-Shirt';
        if (in_array('medical_history', $this->coreFields)) $header[] = 'Riwayat Penyakit';
        
        $header = array_merge($header, [
            'Kode Pesanan', 'Kode Tiket', 'Status Pembayaran', 'Status Check-in'
        ]);

        foreach ($this->customFields as $field) {
            $header[] = $field->label;
        }

        $header[] = 'Password';

        return $header;
    }

    public function map($p): array
    {
        $paymentStatus = 'Pending';
        if ($p->ticket && $p->ticket->order) {
            $paymentStatus = $p->ticket->order->payment_status;
        }

        $row = [
            $p->id,
            $p->displayName(),
        ];

        if (in_array('nik', $this->coreFields)) $row[] = $p->nik ?? '-';
        if (in_array('city', $this->coreFields)) $row[] = $p->city ?? '-';
        $row[] = $p->user->email ?? '-';
        if (in_array('phone', $this->coreFields)) $row[] = $p->phone ?? '-';
        if (in_array('category', $this->coreFields)) $row[] = $p->category ?? '-';
        if (in_array('jersey_size', $this->coreFields)) $row[] = $p->jersey_size ?? '-';
        if (in_array('medical_history', $this->coreFields)) $row[] = $p->medical_history ?? '-';

        $row[] = $p->ticket->order->order_code ?? '-';
        $row[] = $p->ticket->ticket_code ?? '-';
        $row[] = strtoupper($paymentStatus);
        $row[] = strtoupper($p->ticket->status ?? 'pending');

        foreach ($this->customFields as $field) {
            // Peserta dari event lain tidak punya field ini — biarkan kosong.
            $row[] = $field->event_id === $p->event_id ? $p->customAnswer($field) : '';
        }

        $row[] = $p->user->plain_password ?? '';

        return $row;
    }
}
