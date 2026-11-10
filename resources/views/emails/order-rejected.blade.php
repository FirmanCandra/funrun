@extends('emails.layout', [
    'title' => 'Verifikasi Pembayaran Perlu Diperbaiki: ' . $order->order_code,
    'badgeText' => 'Perlu Perbaikan',
    'badgeClass' => 'badge-danger'
])

@section('content')
<h1 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Verifikasi Pembayaran Anda Belum Dapat Disetujui
</h1>

<p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Halo <strong>{{ $order->user->name ?? 'Pelanggan' }}</strong>, panitia event <strong>{{ $order->event->title ?? 'SeTiket' }}</strong> telah meninjau bukti pembayaran yang Anda kirimkan untuk pesanan <strong>{{ $order->order_code }}</strong>.
</p>

<!-- Alasan Penolakan -->
<div style="margin: 20px 0; padding: 18px 20px; background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px;">
    <p style="margin: 0 0 6px 0; font-size: 13px; font-weight: 700; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px;">
        Alasan dari Panitia:
    </p>
    <p style="margin: 0; font-size: 14px; font-weight: 600; color: #b91c1c; line-height: 1.5;">
        "{{ $reason }}"
    </p>
</div>

<!-- Meta Card -->
<table class="meta-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="meta-label">Kode Pesanan</td>
        <td class="meta-val" style="font-family: monospace; font-size: 15px; color: #2563eb;">
            {{ $order->order_code }}
        </td>
    </tr>
    <tr>
        <td class="meta-label">Total Tagihan</td>
        <td class="meta-val">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="meta-label">Rekening Tujuan</td>
        <td class="meta-val">{{ $order->payment_method }} - {{ $order->payment_account_number }} (a.n. {{ $order->payment_account_holder }})</td>
    </tr>
</table>

<p style="margin: 24px 0 16px 0; font-size: 14px; line-height: 1.6; color: #475569;">
    Jangan khawatir, tiket Anda tidak hangus! Anda dapat langsung mengunggah ulang foto atau screenshot bukti transfer yang valid melalui link di bawah ini:
</p>

<!-- CTA Button -->
<div style="text-align: center; margin: 28px 0 12px 0;">
    <a href="{{ route('orders.show', $order->id) }}" class="btn-primary" style="background-color: #dc2626;" target="_blank">
        Unggah Ulang Bukti Transfer &rarr;
    </a>
</div>
@endsection
