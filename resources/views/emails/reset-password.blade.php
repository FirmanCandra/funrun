@extends('emails.layout', [
    'title' => 'Permintaan Reset Kata Sandi Akun - SeTiket',
    'badgeText' => 'Keamanan Akun',
    'badgeClass' => 'badge-info'
])

@section('content')
<h1 style="margin: 0 0 12px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Atur Ulang Kata Sandi Akun
</h1>

<p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Halo {{ $user->name ?? 'Pengguna' }},
</p>

<p style="margin: 0 0 20px 0; font-size: 14px; line-height: 1.6; color: #334155;">
    Kami menerima permintaan pengaturan ulang kata sandi untuk akun SeTiket yang terdaftar dengan alamat email <strong>{{ $user->email }}</strong>. Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
</p>

<!-- CTA Button -->
<div style="text-align: center; margin: 28px 0;">
    <a href="{{ $resetUrl }}" class="btn-primary" target="_blank" style="padding: 13px 28px; font-size: 14px; letter-spacing: 0.2px;">
        Atur Ulang Kata Sandi
    </a>
</div>

<!-- Informasi Masa Berlaku -->
<div style="margin: 24px 0; padding: 14px 16px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #475569; line-height: 1.6;">
    <strong style="color: #0f172a;">Catatan Keamanan:</strong>
    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
        <li>Tautan pengaturan ulang kata sandi ini hanya berlaku selama {{ $count ?? 60 }} menit.</li>
        <li>Jika Anda tidak merasa mengajukan permintaan ini, silakan abaikan email ini. Kata sandi akun Anda tetap aman dan tidak mengalami perubahan.</li>
    </ul>
</div>

<!-- Fallback URL -->
<p style="margin: 24px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.6; border-top: 1px solid #e2e8f0; padding-top: 16px;">
    Jika Anda mengalami kendala saat mengklik tombol di atas, salin dan tempel tautan berikut ke peramban web Anda:<br>
    <a href="{{ $resetUrl }}" style="color: #2563eb; word-break: break-all;">{{ $resetUrl }}</a>
</p>
@endsection

