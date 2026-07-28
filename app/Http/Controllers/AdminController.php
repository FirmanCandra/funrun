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
        if (auth()->user()->role !== 'super_admin') {
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
                    'payment_methods' => $ev['payment_methods'] ?? null
                ]
            );
        }
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $eventId = $user->event_id;
            $totalParticipants = \App\Models\Participant::where('event_id', $eventId)->count();
            $totalRevenue = \App\Models\Payment::whereHas('ticket.participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('payment_status', 'paid')->sum('amount');
            $ticketsSold = \App\Models\Ticket::whereHas('participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('status', 'valid')->count();
            $checkedIn = \App\Models\Ticket::whereHas('participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })->where('status', 'checked-in')->count();
        } else {
            $totalParticipants = \App\Models\Participant::count();
            $totalRevenue = \App\Models\Payment::where('payment_status', 'paid')->sum('amount');
            $ticketsSold = \App\Models\Ticket::where('status', 'valid')->count();
            $checkedIn = \App\Models\Ticket::where('status', 'checked-in')->count();
        }

        return view('admin.dashboard', compact('totalParticipants', 'totalRevenue', 'ticketsSold', 'checkedIn'));
    }

    public function participants(Request $request)
    {
        $query = \App\Models\Participant::with(['ticket.payments', 'user', 'event.admins'])->latest();
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
        return view('admin.participant-edit', compact('participant', 'categories'));
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
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'category' => 'required|in:' . implode(',', $validCategories),
            'jersey_size' => 'required|in:S,M,L,XL,XXL',
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
            $participant->ticket->payments()->delete();
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
                    $participant->ticket->payments()->delete();
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
            'participant' => $ticket->participant->fullname
        ]);
    }

    public function exportCSV(Request $request)
    {
        $query = \App\Models\Participant::with(['user', 'ticket.payments'])->latest();
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

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write column headers
            fputcsv($file, ['ID', 'Nama Lengkap', 'NIK', 'Asal Kota', 'Email', 'No. WhatsApp', 'Kategori', 'Ukuran Jersey', 'Riwayat Penyakit', 'Kode Tiket', 'Status Pembayaran', 'Status Check-in'], ';');

            foreach ($participants as $p) {
                $paymentStatus = 'Pending';
                if ($p->ticket && $p->ticket->payments->count() > 0) {
                    $paymentStatus = $p->ticket->payments->first()->payment_status;
                }

                fputcsv($file, [
                    $p->id,
                    $p->fullname,
                    $p->nik ?? '-',
                    $p->city ?? '-',
                    $p->user->email ?? '-',
                    $p->phone,
                    $p->category,
                    $p->jersey_size,
                    $p->medical_history ?? '-',
                    $p->ticket->ticket_code ?? '-',
                    strtoupper($paymentStatus),
                    strtoupper($p->ticket->status ?? 'pending')
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function payments(Request $request)
    {
        $query = \App\Models\Payment::with(['ticket.participant'])->latest();
        $user = auth()->user();

        if ($user->role === 'admin') {
            $eventId = $user->event_id;
            $query->whereHas('ticket.participant', function($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        $currentStatus = $request->status ?? 'all';
        if ($currentStatus !== 'all') {
            $query->where('payment_status', $currentStatus);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_method', 'like', "%{$search}%")
                  ->orWhere('payment_status', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('ticket', function($qt) use ($search) {
                      $qt->where('ticket_code', 'like', "%{$search}%")
                        ->orWhereHas('participant', function($qp) use ($search) {
                            $qp->where('fullname', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $payments = $query->paginate(10)->withQueryString();

        return view('admin.payments', compact('payments', 'currentStatus'));
    }

    public function bulkDestroyPayment(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Select payments to delete first.');
        }

        $user = auth()->user();
        foreach ($ids as $id) {
            $payment = \App\Models\Payment::find($id);
            if ($payment) {
                if ($user->role === 'admin' && $payment->ticket->participant->event_id !== $user->event_id) {
                    continue; // Skip unauthorized deletion
                }
                if ($payment->ticket) {
                    $payment->ticket->update(['status' => 'pending']);
                }
                $payment->delete();
            }
        }

        return redirect()->back()->with('success', 'Selected payments deleted successfully!');
    }

    public function approvePayment($id)
    {
        $payment = \App\Models\Payment::findOrFail($id);
        $user = auth()->user();
        if ($user->role === 'admin' && $payment->ticket->participant->event_id !== $user->event_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $payment->update(['payment_status' => 'paid']);
        
        if ($payment->ticket) {
            $payment->ticket->update([
                'status' => 'valid',
                'qr_code' => 'QR-' . $payment->ticket->ticket_code . '-' . uniqid()
            ]);

            $participant = $payment->ticket->participant;
            $pdfUrl = route('ticket.pdf', $payment->ticket->ticket_code);
            
            $categoryModel = \App\Models\EventCategory::where('event_id', $participant->event_id)
                ->where('code', $participant->category)
                ->first();
            $ticketTitle = $categoryModel ? $categoryModel->name : $participant->category;

            $waMessage = "*PEMBAYARAN BERHASIL*\n" .
                         "*SeTiket*\n\n" .
                         "Halo *{$participant->fullname}*,\n\n" .
                         "Pembayaran pendaftaran Anda untuk event *SeTiket* telah berhasil diverifikasi!\n\n" .
                         "*Detail Peserta:*\n" .
                         "• Nama Lengkap: *{$participant->fullname}*\n" .
                         "• No. WhatsApp: *{$participant->phone}*\n" .
                         "• Kode Tiket: *{$payment->ticket->ticket_code}*\n" .
                         "• Kategori: *{$ticketTitle}*\n" .
                         "• Ukuran Jersey: *{$participant->jersey_size}*\n\n" .
                         "*Download PDF Resmi E-Ticket:*\n{$pdfUrl}\n\n" .
                         "*Catatan:*\n" .
                         "Silakan simpan link di atas atau unduh PDF tiket Anda. Tunjukkan QR Code pada tiket saat melakukan check-in di lokasi acara untuk pengambilan Race Pack & BIB.\n\n" .
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

        return redirect()->back()->with('success', 'Payment approved! Ticket validated and WA notification queued.');
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

        $pdf = Pdf::loadView('admin.eticket-pdf', compact('ticket', 'qrBase64'))
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
            'payment_name'     => 'required|array|min:1',
            'payment_name.*'   => 'required|string|max:255',
            'payment_number.*' => 'required|string|max:255',
            'payment_holder.*' => 'required|string|max:255',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $maxId  = count($events) > 0 ? max(array_column($events, 'id')) : 0;

        $thumbnailPath = '';
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $thumbnailPath = '/storage/' . $path;
        }

        $paymentMethods = [];
        if ($request->has('payment_name')) {
            foreach ($request->payment_name as $index => $name) {
                if (!empty($name)) {
                    $paymentMethods[] = [
                        'name' => $name,
                        'account_number' => $request->payment_number[$index] ?? '',
                        'account_holder' => $request->payment_holder[$index] ?? '',
                    ];
                }
            }
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
            'payment_methods'  => $paymentMethods,
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
                'payment_methods' => $paymentMethods
            ]
        );

        return redirect()->route('admin.events')->with('success', 'Event berhasil ditambahkan!');
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
            'payment_name'     => 'required|array|min:1',
            'payment_name.*'   => 'required|string|max:255',
            'payment_number.*' => 'required|string|max:255',
            'payment_holder.*' => 'required|string|max:255',
        ]);

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $found = false;

        $paymentMethods = [];
        if ($request->has('payment_name')) {
            foreach ($request->payment_name as $index => $name) {
                if (!empty($name)) {
                    $paymentMethods[] = [
                        'name' => $name,
                        'account_number' => $request->payment_number[$index] ?? '',
                        'account_holder' => $request->payment_holder[$index] ?? '',
                    ];
                }
            }
        }

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
                $ev['payment_methods']  = $paymentMethods;
                
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
                    'payment_methods' => $paymentMethods
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
}
