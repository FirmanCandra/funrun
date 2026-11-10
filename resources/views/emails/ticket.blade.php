@extends('emails.layout', [
    'title' => 'E-Ticket Resmi: ' . ($ticket->participant->event->title ?? 'SeTiket'),
    'badgeText' => 'Pembayaran Berhasil',
    'badgeClass' => 'badge-success'
])

@section('content')
<h1 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Halo, {{ $ticket->participant->displayName() }}! 🎉
</h1>

<p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Kabar baik! Pembayaran pendaftaran Anda untuk event <strong>{{ $ticket->participant->event->title ?? 'SeTiket' }}</strong> telah berhasil diverifikasi oleh tim panitia. Berikut adalah detail resmi e-ticket Anda:
</p>

<!-- Meta Card -->
<table class="meta-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="meta-label">Kode Tiket</td>
        <td class="meta-val" style="font-family: monospace; font-size: 15px; color: #2563eb; letter-spacing: 0.5px;">
            {{ $ticket->ticket_code }}
        </td>
    </tr>
    <tr>
        <td class="meta-label">Nama Peserta</td>
        <td class="meta-val">{{ $ticket->participant->displayName() }}</td>
    </tr>
    <tr>
        <td class="meta-label">Event</td>
        <td class="meta-val">{{ $ticket->participant->event->title ?? 'SeTiket' }}</td>
    </tr>
    <tr>
        <td class="meta-label">Kategori Lari</td>
        <td class="meta-val">{{ $ticket->participant->category }}</td>
    </tr>
    @if($ticket->participant->jersey_size)
    <tr>
        <td class="meta-label">Ukuran Jersey</td>
        <td class="meta-val">{{ $ticket->participant->jersey_size }}</td>
    </tr>
    @endif
    <tr>
        <td class="meta-label">Status Tiket</td>
        <td class="meta-val" style="color: #16a34a;">Valid & Terkonfirmasi</td>
    </tr>
</table>

<!-- QR Code Section -->
<div style="text-align: center; margin: 28px 0; padding: 20px; background-color: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
    <p style="margin: 0 0 12px 0; font-size: 13px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">
        QR Code Check-in Resmi
    </p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($ticket->qr_code ?? $ticket->ticket_code) }}" 
         alt="QR Code Tiket" 
         width="200" 
         height="200" 
         style="display: block; margin: 0 auto; border-radius: 8px; border: 4px solid #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
    <p style="margin: 12px 0 0 0; font-size: 12px; color: #64748b;">
        Tunjukkan QR Code ini pada saat pengambilan Race Pack / Gate Masuk Acara.
    </p>
</div>

<!-- Action CTA Buttons -->
<div style="text-align: center; margin: 32px 0 16px 0;">
    <table role="presentation" align="center" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
        <tr>
            <td style="padding: 0 6px;">
                <a href="{{ route('ticket.show', ['ticket_code' => $ticket->ticket_code]) }}" class="btn-primary" target="_blank">
                    Buka E-Ticket Online &rarr;
                </a>
            </td>
            <td style="padding: 0 6px;">
                <a href="{{ route('ticket.pdf', ['ticket_code' => $ticket->ticket_code]) }}" class="btn-secondary" target="_blank">
                    Unduh PDF Tiket
                </a>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top: 24px; padding: 14px 16px; background-color: #f1f5f9; border-radius: 8px; font-size: 13px; color: #475569; line-height: 1.5;">
    <strong>Tips Peserta:</strong> Simpan email ini atau unduh file PDF tiket Anda di ponsel agar dapat ditunjukkan secara offline saat berada di lokasi acara.
</div>
@endsection
