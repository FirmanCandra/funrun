@extends('emails.layout', [
    'title' => 'Pesanan Diterima: ' . $order->order_code,
    'badgeText' => 'Menunggu Verifikasi',
    'badgeClass' => 'badge-warning'
])

@section('content')
<h1 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Pesanan Tiket Anda Berhasil Dibuat! 🎟️
</h1>

<p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Halo <strong>{{ $order->user->name ?? 'Pelanggan' }}</strong>, terima kasih telah melakukan pemesanan tiket untuk event <strong>{{ $order->event->title ?? 'SeTiket' }}</strong>. Bukti transfer pembayaran Anda telah kami terima dan saat ini sedang dalam antrean verifikasi oleh tim panitia.
</p>

<!-- Meta Card -->
<table class="meta-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="meta-label">Kode Pesanan</td>
        <td class="meta-val" style="font-family: monospace; font-size: 15px; color: #2563eb; letter-spacing: 0.5px;">
            {{ $order->order_code }}
        </td>
    </tr>
    <tr>
        <td class="meta-label">Nama Event</td>
        <td class="meta-val">{{ $order->event->title ?? 'SeTiket' }}</td>
    </tr>
    <tr>
        <td class="meta-label">Jumlah Tiket</td>
        <td class="meta-val">{{ $order->tickets->count() }} Tiket Peserta</td>
    </tr>
    <tr>
        <td class="meta-label">Total Pembayaran</td>
        <td class="meta-val" style="font-size: 16px; color: #0f172a;">
            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td class="meta-label">Metode Pembayaran</td>
        <td class="meta-val">
            Transfer Bank {{ $order->payment_method }}<br>
            <span style="font-size: 13px; color: #64748b;">
                No. Rekening: <strong>{{ $order->payment_account_number }}</strong> (a.n. {{ $order->payment_account_holder }})
            </span>
        </td>
    </tr>
    <tr>
        <td class="meta-label">Status Pesanan</td>
        <td class="meta-val" style="color: #d97706;">
            ⏳ Menunggu Verifikasi Admin
        </td>
    </tr>
</table>

<!-- Info Box -->
<div style="margin: 24px 0; padding: 16px; background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
    <p style="margin: 0 0 6px 0; font-size: 14px; font-weight: 700; color: #92400e;">
        Apa langkah selanjutnya?
    </p>
    <p style="margin: 0; font-size: 13px; color: #78350f; line-height: 1.5;">
        Admin akan memvalidasi bukti pembayaran Anda dalam waktu 1x24 jam. Begitu pembayaran berhasil diverifikasi, E-Ticket resmi beserta QR Code akan langsung dikirimkan ke email ini dan nomor WhatsApp Anda.
    </p>
</div>

<!-- CTA Button -->
<div style="text-align: center; margin: 28px 0 12px 0;">
    <a href="{{ route('orders.show', $order->id) }}" class="btn-primary" target="_blank">
        Lihat Detail Pesanan &rarr;
    </a>
</div>
@endsection
