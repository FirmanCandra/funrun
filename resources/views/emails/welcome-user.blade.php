@extends('emails.layout', [
    'title' => 'Selamat Datang di SeTiket',
    'badgeText' => 'Akun Aktif',
    'badgeClass' => 'badge-success'
])

@section('content')
<h1 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Selamat Datang di SeTiket, {{ $user->name }}! 🏃‍♂️💨
</h1>

<p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Akun Anda telah berhasil terdaftar. Sekarang Anda dapat dengan mudah membeli tiket event lari, fun run, marathon, dan event seru lainnya di seluruh Indonesia melalui <strong>SeTiket</strong>.
</p>

<!-- Meta Card -->
<table class="meta-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="meta-label">Nama Akun</td>
        <td class="meta-val">{{ $user->name }}</td>
    </tr>
    <tr>
        <td class="meta-label">Email Terdaftar</td>
        <td class="meta-val" style="font-family: monospace;">{{ $user->email }}</td>
    </tr>
    <tr>
        <td class="meta-label">Status Akun</td>
        <td class="meta-val" style="color: #16a34a;">Aktif & Siap Digunakan</td>
    </tr>
</table>

<p style="margin: 20px 0 16px 0; font-size: 14px; line-height: 1.6; color: #475569;">
    Semua tiket yang Anda beli, histori pembayaran, dan e-ticket dengan QR Code check-in akan tersimpan dengan rapi di Dashboard akun Anda.
</p>

<!-- CTA Button -->
<div style="text-align: center; margin: 28px 0 12px 0;">
    <a href="{{ url('/') }}" class="btn-primary" target="_blank">
        Jelajahi Event & Tiket &rarr;
    </a>
</div>
@endsection
