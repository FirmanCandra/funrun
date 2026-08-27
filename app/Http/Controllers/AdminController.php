<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct()
    {
        // Only run sync if database table events exists, to avoid issues during migration
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('events')) {
                self::syncEventsToDatabase();
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    private function checkSuperAdmin()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses halaman ini.');
        }
    }

    public static function parseDateString($dateStr)
    {
        $dateStr = trim($dateStr);
        $dbDate = '2026-09-15'; // Default fallback
        try {
            $months = [
                'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04',
                'Jun' => '06', 'Jul' => '07', 'Aug' => '08', 'Sep' => '09', 'Oct' => '10', 'Nov' => '11', 'Dec' => '12'
            ];
            // If date is range e.g. "25-26 Juli 2026", extract "26 Juli 2026"
            $cleaned = preg_replace('/^\d+\s*-\s*(\d+)/', '$1', $dateStr);
            foreach ($months as $ind => $num) {
                if (stripos($cleaned, $ind) !== false) {
                    $cleaned = str_ireplace($ind, $num, $cleaned);
                    break;
                }
            }
            $timestamp = strtotime($cleaned);
            if ($timestamp) {
                $dbDate = date('Y-m-d', $timestamp);
            }
        } catch (\Throwable $e) {
            // fallback
        }
        return $dbDate;
    }

    public static function syncEventsToDatabase()
    {
        $events = \App\Http\Controllers\HomeController::loadEvents();
        foreach ($events as $ev) {
            \App\Models\Event::updateOrCreate(
                ['id' => $ev['id']],
                [
                    'title' => $ev['nama'] ?? 'SeTiket',
                    'date' => self::parseDateString($ev['tanggal']),
                    'location' => $ev['lokasi'] ?? 'City Square',
                    'quota' => 5000,
                ]
            );
        }
    }

    /**
     * Jumlah pesanan yang menunggu verifikasi, dibatasi pada event yang
     * ditangani admin tersebut. Super admin melihat seluruh event.
     */
    public static function pendingOrdersCount($user): int
    {
        $query = \App\Models\Order::where('payment_status', \App\Models\Order::STATUS_WAITING);

        // Pesanan menyimpan event_id sendiri, jadi penyaringannya langsung.
        if ($user->role === 'admin') {
            $query->where('event_id', $user->event_id);
        }

        return $query->count();
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $eventId = $user->event_id;
            $totalParticipants = \App\Models\Participant::where('event_id', $eventId)->count();
            $totalRevenue = \App\Models\Order::where('event_id', $eventId)
                ->where('payment_status', \App\Models\Order::STATUS_PAID)->sum('total_amount');
            $ticketsSold = \App\Models\Ticket::whereHas('participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('status', 'valid')->count();
            $checkedIn = \App\Models\Ticket::whereHas('participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('status', 'checked-in')->count();
        } else {
            $totalParticipants = \App\Models\Participant::count();
            $totalRevenue = \App\Models\Order::where('payment_status', \App\Models\Order::STATUS_PAID)->sum('total_amount');
            $ticketsSold = \App\Models\Ticket::where('status', 'valid')->count();
            $checkedIn = \App\Models\Ticket::where('status', 'checked-in')->count();
        }

        $pendingOrders = self::pendingOrdersCount($user);

        return view('admin.dashboard', compact('totalParticipants', 'totalRevenue', 'ticketsSold', 'checkedIn', 'pendingOrders'));
    }

    public function participants(Request $request)
    {
        $query = \App\Models\Participant::with(['ticket.order', 'user', 'event.admins'])->latest();
        $user = auth()->user();
 
        if ($user->role === 'admin') {
            $query->where('event_id', $user->event_id);
            $categories = \App\Models\EventCategory::where('event_id', $user->event_id)->get();
        } else {
            $categories = \App\Models\EventCategory::select('code', 'name')->groupBy('code', 'name')->get();
        }

        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['code' => '3K', 'name' => '3K Run'],
                (object) ['code' => '5K', 'name' => '5K Run'],
                (object) ['code' => '10K', 'name' => '10K Run']
            ]);
        }
 
        $currentCategory = $request->category ?? 'all';
        if ($currentCategory !== 'all') {
            $query->where('category', $currentCategory);
        }
 
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%");
                  });
            });
        }
 
        $participants = $query->paginate(10)->withQueryString();
 
        return view('admin.participants', compact('participants', 'currentCategory', 'categories'));
    }
 
    public function editParticipant($id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $participant->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }
        $categories = \App\Models\EventCategory::where('event_id', $participant->event_id)->get();
        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['code' => '3K', 'name' => '3K Run'],
                (object) ['code' => '5K', 'name' => '5K Run'],
                (object) ['code' => '10K', 'name' => '10K Run']
            ]);
        }
        $customFields = \App\Models\EventFormField::where('event_id', $participant->event_id)
            ->where('is_core', false)
            ->orderBy('sort_order')
            ->get();

        return view('admin.participant-edit', compact('participant', 'categories', 'customFields'));
    }
 
    public function updateParticipant(Request $request, $id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $participant->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }
 
        $validCategories = \App\Models\EventCategory::where('event_id', $participant->event_id)->pluck('code')->toArray();
        if (empty($validCategories)) {
            $validCategories = ['3K', '5K', '10K'];
        }

        $request->validate([
            // Semua opsional — admin event boleh mematikan field mana pun di
            // formulir, jadi peserta bisa memang tidak punya data ini.
            'fullname' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'category' => 'nullable|in:' . implode(',', $validCategories),
            'jersey_size' => 'nullable|in:' . implode(',', array_keys(\App\Models\EventFormField::SELECT_OPTIONS['jersey_size'])),
        ]);
 
        $participant->update($request->only('fullname', 'phone', 'category', 'jersey_size'));
 
        return redirect()->route('admin.participants')->with('success', 'Participant data updated successfully!');
    }

    public function deleteParticipant($id)
    {
        $participant = \App\Models\Participant::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $participant->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Manual cascade delete
        if ($participant->ticket) {
            $participant->ticket()->delete();
        }
        $participant->delete();

        return redirect()->back()->with('success', 'Participant deleted successfully!');
    }

    public function bulkDestroyParticipant(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Select participants to delete first.');
        }

        $user = auth()->user();
        foreach ($ids as $id) {
            $participant = \App\Models\Participant::find($id);
            if ($participant) {
                if ($user->role === 'admin' && $participant->event_id !== $user->event_id) {
                    continue; // Skip unauthorized deletion
                }
                if ($participant->ticket) {
                    $participant->ticket()->delete();
                }
                $participant->delete();
            }
        }

        return redirect()->back()->with('success', 'Selected participants deleted successfully!');
    }

    public function scanner()
    {
        return view('admin.scanner');
    }

    public function scanTicket(Request $request)
    {
        $code = $request->qr_code;
        
        // Cek berdasarkan qr_code atau ticket_code (untuk manual entry)
        $ticket = \App\Models\Ticket::where('qr_code', $code)
                                    ->orWhere('ticket_code', $code)
                                    ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found!']);
        }

        $user = auth()->user();
        if ($user->role === 'admin' && $ticket->participant->event_id !== $user->event_id) {
            return response()->json(['success' => false, 'message' => 'Tiket ini terdaftar pada event lain!']);
        }

        if ($ticket->status === 'checked-in') {
            return response()->json(['success' => false, 'message' => 'Ticket already checked in!']);
        }

        if ($ticket->status !== 'valid') {
            return response()->json(['success' => false, 'message' => 'Ticket is not valid or payment pending.']);
        }

        $ticket->update(['status' => 'checked-in']);

        return response()->json([
            'success' => true, 
            'message' => 'Check-in successful!',
            'participant' => $ticket->participant->displayName()
        ]);
    }

    public function exportCSV(Request $request)
    {
        $query = \App\Models\Participant::with(['user', 'ticket.order'])->latest();
        $user = auth()->user();

        if ($user->role === 'admin') {
            $query->where('event_id', $user->event_id);
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%");
                  });
            });
        }

        $participants = $query->get();
        
        $filename = "participants_setiket.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Kolom tambahan diambil dari field custom milik event yang muncul di
        // hasil export. Untuk admin yang terikat satu event, ini persis daftar
        // pertanyaan tambahan event itu.
        $customFields = \App\Models\EventFormField::whereIn('event_id', $participants->pluck('event_id')->unique())
            ->where('is_core', false)
            ->orderBy('event_id')
            ->orderBy('sort_order')
            ->get();

        $callback = function() use ($participants, $customFields) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write column headers
            $header = ['ID', 'Nama Lengkap', 'NIK', 'Asal Kota', 'Email', 'No. WhatsApp', 'Kategori', 'Ukuran T-Shirt', 'Riwayat Penyakit', 'Kode Pesanan', 'Kode Tiket', 'Status Pembayaran', 'Status Check-in'];
            foreach ($customFields as $field) {
                $header[] = $field->label;
            }
            fputcsv($file, $header, ';');

            foreach ($participants as $p) {
                $paymentStatus = 'Pending';
                if ($p->ticket && $p->ticket->order) {
                    $paymentStatus = $p->ticket->order->payment_status;
                }

                $row = [
                    $p->id,
                    $p->displayName(),
                    $p->nik ?? '-',
                    $p->city ?? '-',
                    $p->user->email ?? '-',
                    $p->phone,
                    $p->category,
                    $p->jersey_size,
                    $p->medical_history ?? '-',
                    $p->ticket->order->order_code ?? '-',
                    $p->ticket->ticket_code ?? '-',
                    strtoupper($paymentStatus),
                    strtoupper($p->ticket->status ?? 'pending')
                ];

                foreach ($customFields as $field) {
                    // Peserta dari event lain tidak punya field ini — biarkan kosong.
                    $row[] = $field->event_id === $p->event_id ? $p->customAnswer($field) : '';
                }

                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Antrian verifikasi: satu baris per pesanan, bukan per tiket.
     */
    public function orders(Request $request)
    {
        $query = \App\Models\Order::with(['event', 'user', 'tickets.participant'])
            ->withCount('tickets')
            ->latest();
        $user = auth()->user();

        if ($user->role === 'admin') {
            $query->where('event_id', $user->event_id);
        }

        $currentStatus = $request->status ?? 'all';
        if ($currentStatus !== 'all') {
            $query->where('payment_status', $currentStatus);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('payment_status', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tickets', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%")
                        ->orWhereHas('participant', function($qp) use ($search) {
                            $qp->where('fullname', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%")
                              ->orWhere('nik', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders', compact('orders', 'currentStatus'));
    }

    public function bulkDestroyOrder(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih pesanan yang ingin dihapus terlebih dahulu.');
        }

        $user = auth()->user();
        foreach ($ids as $id) {
            $order = \App\Models\Order::with('tickets.participant')->find($id);
            if (! $order) {
                continue;
            }
            if ($user->role === 'admin' && $order->event_id !== $user->event_id) {
                continue; // Lewati pesanan milik event lain
            }

            // Peserta ikut dihapus supaya NIK-nya bebas dipakai mendaftar lagi.
            foreach ($order->tickets as $ticket) {
                $ticket->participant?->delete();
                $ticket->delete();
            }

            if ($order->proof_of_payment) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($order->proof_of_payment);
            }

            $order->delete();
        }

        return redirect()->back()->with('success', 'Pesanan terpilih berhasil dihapus!');
    }

    /**
     * Setujui satu pesanan: seluruh tiket di dalamnya langsung terbit.
     *
     * Pembeli hanya melakukan satu kali transfer, jadi admin cukup memverifikasi
     * satu kali walaupun pesanannya berisi banyak tiket.
     */
    public function approveOrder($id)
    {
        $order = \App\Models\Order::with('tickets.participant')->findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $order->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }

        $order->update([
            'payment_status' => \App\Models\Order::STATUS_PAID,
            'rejection_reason' => null,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        foreach ($order->tickets as $ticket) {
            $ticket->update([
                'status' => 'valid',
                'qr_code' => 'QR-' . $ticket->ticket_code . '-' . uniqid(),
            ]);

            $this->notifyParticipant($ticket);
        }

        $jumlah = $order->tickets->count();

        return redirect()->back()->with('success', "Pesanan {$order->order_code} disetujui! {$jumlah} tiket diterbitkan dan notifikasi WA dikirim.");
    }

    /**
     * Tolak pesanan — pembeli bisa mengunggah ulang bukti transfernya.
     */
    public function rejectOrder(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $order->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi agar pembeli tahu apa yang harus diperbaiki.',
        ]);

        $order->update([
            'payment_status' => \App\Models\Order::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        // Tiket dikembalikan ke status menunggu.
        $order->tickets()->update(['status' => 'pending', 'qr_code' => null]);

        return redirect()->back()->with('success', "Pesanan {$order->order_code} ditolak. Pembeli dapat mengunggah ulang bukti pembayaran.");
    }

    /**
     * Kirim detail e-ticket ke WhatsApp peserta lewat Fonnte, kalau dikonfigurasi.
     */
    private function notifyParticipant(\App\Models\Ticket $ticket): void
    {
        $participant = $ticket->participant;
        if (! $participant) {
            return;
        }

        $pdfUrl = route('ticket.pdf', $ticket->ticket_code);

        $categoryModel = \App\Models\EventCategory::where('event_id', $participant->event_id)
            ->where('code', $participant->category)
            ->first();
        $ticketTitle = $categoryModel ? $categoryModel->name : $participant->category;

        $waMessage = "*PEMBAYARAN BERHASIL*\n" .
                     "*SeTiket*\n\n" .
                     "Halo *{$participant->displayName()}*,\n\n" .
                     "Pembayaran pendaftaran Anda untuk event *SeTiket* telah berhasil diverifikasi!\n\n" .
                     "*Detail Peserta:*\n" .
                     "• Nama Lengkap: *{$participant->displayName()}*\n" .
                     "• No. WhatsApp: *{$participant->phone}*\n" .
                     "• Kode Tiket: *{$ticket->ticket_code}*\n" .
                     "• Kategori: *{$ticketTitle}*\n" .
                     "• Ukuran T-Shirt: *{$participant->jersey_size}*\n\n" .
                     "*Download PDF Resmi E-Ticket:*\n{$pdfUrl}\n\n" .
                     "*Catatan:*\n" .
                     "Silakan simpan link di atas atau unduh PDF tiket Anda. Tunjukkan QR Code pada tiket saat melakukan check-in di lokasi acara untuk pengambilan Pesanan.\n\n" .
                     "Terima kasih atas partisipasi Anda, sampai jumpa di garis start!";

        // Attempt to send via Fonnte WA API (if configured in .env)
        $fonnteToken = env('FONNTE_TOKEN');
        if ($fonnteToken) {
            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $fonnteToken,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $participant->phone,
                    'message' => $waMessage,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('WA API Error: ' . $e->getMessage());
            }
        } else {
            // Log the message if API is not configured
            \Illuminate\Support\Facades\Log::info("WA Message to {$participant->phone}: \n" . $waMessage);
        }
    }

    // ===== E-TICKET PDF =====

    public function downloadEticket($ticket_code)
    {
        $ticket = \App\Models\Ticket::with(['participant.event'])
            ->where('ticket_code', $ticket_code)
            ->firstOrFail();

        $user = auth()->user();
        if ($user->role === 'admin' && $ticket->participant->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }

        // Load enabled fields for this event so the PDF only shows active fields
        $enabledFields = collect();
        if ($ticket->participant && $ticket->participant->event_id) {
            $enabledFields = \App\Models\EventFormField::activeFor($ticket->participant->event_id);
        }

        // Fetch QR code as base64 so DomPDF can embed it without external HTTP requests
        $qrBase64 = null;
        if ($ticket->qr_code) {
            $qrData = urlencode($ticket->qr_code);
            $qrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$qrData}";
            
            // 1. Try Laravel Http Client
            try {
                $response = \Illuminate\Support\Facades\Http::withOptions([
                    'verify' => false,
                ])->timeout(5)->get($qrUrl);

                if ($response->successful()) {
                    $qrBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('QR fetch via Http client failed, trying fallback: ' . $e->getMessage());
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
                    \Illuminate\Support\Facades\Log::warning('QR fetch via file_get_contents failed: ' . $e->getMessage());
                }
            }
        }

        $pdf = Pdf::loadView('admin.eticket-pdf', compact('ticket', 'qrBase64', 'enabledFields'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('eticket-' . $ticket->ticket_code . '.pdf');
    }

    // ===== EVENT MANAGEMENT =====

    public function events(Request $request)
    {
        $this->checkSuperAdmin();
        $events = \App\Http\Controllers\HomeController::loadEvents();

        // 1. Search
        $search = $request->input('search');
        if (!empty($search)) {
            $events = array_filter($events, function($ev) use ($search) {
                return (isset($ev['nama']) && stripos($ev['nama'], $search) !== false) ||
                       (isset($ev['lokasi']) && stripos($ev['lokasi'], $search) !== false) ||
                       (isset($ev['tanggal']) && stripos($ev['tanggal'], $search) !== false);
            });
            // Re-index array keys
            $events = array_values($events);
        }

        // 2. Pagination
        $perPage = 10;
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedItems = array_slice($events, $offset, $perPage);
        $events = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($events),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.events', compact('events'));
    }

    public function storeEvent(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'nama'             => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal'          => 'required|string|max:100',
            'harga'            => 'required|integer|min:0',
            'kategori'         => 'required|in:upcoming,highlight',
            'urlBeli'          => 'nullable|string|max:500',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:1024',
            'waktu'            => 'nullable|string|max:100',
            'deskripsi'        => 'nullable|string|max:5000',
            'syarat_ketentuan' => 'nullable|string|max:5000',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $maxId  = count($events) > 0 ? max(array_column($events, 'id')) : 0;

        $thumbnailPath = '';
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $thumbnailPath = '/storage/' . $path;
        }

        $newEventId = $maxId + 1;
        $events[] = [
            'id'               => $newEventId,
            'nama'             => $request->nama,
            'lokasi'           => $request->lokasi,
            'tanggal'          => $request->tanggal,
            'harga'            => (int) $request->harga,
            'thumbnail'        => $thumbnailPath,
            'kategori'         => $request->kategori,
            'urlBeli'          => $request->urlBeli ?: 'https://wa.me/6289681201941',
            'waktu'            => $request->waktu ?? '',
            'deskripsi'        => $request->deskripsi ?? '',
            'syarat_ketentuan' => $request->syarat_ketentuan ?? '',
        ];

        \App\Http\Controllers\HomeController::saveEvents($events);
        
        // Database sync
        \App\Models\Event::updateOrCreate(
            ['id' => $newEventId],
            [
                'title' => $request->nama,
                'date' => self::parseDateString($request->tanggal),
                'location' => $request->lokasi,
                'quota' => 5000,
            ]
        );

        return redirect()->route('admin.payment-accounts', ['event_id' => $newEventId])
            ->with('success', 'Event berhasil ditambahkan! Tambahkan rekening tujuan transfer agar peserta bisa membeli tiket.');
    }

    public function updateEvent(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'nama'             => 'required|string|max:255',
            'lokasi'           => 'required|string|max:255',
            'tanggal'          => 'required|string|max:100',
            'harga'            => 'required|integer|min:0',
            'kategori'         => 'required|in:upcoming,highlight',
            'urlBeli'          => 'nullable|string|max:500',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,webp|max:1024',
            'waktu'            => 'nullable|string|max:100',
            'deskripsi'        => 'nullable|string|max:5000',
            'syarat_ketentuan' => 'nullable|string|max:5000',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $found = false;

        foreach ($events as &$ev) {
            if ($ev['id'] == $id) {
                $ev['nama']             = $request->nama;
                $ev['lokasi']           = $request->lokasi;
                $ev['tanggal']          = $request->tanggal;
                $ev['harga']            = (int) $request->harga;
                $ev['kategori']         = $request->kategori;
                $ev['urlBeli']          = $request->urlBeli ?: 'https://wa.me/6289681201941';
                $ev['waktu']            = $request->waktu ?? '';
                $ev['deskripsi']        = $request->deskripsi ?? '';
                $ev['syarat_ketentuan'] = $request->syarat_ketentuan ?? '';
                
                if ($request->hasFile('thumbnail')) {
                    $path = $request->file('thumbnail')->store('thumbnails', 'public');
                    $ev['thumbnail'] = '/storage/' . $path;
                }
                $found = true;
                break;
            }
        }
        unset($ev);

        if ($found) {
            \App\Http\Controllers\HomeController::saveEvents($events);
            
            // Database sync
            \App\Models\Event::updateOrCreate(
                ['id' => $id],
                [
                    'title' => $request->nama,
                    'date' => self::parseDateString($request->tanggal),
                    'location' => $request->lokasi,
                    'quota' => 5000,
                ]
            );
        }

        return redirect()->route('admin.events')->with('success', 'Event berhasil diperbarui!');
    }

    public function destroyEvent($id)
    {
        $this->checkSuperAdmin();
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $events = array_values(array_filter($events, fn($e) => $e['id'] != $id));
        \App\Http\Controllers\HomeController::saveEvents($events);

        // Delete from database
        $dbEvent = \App\Models\Event::find($id);
        if ($dbEvent) {
            $dbEvent->delete();
        }

        return redirect()->route('admin.events')->with('success', 'Event berhasil dihapus!');
    }

    public function bulkDestroyEvent(Request $request)
    {
        $this->checkSuperAdmin();
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih event yang ingin dihapus terlebih dahulu.');
        }

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $events = array_values(array_filter($events, fn($e) => !in_array($e['id'], $ids)));
        \App\Http\Controllers\HomeController::saveEvents($events);

        // Delete from database
        \App\Models\Event::whereIn('id', $ids)->delete();

        return redirect()->route('admin.events')->with('success', 'Event terpilih berhasil dihapus!');
    }

    // ===== GAMBAR / THUMBNAIL EVENT =====

    /**
     * Halaman pengaturan gambar event.
     *
     * Beda dengan Manajemen Event yang dikunci super admin: gambar adalah materi
     * promosi yang dipegang penyelenggara, jadi admin event boleh mengurus
     * gambar event yang dia tangani sendiri tanpa menunggu super admin.
     */
    public function eventImage(Request $request)
    {
        $event = $this->resolveFormEvent($request);
        $thumbnail = $this->eventThumbnail($event->id);

        $events = auth()->user()->isSuperAdmin()
            ? \App\Models\Event::orderBy('id')->get()
            : collect([$event]);

        return view('admin.event-image', compact('event', 'thumbnail', 'events'));
    }

    public function updateEventImage(Request $request)
    {
        $event = $this->resolveFormEvent($request);

        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,webp|max:1024',
        ], [
            'thumbnail.required' => 'Pilih dulu gambar yang mau diunggah.',
            'thumbnail.image' => 'Berkas yang diunggah harus berupa gambar.',
            'thumbnail.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'thumbnail.max' => 'Ukuran gambar maksimal 1 MB.',
        ]);

        $lama = $this->eventThumbnail($event->id);
        $path = '/storage/' . $request->file('thumbnail')->store('thumbnails', 'public');

        $this->saveEventThumbnail($event->id, $path);
        $this->deleteThumbnailFile($lama);

        return redirect()->route('admin.event-image', ['event_id' => $event->id])
            ->with('success', 'Gambar event berhasil diperbarui. Peserta langsung melihat gambar barunya di halaman event.');
    }

    public function destroyEventImage(Request $request)
    {
        $event = $this->resolveFormEvent($request);

        $lama = $this->eventThumbnail($event->id);
        $this->saveEventThumbnail($event->id, '');
        $this->deleteThumbnailFile($lama);

        return redirect()->route('admin.event-image', ['event_id' => $event->id])
            ->with('success', 'Gambar event dihapus. Kartu event kembali memakai tampilan cadangan berisi inisial nama event.');
    }

    /**
     * Thumbnail event dari events.json — file itu yang dibaca halaman publik.
     */
    private function eventThumbnail($eventId): string
    {
        foreach (\App\Http\Controllers\HomeController::loadEvents() as $ev) {
            if ($ev['id'] == $eventId) {
                return $ev['thumbnail'] ?? '';
            }
        }

        return '';
    }

    private function saveEventThumbnail($eventId, string $path): void
    {
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $ketemu = false;

        foreach ($events as &$ev) {
            if ($ev['id'] == $eventId) {
                $ev['thumbnail'] = $path;
                $ketemu = true;
                break;
            }
        }
        unset($ev);

        abort_if(! $ketemu, 404, 'Event ini belum terdaftar di halaman publik, jadi gambarnya belum bisa diatur.');

        \App\Http\Controllers\HomeController::saveEvents($events);
    }

    /**
     * Buang berkas gambar yang sudah tidak dipakai event mana pun.
     *
     * Pengecekan referensi penting karena dua event bisa saja menunjuk berkas
     * yang sama — menghapusnya tanpa cek akan mengosongkan gambar event lain.
     */
    private function deleteThumbnailFile(string $path): void
    {
        if ($path === '' || ! str_starts_with($path, '/storage/thumbnails/')) {
            return;
        }

        foreach (\App\Http\Controllers\HomeController::loadEvents() as $ev) {
            if (($ev['thumbnail'] ?? '') === $path) {
                return;
            }
        }

        \Illuminate\Support\Facades\Storage::disk('public')
            ->delete(substr($path, strlen('/storage/')));
    }

    // ===== ADMIN MANAGEMENT =====

    public function admins(Request $request)
    {
        $this->checkSuperAdmin();
        
        $query = \App\Models\User::with('event')->where('role', 'admin')->latest();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->paginate(10)->withQueryString();
        
        // Load events for the selection dropdown
        $events = \App\Models\Event::all();
        
        return view('admin.admins', compact('admins', 'events'));
    }

    public function bulkDestroyAdmin(Request $request)
    {
        $this->checkSuperAdmin();
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih admin yang ingin dihapus terlebih dahulu.');
        }

        \App\Models\User::whereIn('id', $ids)->where('role', 'admin')->delete();

        return redirect()->route('admin.admins')->with('success', 'Admin terpilih berhasil dihapus!');
    }

    public function storeAdmin(Request $request)
    {
        $this->checkSuperAdmin();
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'event_id' => 'required|exists:events,id',
        ]);

        \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'plain_password' => $request->password,
            'role'     => 'admin',
            'event_id' => $request->event_id,
        ]);

        return redirect()->route('admin.admins')->with('success', 'Admin berhasil ditambahkan!');
    }

    public function updateAdmin(Request $request, $id)
    {
        $this->checkSuperAdmin();
        $admin = \App\Models\User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'event_id' => 'required|exists:events,id',
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'event_id' => $request->event_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        $admin->update($data);

        return redirect()->route('admin.admins')->with('success', 'Admin berhasil diperbarui!');
    }

    public function destroyAdmin($id)
    {
        $this->checkSuperAdmin();
        $admin = \App\Models\User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.admins')->with('success', 'Admin berhasil dihapus!');
    }

    // ===== EVENT CATEGORY MANAGEMENT =====

    public function eventCategories($id)
    {
        $this->checkSuperAdmin();
        
        // Ensure event exists in DB, if not firstOrCreate from JSON fallback
        $dbEvent = \App\Models\Event::find($id);
        if (!$dbEvent) {
            $events = \App\Http\Controllers\HomeController::loadEvents();
            $jsonEvent = collect($events)->firstWhere('id', (int)$id);
            if ($jsonEvent) {
                $dbEvent = \App\Models\Event::create([
                    'id' => $id,
                    'title' => $jsonEvent['nama'] ?? 'SeTiket',
                    'date' => self::parseDateString($jsonEvent['tanggal'] ?? ''),
                    'location' => $jsonEvent['lokasi'] ?? 'City Square',
                    'quota' => 5000
                ]);
            } else {
                abort(404, 'Event tidak ditemukan.');
            }
        }

        // Seed default categories if none exist yet
        if ($dbEvent->categories()->count() === 0) {
            $dbEvent->categories()->createMany([
                ['name' => '3K Fun Walk', 'code' => '3K', 'bib_code' => 'FW', 'price' => 100000],
                ['name' => '5K Night Run', 'code' => '5K', 'bib_code' => 'NR', 'price' => 150000],
                ['name' => '10K Challenger', 'code' => '10K', 'bib_code' => 'CH', 'price' => 250000],
            ]);
        }

        $categories = $dbEvent->categories;
        return view('admin.categories', compact('dbEvent', 'categories'));
    }

    public function storeEventCategory(Request $request, $id)
    {
        $this->checkSuperAdmin();
        
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:50',
            'bib_code' => 'required|string|max:50',
            'price'    => 'required|integer|min:0',
        ]);

        \App\Models\EventCategory::create([
            'event_id' => $id,
            'name'     => $request->name,
            'code'     => $request->code,
            'bib_code' => $request->bib_code,
            'price'    => $request->price,
        ]);

        return redirect()->route('admin.events.categories', $id)->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function updateEventCategory(Request $request, $category_id)
    {
        $this->checkSuperAdmin();
        
        $category = \App\Models\EventCategory::findOrFail($category_id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:50',
            'bib_code' => 'required|string|max:50',
            'price'    => 'required|integer|min:0',
        ]);

        $category->update([
            'name'     => $request->name,
            'code'     => $request->code,
            'bib_code' => $request->bib_code,
            'price'    => $request->price,
        ]);

        return redirect()->route('admin.events.categories', $category->event_id)->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyEventCategory($category_id)
    {
        $this->checkSuperAdmin();
        
        $category = \App\Models\EventCategory::findOrFail($category_id);
        $eventId = $category->event_id;
        $category->delete();

        return redirect()->route('admin.events.categories', $eventId)->with('success', 'Kategori berhasil dihapus!');
    }

    // ===== REKENING TUJUAN TRANSFER PER EVENT =====

    /**
     * Daftar rekening sebuah event.
     *
     * Admin event boleh melihat rekening event yang dia tangani — dia yang
     * mencocokkan bukti transfer masuk. Yang boleh mengubahnya hanya super admin.
     */
    public function paymentAccounts(Request $request)
    {
        $event = $this->resolveFormEvent($request);

        $accounts = $event->paymentAccounts()->get();

        $events = auth()->user()->isSuperAdmin()
            ? \App\Models\Event::orderBy('id')->get()
            : collect([$event]);

        return view('admin.payment-accounts', compact('event', 'accounts', 'events'));
    }

    public function storePaymentAccount(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:64',
            'account_holder' => 'required|string|max:255',
        ], $this->paymentAccountMessages());

        $event = \App\Models\Event::findOrFail($request->event_id);

        \App\Models\EventPaymentAccount::create([
            'event_id' => $event->id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'is_active' => true,
            'sort_order' => (int) $event->paymentAccounts()->max('sort_order') + 10,
        ]);

        return redirect()->route('admin.payment-accounts', ['event_id' => $event->id])
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function updatePaymentAccount(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $account = \App\Models\EventPaymentAccount::findOrFail($id);

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:64',
            'account_holder' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], $this->paymentAccountMessages());

        $account->update([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : $account->sort_order,
        ]);

        return redirect()->route('admin.payment-accounts', ['event_id' => $account->event_id])
            ->with('success', 'Rekening berhasil diperbarui. Pesanan lama tetap menyimpan nomor rekening yang berlaku saat itu.');
    }

    public function destroyPaymentAccount($id)
    {
        $this->checkSuperAdmin();

        $account = \App\Models\EventPaymentAccount::findOrFail($id);
        $eventId = $account->event_id;

        // Pesanan yang terlanjur memakai rekening ini tidak ikut terhapus —
        // kolom snapshot pada pesanan sudah menyimpan nomornya.
        $account->delete();

        return redirect()->route('admin.payment-accounts', ['event_id' => $eventId])
            ->with('success', 'Rekening dihapus. Pesanan yang sudah memakainya tetap menyimpan nomor rekening lamanya.');
    }

    private function paymentAccountMessages(): array
    {
        return [
            'bank_name.required' => 'Nama bank / e-wallet wajib diisi.',
            'account_number.required' => 'Nomor rekening / nomor HP wajib diisi.',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi.',
        ];
    }

    // ===== FORMULIR PENDAFTARAN PER EVENT =====

    /**
     * Event yang boleh diatur formulirnya oleh user yang sedang login.
     *
     * Admin hanya boleh event yang ditugaskan kepadanya; super admin bebas
     * memilih event mana pun lewat ?event_id=.
     */
    private function resolveFormEvent(Request $request): \App\Models\Event
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            if (! $user->event_id) {
                abort(403, 'Akun admin ini belum ditugaskan ke event mana pun. Hubungi Super Admin.');
            }

            return \App\Models\Event::findOrFail($user->event_id);
        }

        $eventId = $request->input('event_id') ?? \App\Models\Event::orderBy('id')->value('id');

        abort_if(! $eventId, 404, 'Belum ada event yang bisa diatur.');

        return \App\Models\Event::findOrFail($eventId);
    }

    /**
     * Pastikan field yang disentuh benar-benar milik event yang boleh diakses.
     */
    private function authorizeFieldEvent(\App\Models\EventFormField $field): void
    {
        $user = auth()->user();

        if ($user->role === 'admin' && $field->event_id !== $user->event_id) {
            abort(403, 'Field ini milik event lain.');
        }
    }

    public function formFields(Request $request)
    {
        $event = $this->resolveFormEvent($request);

        \App\Models\EventFormField::ensureCoreFields($event->id);

        $coreFields = $event->formFields()->where('is_core', true)->get();
        $customFields = $event->formFields()->where('is_core', false)->get();

        // Dropdown pemilih event, hanya relevan untuk super admin.
        $events = auth()->user()->isSuperAdmin()
            ? \App\Models\Event::orderBy('id')->get()
            : collect([$event]);

        return view('admin.form-fields', compact('event', 'coreFields', 'customFields', 'events'));
    }

    public function storeFormField(Request $request)
    {
        $event = $this->resolveFormEvent($request);

        $request->validate([
            'label' => 'required|string|max:255',
            'type' => ['required', \Illuminate\Validation\Rule::in(array_keys(\App\Models\EventFormField::CUSTOM_TYPES))],
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:255',
            'required' => 'nullable|boolean',
        ], [
            'label.required' => 'Nama pertanyaan wajib diisi.',
            'type.in' => 'Tipe field tidak dikenal.',
        ]);

        \App\Models\EventFormField::create([
            'event_id' => $event->id,
            'key' => \App\Models\EventFormField::makeKey($event->id, $request->label),
            'label' => $request->label,
            'type' => $request->type,
            'is_core' => false,
            'enabled' => true,
            'required' => $request->boolean('required'),
            'placeholder' => $request->placeholder,
            'help_text' => $request->help_text,
            'sort_order' => (int) $event->formFields()->max('sort_order') + 10,
        ]);

        return redirect()->route('admin.form-fields', ['event_id' => $event->id])
            ->with('success', 'Field "'.$request->label.'" berhasil ditambahkan ke formulir.');
    }

    public function updateFormField(Request $request, $id)
    {
        $field = \App\Models\EventFormField::findOrFail($id);
        $this->authorizeFieldEvent($field);

        $request->validate([
            'label' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'label' => $request->label,
            'placeholder' => $request->placeholder,
            'help_text' => $request->help_text,
            'required' => $request->boolean('required'),
            'enabled' => $request->boolean('enabled'),
        ];

        if ($request->filled('sort_order')) {
            $data['sort_order'] = (int) $request->sort_order;
        }

        // Tipe field bawaan tidak boleh diubah — kolomnya sudah tetap di
        // tabel participants.
        if (! $field->is_core && $request->filled('type')) {
            $request->validate([
                'type' => \Illuminate\Validation\Rule::in(array_keys(\App\Models\EventFormField::CUSTOM_TYPES)),
            ]);
            $data['type'] = $request->type;
        }

        $field->update($data);

        return redirect()->route('admin.form-fields', ['event_id' => $field->event_id])
            ->with('success', 'Field "'.$field->label.'" berhasil diperbarui.');
    }

    public function destroyFormField($id)
    {
        $field = \App\Models\EventFormField::findOrFail($id);
        $this->authorizeFieldEvent($field);

        if ($field->is_core) {
            return redirect()->back()->with('error', 'Field bawaan tidak bisa dihapus. Nonaktifkan saja kalau tidak dibutuhkan.');
        }

        $eventId = $field->event_id;
        $label = $field->label;
        $field->delete();

        return redirect()->route('admin.form-fields', ['event_id' => $eventId])
            ->with('success', 'Field "'.$label.'" dihapus. Jawaban peserta yang sudah terlanjur masuk tetap tersimpan.');
    }
}
