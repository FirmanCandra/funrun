<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>E-Ticket {{ $ticket->ticket_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
        }

        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header */
        .ticket-header {
            background: linear-gradient(135deg, #1e40af, #7c3aed);
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }

        .ticket-header h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .ticket-header p {
            font-size: 12px;
            opacity: 0.85;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            padding: 24px 30px;
            border-bottom: 2px dashed #cbd5e1;
            background: #f8fafc;
        }

        .qr-section img {
            width: 180px;
            height: 180px;
            display: inline-block;
            border: 4px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .ticket-code {
            margin-top: 12px;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #1e40af;
        }

        .ticket-status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-valid {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-checkedin {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Participant Data */
        .data-section {
            padding: 24px 30px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .data-grid td {
            padding: 8px 0;
            vertical-align: top;
        }

        .data-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 40%;
        }

        .data-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .data-grid tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .data-grid tr:last-child {
            border-bottom: none;
        }

        /* Category Badge */
        .category-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: #eff6ff;
            color: #1e40af;
        }

        /* Check-in Section */
        .checkin-section {
            padding: 20px 30px;
            background: #f1f5f9;
            border-top: 2px dashed #cbd5e1;
        }

        .checkin-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #475569;
            margin-bottom: 10px;
        }

        .checkin-info {
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }

        .checkin-info strong {
            color: #334155;
        }

        /* Footer */
        .ticket-footer {
            padding: 16px 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="ticket-container">
        {{-- Header --}}
        <div class="ticket-header">
            <h1>SeTiket</h1>
            <p>{{ $ticket->participant->event->title ?? 'SeTiket' }} — E-Ticket</p>
        </div>

        {{-- QR Code Section --}}
        <div class="qr-section">
            @if(!empty($qrBase64))
                <img src="{{ $qrBase64 }}" alt="QR Code">
            @elseif($ticket->qr_code)
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($ticket->qr_code) }}"
                    alt="QR Code">
            @else
                <div
                    style="width:180px;height:180px;display:inline-block;background:#f1f5f9;border:2px dashed #cbd5e1;border-radius:8px;line-height:180px;color:#94a3b8;font-size:12px;">
                    QR Belum Tersedia
                </div>
            @endif
            <div class="ticket-code">{{ $ticket->ticket_code }}</div>
            <div>
                @if($ticket->status === 'valid')
                    <span class="ticket-status status-valid">VALID</span>
                @elseif($ticket->status === 'checked-in')
                    <span class="ticket-status status-checkedin">CHECKED-IN</span>
                @else
                    <span class="ticket-status status-pending">PENDING</span>
                @endif
            </div>
        </div>

        {{-- Participant Data --}}
        <div class="data-section">
            <div class="section-title">Data Peserta</div>
            <table class="data-grid">
                <tr>
                    <td class="data-label">Nama Lengkap</td>
                    <td class="data-value">{{ $ticket->participant->fullname }}</td>
                </tr>
                <tr>
                    <td class="data-label">NIK</td>
                    <td class="data-value">{{ $ticket->participant->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="data-label">No. WhatsApp</td>
                    <td class="data-value">{{ $ticket->participant->phone }}</td>
                </tr>
                <tr>
                    <td class="data-label">Asal Kota/Kabupaten</td>
                    <td class="data-value">{{ $ticket->participant->city ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="data-label">Tanggal Lahir</td>
                    <td class="data-value">
                        {{ $ticket->participant->dob ? \Carbon\Carbon::parse($ticket->participant->dob)->format('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="data-label">Jenis Kelamin</td>
                    <td class="data-value">{{ $ticket->participant->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                </tr>
                <tr>
                    <td class="data-label">Kategori</td>
                    <td class="data-value"><span class="category-badge">{{ $ticket->participant->category }}</span></td>
                </tr>
                <tr>
                    <td class="data-label">Ukuran Jersey</td>
                    <td class="data-value">{{ $ticket->participant->jersey_size }}</td>
                </tr>
                <tr>
                    <td class="data-label">Kontak Darurat</td>
                    <td class="data-value">{{ $ticket->participant->emergency_contact ?? '-' }}</td>
                </tr>
                @if($ticket->participant->medical_history)
                    <tr>
                        <td class="data-label">Riwayat Penyakit</td>
                        <td class="data-value" style="color: #dc2626;">{{ $ticket->participant->medical_history }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Check-in Info --}}
        <div class="checkin-section">
            <div class="checkin-title">Informasi Check-In</div>
            <div class="checkin-info">
                <strong>Untuk Peserta:</strong> Tunjukkan E-Ticket ini (cetak/digital) di meja registrasi event untuk
                mengambil Race Pack dan nomor BIB Anda.
            </div>
        </div>

        {{-- Footer --}}
        <div class="ticket-footer">
            E-Ticket ini dicetak pada {{ now()->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB
        </div>
    </div>
</body>

</html>