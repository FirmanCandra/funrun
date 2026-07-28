<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #ffffff; padding: 30px; border-radius: 8px; max-w-lg; margin: 0 auto; border-top: 5px solid #3b82f6; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; line-height: 1.6; }
        .qr-code { text-align: center; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #888; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 Payment Verified! 🎉</h2>
            <p>Your registration for <strong>{{ $ticket->participant->event->title ?? 'SeTiket' }}</strong> is officially confirmed.</p>
        </div>
        
        <div class="details">
            <p>Hi <strong>{{ $ticket->participant->fullname }}</strong>,</p>
            <p>Thank you for your payment. Here are your E-Ticket details:</p>
            <ul>
                <li><strong>Ticket Code:</strong> {{ $ticket->ticket_code }}</li>
                <li><strong>Category:</strong> {{ $ticket->participant->category }}</li>
                <li><strong>Jersey Size:</strong> {{ $ticket->participant->jersey_size }}</li>
            </ul>
        </div>
        
        <div class="qr-code">
            <p>Please present this QR Code during the race pack collection & check-in:</p>
            <!-- Render QR Code using external API for the email -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($ticket->qr_code) }}" alt="QR Code">
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('ticket.show', ['ticket_code' => $ticket->ticket_code]) }}" class="btn">View Live E-Ticket</a>
        </div>

        <div class="footer">
            <p>&copy; 2026 SeTiket. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
