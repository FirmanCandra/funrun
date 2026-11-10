<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'Notifikasi SeTiket' }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 24px 12px; }
        .main-card { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 24px 32px; text-align: left; }
        .brand { font-size: 22px; font-weight: 800; color: #ffffff; text-decoration: none; letter-spacing: -0.5px; }
        .brand-dot { color: #38bdf8; }
        .header-sub { font-size: 13px; color: #94a3b8; margin-top: 4px; }
        .content { padding: 32px; }
        .btn-primary { display: inline-block; background-color: #2563eb; color: #ffffff !important; font-size: 14px; font-weight: 700; text-decoration: none; padding: 12px 24px; border-radius: 8px; text-align: center; }
        .btn-secondary { display: inline-block; background-color: #f8fafc; color: #334155 !important; font-size: 14px; font-weight: 600; text-decoration: none; padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; text-align: center; }
        .footer { padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; line-height: 1.6; text-align: center; }
        .footer a { color: #2563eb; text-decoration: none; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #e0f2fe; color: #075985; }
        .meta-table { width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .meta-table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .meta-table tr:last-child td { border-bottom: none; }
        .meta-label { color: #64748b; width: 38%; font-weight: 500; }
        .meta-val { color: #0f172a; font-weight: 600; }
    </style>
</head>
<body>
    <table role="presentation" class="wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" class="main-card" width="100%" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <a href="https://setiket.id" class="brand">
                                            SeTiket<span class="brand-dot">.id</span>
                                        </a>
                                        <div class="header-sub">Platform Resmi Tiket Event & Olahraga</div>
                                    </td>
                                    @if(isset($badgeText))
                                    <td align="right">
                                        <span class="badge {{ $badgeClass ?? 'badge-info' }}">{{ $badgeText }}</span>
                                    </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td class="content">
                            @yield('content')
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p style="margin: 0 0 8px 0; font-weight: 600; color: #475569;">
                                Butuh bantuan seputar pesanan atau event?
                            </p>
                            <p style="margin: 0 0 12px 0;">
                                Hubungi kami di <a href="mailto:info@setiket.id">info@setiket.id</a> atau kunjungi <a href="https://setiket.id">setiket.id</a>
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                &copy; {{ date('Y') }} SeTiket. Hak Cipta Dilindungi. Email ini dikirim otomatis oleh sistem resmi SeTiket.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
