<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $eventId = $request->query('event_id', 1);
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $event = collect($events)->firstWhere('id', (int)$eventId);
 
        if (!$event) {
            // Fallback to first event or default
            $event = count($events) > 0 ? $events[0] : [
                'id' => 1,
                'nama' => 'SeTiket',
                'lokasi' => 'City Square',
                'tanggal' => '25-26 Juli 2026',
                'harga' => 100000,
                'kategori' => 'upcoming',
                'waktu' => '16.00 - 23.00'
            ];
        }
 
        // Apply fallbacks
        if (empty($event['waktu'])) {
            $event['waktu'] = '16.00 - 23.00';
        }

        // Ensure the event exists in the database
        $dbEvent = \App\Models\Event::firstOrCreate(
            ['id' => $event['id']],
            [
                'title' => $event['nama'] ?? 'SeTiket',
                'date' => \App\Http\Controllers\AdminController::parseDateString($event['tanggal'] ?? ''),
                'location' => $event['lokasi'] ?? 'City Square',
                'quota' => 5000
            ]
        );

        // Seed default categories if none exist yet in DB
        if ($dbEvent->categories()->count() === 0) {
            $dbEvent->categories()->createMany([
                ['name' => '3K Fun Walk', 'code' => '3K', 'bib_code' => 'FW', 'price' => 100000],
                ['name' => '5K Night Run', 'code' => '5K', 'bib_code' => 'NR', 'price' => 150000],
                ['name' => '10K Challenger', 'code' => '10K', 'bib_code' => 'CH', 'price' => 250000],
            ]);
        }

        $categories = $dbEvent->categories;

        $paymentMethods = $event['payment_methods'] ?? [];
        if (empty($paymentMethods)) {
            $paymentMethods = [
                [
                    'name' => 'Transfer Bank BCA',
                    'account_number' => '80771234567890',
                    'account_holder' => 'SeTiket Organizer'
                ],
                [
                    'name' => 'E-Wallet DANA',
                    'account_number' => '081234567890',
                    'account_holder' => 'SeTiket Organizer'
                ]
            ];
        }
 
        return view('register', compact('event', 'categories', 'paymentMethods'));
    }
 
    public function submitRegistration(Request $request)
    {
        $eventId = $request->input('event_id', 1);
        $validCategories = \App\Models\EventCategory::where('event_id', $eventId)->pluck('code')->toArray();
        if (empty($validCategories)) {
            $validCategories = ['3K', '5K', '10K'];
        }

        $events = \App\Http\Controllers\HomeController::loadEvents();
        $jsonEvent = collect($events)->firstWhere('id', (int)$eventId);
        $paymentMethods = $jsonEvent['payment_methods'] ?? [];
        if (empty($paymentMethods)) {
            $paymentMethods = [
                ['name' => 'Transfer Bank BCA'],
                ['name' => 'E-Wallet DANA']
            ];
        }
        $validPaymentMethods = collect($paymentMethods)->pluck('name')->toArray();

        $validated = $request->validate([
            'fullname'          => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'phone'             => 'required|string|max:20',
            'dob'               => 'required|date',
            'gender'            => 'required|in:male,female',
            'address'           => 'required|string',
            'nik'               => 'required|string|size:16|regex:/^[0-9]+$/',
            'city'              => 'required|string|max:255',
            'medical_history'   => 'nullable|string|max:1000',
            'jersey_size'       => 'required|in:S,M,L,XL,XXL',
            'emergency_contact' => 'required|string|max:255',
            'category'          => 'required|in:' . implode(',', $validCategories),
            'event_id'          => 'required|integer',
            'payment_method'    => 'required|in:' . implode(',', $validPaymentMethods),
            'proof'             => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size'     => 'NIK harus terdiri dari 16 digit.',
            'nik.regex'    => 'NIK hanya boleh berisi angka.',
            'city.required' => 'Asal Kota/Kabupaten wajib diisi.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);
 
        // 1. Create or Find User
        $user = \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            ['name' => $validated['fullname'], 'password' => bcrypt('password'), 'role' => 'participant']
        );
 
        // 2. Find or Create Event in Database
        $events = \App\Http\Controllers\HomeController::loadEvents();
        $jsonEvent = collect($events)->firstWhere('id', (int)$eventId);
 
        $title = $jsonEvent['nama'] ?? 'SeTiket';
        
        $event = \App\Models\Event::firstOrCreate(
            ['id' => $eventId],
            [
                'title' => $title,
                'date' => \App\Http\Controllers\AdminController::parseDateString($jsonEvent['tanggal'] ?? ''),
                'location' => $jsonEvent['lokasi'] ?? 'City Square',
                'quota' => 5000
            ]
        );
 
        // 3. Create Participant
        $participant = \App\Models\Participant::create([
            'user_id'           => $user->id,
            'event_id'          => $event->id,
            'fullname'          => $validated['fullname'],
            'phone'             => $validated['phone'],
            'dob'               => $validated['dob'],
            'gender'            => $validated['gender'],
            'address'           => $validated['address'],
            'nik'               => $validated['nik'],
            'city'              => $validated['city'],
            'medical_history'   => $validated['medical_history'] ?? null,
            'jersey_size'       => $validated['jersey_size'],
            'emergency_contact' => $validated['emergency_contact'],
            'category'          => $validated['category'],
        ]);
 
        // 4. Determine price and BIB code based on category in DB
        $categoryModel = \App\Models\EventCategory::where('event_id', $event->id)
            ->where('code', $validated['category'])
            ->first();
        
        $price = $categoryModel ? $categoryModel->price : 100000;
        $bibCode = $categoryModel ? $categoryModel->bib_code : 'FW';
 
        // 5. Generate Ticket
        $categoryCount = \App\Models\Participant::where('event_id', $event->id)
                                                ->where('category', $validated['category'])
                                                ->whereHas('ticket')
                                                ->count();
        $newNumber = $categoryCount + 1;
        $ticketCode = 'ST-' . $validated['category'] . '-' . $bibCode . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
 
        $ticket = \App\Models\Ticket::create([
            'participant_id' => $participant->id,
            'ticket_code'    => $ticketCode,
            'status'         => 'pending'
        ]);
 
        // 6. Save Proof of Payment file
        $path = $request->file('proof')->store('proofs', 'public');
 
        // 7. Create Payment record
        $payment = \App\Models\Payment::create([
            'ticket_id'        => $ticket->id,
            'amount'           => $price,
            'payment_method'   => $validated['payment_method'],
            'payment_status'   => 'waiting_verification',
            'proof_of_payment' => $path
        ]);
 
        return redirect()->route('registration.success')
            ->with('success', 'Pendaftaran berhasil! Bukti pembayaran telah diunggah. Silakan tunggu verifikasi admin.');
    }
 
    public function success()
    {
        return view('registration-success');
    }
 
    public function checkout($participant_id)
    {
        $participant = \App\Models\Participant::findOrFail($participant_id);
        
        $categoryModel = \App\Models\EventCategory::where('event_id', $participant->event_id)
            ->where('code', $participant->category)
            ->first();
        
        $price = $categoryModel ? $categoryModel->price : 100000;
        $bibCode = $categoryModel ? $categoryModel->bib_code : 'FW';
 
        $ticket = \App\Models\Ticket::where('participant_id', $participant->id)->first();
        
        if (!$ticket) {
            $categoryCount = \App\Models\Participant::where('event_id', $participant->event_id)
                                                    ->where('category', $participant->category)
                                                    ->whereHas('ticket')
                                                    ->count();
            $newNumber = $categoryCount + 1;
            $ticketCode = 'ST-' . $participant->category . '-' . $bibCode . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
 
            $ticket = \App\Models\Ticket::create([
                'participant_id' => $participant->id,
                'ticket_code' => $ticketCode,
                'status' => 'pending'
            ]);
        }
 
        // Create Payment record
        $payment = \App\Models\Payment::firstOrCreate(
            ['ticket_id' => $ticket->id],
            ['amount' => $price, 'payment_status' => 'pending']
        );
 
        return view('checkout', compact('participant', 'ticket', 'payment', 'price'));
    }
}
