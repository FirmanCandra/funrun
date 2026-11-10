@extends('emails.layout', [
    'title' => 'Akun Admin Event Dibuat - SeTiket',
    'badgeText' => 'Akses Admin',
    'badgeClass' => 'badge-info'
])

@section('content')
<h1 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
    Halo, {{ $user->name }}! 🛡️
</h1>

<p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #334155;">
    Akun administrator Anda di platform <strong>SeTiket</strong> telah berhasil didaftarkan oleh Super Admin. Anda telah ditugaskan untuk mengelola event <strong>{{ $eventTitle }}</strong>.
</p>

<!-- Kredensial Login -->
<div style="margin: 20px 0; padding: 18px 20px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
    <p style="margin: 0 0 10px 0; font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">
        Kredensial Login Administrator:
    </p>
    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px;">
        <tr>
            <td style="padding: 6px 0; color: #64748b; width: 35%;">Alamat Email:</td>
            <td style="padding: 6px 0; font-weight: 700; color: #0f172a; font-family: monospace;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Password Awal:</td>
            <td style="padding: 6px 0; font-weight: 700; color: #2563eb; font-family: monospace; font-size: 15px;">{{ $plainPassword }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; color: #64748b;">Event Yang Dikelola:</td>
            <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $eventTitle }}</td>
        </tr>
    </table>
</div>

<div style="margin: 16px 0 24px 0; padding: 12px 16px; background-color: #fef3c7; border-radius: 8px; font-size: 13px; color: #92400e; line-height: 1.5;">
    ⚠️ <strong>Penting:</strong> Demi keamanan akun, segera masuk dan perbarui password Anda di menu Profil setelah berhasil login pertama kali.
</div>

<!-- CTA Button -->
<div style="text-align: center; margin: 28px 0 12px 0;">
    <a href="{{ route('login') }}" class="btn-primary" target="_blank">
        Masuk ke Portal Admin &rarr;
    </a>
</div>
@endsection
