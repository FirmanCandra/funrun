<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EventFormField;

class TicketController extends Controller
{
    public function showTicket($ticket_code)
    {
        $ticket = \App\Models\Ticket::with(['participant.event'])->where('ticket_code', $ticket_code)->firstOrFail();

        // Load enabled fields for this event so the ticket view only shows active fields
        $enabledFields = collect();
        if ($ticket->participant && $ticket->participant->event_id) {
            $enabledFields = EventFormField::activeFor($ticket->participant->event_id);
        }

        return view('ticket', compact('ticket', 'enabledFields'));
    }

    public function downloadPdf($ticket_code)
    {
        $ticket = \App\Models\Ticket::with(['participant.event'])
            ->where('ticket_code', $ticket_code)
            ->firstOrFail();

        // Load enabled fields for this event so the PDF only shows active fields
        $enabledFields = collect();
        if ($ticket->participant && $ticket->participant->event_id) {
            $enabledFields = EventFormField::activeFor($ticket->participant->event_id);
        }

        // Fetch QR code as base64 so DomPDF can embed it without external HTTP requests
        $qrBase64 = null;
        if ($ticket->qr_code) {
            $qrData = urlencode($ticket->qr_code);
            $qrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$qrData}";
            
            // 1. Try Laravel Http Client (uses cURL)
            try {
                $response = \Illuminate\Support\Facades\Http::withOptions([
                    'verify' => false,
                ])->timeout(5)->get($qrUrl);

                if ($response->successful()) {
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Public QR fetch via Http client failed, trying fallback: ' . $e->getMessage());
            }

            // 2. Fallback to file_get_contents
            if (empty($qrBase64)) {
                try {
                    $context = stream_context_create([
                        'http' => [
                            'timeout'     => 5,
                            'user_agent'  => 'Mozilla/5.0',
                        ],
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                        ],
                    ]);
                    $imageData = @file_get_contents($qrUrl, false, $context);
                    if ($imageData !== false) {
                        $qrBase64 = 'data:image/png;base64,' . base64_encode($imageData);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Public QR fetch via file_get_contents failed: ' . $e->getMessage());
                }
            }
        }

        $pdf = Pdf::loadView('admin.eticket-pdf', compact('ticket', 'qrBase64', 'enabledFields'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('eticket-' . $ticket->ticket_code . '.pdf');
    }
}
