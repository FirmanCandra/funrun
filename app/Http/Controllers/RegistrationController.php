<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventFormField;
use App\Models\EventPaymentAccount;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * Batas jumlah tiket dalam satu pesanan.
     */
    public const MAX_TICKETS_PER_ORDER = 10;

    public function showRegistrationForm(Request $request)
    {
        $eventId = $request->query('event_id', 1);
        $events = HomeController::loadEvents();
        $event = collect($events)->firstWhere('id', (int) $eventId);

        if (! $event) {
            // Fallback to first event or default
            $event = count($events) > 0 ? $events[0] : [
                'id' => 1,
                'nama' => 'SeTiket',
                'lokasi' => 'City Square',
                'tanggal' => '25-26 Juli 2026',
                'harga' => 100000,
                'kategori' => 'upcoming',
                'waktu' => '16.00 - 23.00',
            ];
        }

        if (empty($event['waktu'])) {
            $event['waktu'] = '16.00 - 23.00';
        }

        if ($event['is_closed'] ?? false) {
            return redirect()->route('event.show', $event['id'])->with('error', 'Mohon maaf, pendaftaran untuk event ini telah ditutup.');
        }

        if ($event['is_private'] ?? false) {
            return redirect()->route('event.show', $event['id'])->with('error', 'Mohon maaf, event ini bersifat private dan hanya menerima pendaftaran via undangan/import.');
        }

        $dbEvent = $this->resolveEvent($event);
        $categories = $dbEvent->categories;

        // Rekening tujuan transfer milik event ini, diatur super admin.
        $paymentAccounts = EventPaymentAccount::activeFor($dbEvent->id);

        // Susunan field mengikuti konfigurasi formulir milik event ini.
        $formFields = EventFormField::activeFor($dbEvent->id);

        return view('register', compact('event', 'categories', 'paymentAccounts', 'formFields'));
    }

    /**
     * Simpan pesanan berisi satu atau banyak tiket.
     *
     * Data diri diisi per peserta; yang dipakai bersama untuk seluruh pesanan
     * hanya metode pembayaran dan satu bukti transfer.
     */
    public function submitRegistration(Request $request)
    {
        // Event divalidasi lebih dulu dan terpisah: aturan validasi selanjutnya
        // disusun dari konfigurasi formulir milik event ini, jadi event-nya
        // harus dipastikan ada sebelum apa pun menyentuhnya.
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
        ], [
            'event_id.exists' => 'Event yang dipilih tidak ditemukan.',
        ]);

        $eventId = (int) $request->input('event_id');

        $categories = EventCategory::where('event_id', $eventId)->get();
        $validCategories = $categories->pluck('code')->all();
        if (empty($validCategories)) {
            $validCategories = ['3K', '5K', '10K'];
        }

        $jsonEvent = collect(HomeController::loadEvents())->firstWhere('id', $eventId);

        if ($jsonEvent && ($jsonEvent['is_closed'] ?? false)) {
            throw ValidationException::withMessages([
                'event_id' => 'Mohon maaf, pendaftaran untuk event ini telah ditutup.',
            ]);
        }

        if ($jsonEvent && ($jsonEvent['is_private'] ?? false)) {
            throw ValidationException::withMessages([
                'event_id' => 'Mohon maaf, event ini bersifat private dan hanya menerima pendaftaran via undangan/import.',
            ]);
        }

        // Rekening tujuan harus salah satu milik event ini dan masih aktif —
        // tanpa itu pembeli bisa mengarahkan transfernya ke rekening event lain.
        $paymentAccounts = EventPaymentAccount::activeFor($eventId);

        if ($paymentAccounts->isEmpty()) {
            throw ValidationException::withMessages([
                'payment_account_id' => 'Event ini belum punya rekening tujuan transfer. Hubungi penyelenggara.',
            ]);
        }

        // Field bawaan yang aktif dan field tambahan diambil dari konfigurasi
        // formulir milik event ini, bukan dari daftar tetap.
        $formFields = EventFormField::activeFor($eventId);

        $rules = [
            // event_id berasal dari input tersembunyi, jadi wajib diikat ke event nyata.
            'event_id' => 'required|integer|exists:events,id',
            'payment_account_id' => ['required', Rule::in($paymentAccounts->pluck('id')->all())],
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Tidak ada field peserta yang dipaksakan di sini. Seluruh aturannya
            // disusun dari konfigurasi formulir milik event, di perulangan bawah.
            'participants' => 'required|array|min:1|max:'.self::MAX_TICKETS_PER_ORDER,
        ];

        $messages = [
            'participants.required' => 'Minimal satu peserta harus diisi.',
            'participants.max' => 'Maksimal '.self::MAX_TICKETS_PER_ORDER.' tiket dalam satu pesanan.',
            'participants.*.nik.size' => 'NIK harus terdiri dari 16 digit.',
            'participants.*.nik.regex' => 'NIK hanya boleh berisi angka.',
            'participants.*.nik.distinct' => 'NIK tiap peserta harus berbeda.',
            'event_id.exists' => 'Event yang dipilih tidak ditemukan.',
            'payment_account_id.required' => 'Pilih rekening tujuan transfer.',
            'payment_account_id.in' => 'Rekening tujuan tidak valid untuk event ini.',
            'proof.required' => 'Bukti transfer wajib diunggah.',
            'proof.image' => 'Bukti transfer harus berupa gambar (JPG/PNG/GIF).',
            'proof.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ];

        $attributes = [];

        foreach ($formFields as $field) {
            $path = $field->is_core
                ? 'participants.*.'.$field->key
                : 'participants.*.custom.'.$field->key;

            $rules[$path] = $this->rulesForField($field, $validCategories);
            $attributes[$path] = strtolower($field->label);

            // Label buatan admin dipakai apa adanya di pesan galat.
            $messages[$path.'.required'] = $field->label.' wajib diisi.';
            $messages[$path.'.in'] = $field->label.' yang dipilih tidak valid.';

            if ($field->required && $field->type === EventFormField::TYPE_CONSENT) {
                $messages[$path.'.accepted'] = $field->label.' harus disetujui.';
            }
        }

        $validated = $request->validate($rules, $messages, $attributes);

        // Pengecekan NIK ganda hanya masuk akal kalau NIK memang ditanyakan.
        if ($formFields->firstWhere('key', 'nik')) {
            $this->rejectNiksAlreadyRegistered($validated['participants'], $eventId);
        }

        $event = $this->resolveEvent($jsonEvent ?? ['id' => $eventId]);
        $categoryByCode = $categories->keyBy('code');

        // Kalau admin mematikan pilihan kategori, seluruh peserta memakai
        // kategori pertama event ini — harga dan kode BIB tetap punya sumber.
        $kategoriCadangan = $categories->first();

        $proofPath = $request->file('proof')->store('proofs', 'public');

        $account = $paymentAccounts->firstWhere('id', (int) $validated['payment_account_id']);

        $order = DB::transaction(function () use ($validated, $event, $categoryByCode, $kategoriCadangan, $proofPath, $request, $formFields, $account) {
            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => $request->user()->id,
                'event_id' => $event->id,
                'total_amount' => 0,
                // Nomor rekening ikut disalin, bukan hanya direferensikan: kalau
                // super admin mengganti rekening nanti, pesanan ini tetap
                // menunjukkan ke mana pembeli sebenarnya diminta transfer.
                'payment_method' => $account->bank_name,
                'payment_account_id' => $account->id,
                'payment_account_number' => $account->account_number,
                'payment_account_holder' => $account->account_holder,
                'payment_status' => Order::STATUS_WAITING,
                'proof_of_payment' => $proofPath,
            ]);

            $total = 0;

            foreach ($validated['participants'] as $row) {
                // Kategori dipakai untuk harga dan kode BIB. Kalau field-nya
                // dimatikan admin, pakai kategori pertama event ini.
                $category = $categoryByCode->get($row['category'] ?? null) ?: $kategoriCadangan;
                $price = $category->price ?? 0;
                $categoryCode = $category->code ?? 'UMUM';
                $bibCode = $category->bib_code ?? 'UM';

                $attributes = [
                    'user_id' => $request->user()->id,
                    'event_id' => $event->id,
                    'category' => $categoryCode,
                    'custom_data' => [],
                ];

                // Hanya field yang diaktifkan admin event yang ikut disimpan.
                foreach ($formFields as $field) {
                    if ($field->is_core) {
                        $attributes[$field->key] = $this->normalizeValue($field, $row[$field->key] ?? null);
                    } else {
                        $attributes['custom_data'][$field->key] = $this->normalizeValue(
                            $field,
                            $row['custom'][$field->key] ?? null
                        );
                    }
                }

                // Kategori tersimpan tetap yang benar-benar dipakai menghitung harga.
                $attributes['category'] = $categoryCode;

                $participant = Participant::create($attributes);

                Ticket::create([
                    'participant_id' => $participant->id,
                    'order_id' => $order->id,
                    'ticket_code' => $this->nextTicketCode($event->id, $categoryCode, $bibCode),
                    'status' => 'pending',
                ]);

                $total += $price;
            }

            $order->update(['total_amount' => $total]);

            return $order;
        });

        return redirect()->route('registration.success', ['order' => $order->order_code])
            ->with('success', 'Pesanan berhasil dibuat! Silakan tunggu verifikasi admin.');
    }

    public function success(Request $request)
    {
        $order = Order::with(['event', 'tickets.participant'])
            ->where('order_code', $request->query('order'))
            ->where('user_id', $request->user()->id)
            ->first();

        return view('registration-success', compact('order'));
    }

    // ===== Helper =====

    /**
     * Aturan validasi untuk satu field, disusun dari tipe dan status wajibnya.
     */
    private function rulesForField(EventFormField $field, array $categoryCodes = []): array
    {
        // Persetujuan: kalau wajib harus dicentang, kalau tidak boleh dilewati.
        if ($field->type === EventFormField::TYPE_CONSENT) {
            return $field->required ? ['accepted'] : ['nullable', 'boolean'];
        }

        $rules = [$field->required ? 'required' : 'nullable'];

        // Field core bertipe select punya daftar pilihan tetap.
        if ($field->is_core && isset(EventFormField::SELECT_OPTIONS[$field->key])) {
            $rules[] = Rule::in(array_keys(EventFormField::SELECT_OPTIONS[$field->key]));

            return $rules;
        }

        // Kategori: pilihannya milik event ini, bukan daftar tetap.
        if ($field->is_core && $field->key === 'category') {
            $rules[] = Rule::in($categoryCodes);

            return $rules;
        }

        // NIK tetap divalidasi bentuknya kalau ditanyakan, dan tidak boleh
        // diulang antar peserta dalam satu pesanan.
        if ($field->is_core && $field->key === 'nik') {
            $rules[] = 'string';
            $rules[] = 'size:16';
            $rules[] = 'regex:/^[0-9]+$/';
            $rules[] = 'distinct';

            return $rules;
        }

        if ($field->is_core && $field->key === 'phone') {
            $rules[] = 'string';
            $rules[] = 'max:20';

            return $rules;
        }

        $rules[] = match ($field->type) {
            EventFormField::TYPE_DATE => 'date',
            EventFormField::TYPE_NUMBER => 'numeric',
            EventFormField::TYPE_TEXTAREA => 'string',
            default => 'string',
        };

        if ($field->type === EventFormField::TYPE_TEXTAREA) {
            $rules[] = 'max:1000';
        } elseif ($field->type === EventFormField::TYPE_TEXT) {
            $rules[] = 'max:255';
        }

        return $rules;
    }

    /**
     * Samakan bentuk nilai sebelum disimpan.
     */
    private function normalizeValue(EventFormField $field, $value)
    {
        if ($field->type === EventFormField::TYPE_CONSENT) {
            return (bool) $value;
        }

        return $value === '' ? null : $value;
    }

    /**
     * Pastikan event ada di database (sumber utamanya file JSON) beserta
     * kategori bawaannya.
     */
    private function resolveEvent(array $jsonEvent): Event
    {
        $event = Event::firstOrCreate(
            ['id' => $jsonEvent['id']],
            [
                'title' => $jsonEvent['nama'] ?? 'SeTiket',
                'date' => AdminController::parseDateString($jsonEvent['tanggal'] ?? ''),
                'location' => $jsonEvent['lokasi'] ?? 'City Square',
                'quota' => 5000,
            ]
        );

        if ($event->categories()->count() === 0) {
            $event->categories()->createMany([
                ['name' => '3K Fun Walk', 'code' => '3K', 'bib_code' => 'FW', 'price' => 100000],
                ['name' => '5K Night Run', 'code' => '5K', 'bib_code' => 'NR', 'price' => 150000],
                ['name' => '10K Challenger', 'code' => '10K', 'bib_code' => 'CH', 'price' => 250000],
            ]);
            $event->load('categories');
        }

        return $event;
    }

    /**
     * Tolak NIK yang sudah terdaftar di event ini pada pesanan sebelumnya.
     */
    private function rejectNiksAlreadyRegistered(array $participants, int $eventId): void
    {
        $niks = array_column($participants, 'nik');

        $taken = Participant::where('event_id', $eventId)
            ->whereIn('nik', $niks)
            ->pluck('nik')
            ->all();

        if (empty($taken)) {
            return;
        }

        $errors = [];
        foreach ($participants as $index => $row) {
            if (in_array($row['nik'], $taken, true)) {
                $errors["participants.{$index}.nik"] = 'NIK ini sudah terdaftar pada event tersebut.';
            }
        }

        throw ValidationException::withMessages($errors);
    }

    /**
     * Nomor urut tiket per event + kategori.
     *
     * ID event ikut masuk ke dalam kode karena `tickets.ticket_code` unik secara
     * global, sementara penomoran BIB dihitung ulang dari 1 di tiap event —
     * tanpa itu, peserta 5K pertama di event A dan event B menghasilkan kode
     * yang sama persis dan penyimpanan gagal.
     *
     * Nomor dihitung dari jumlah tiket yang sudah ada, jadi pemanggilan
     * berturut-turut dalam satu transaksi tetap menghasilkan nomor berbeda.
     * Loop menjaga dari tabrakan kalau ada pesanan lain yang masuk bersamaan.
     */
    private function nextTicketCode(int $eventId, string $categoryCode, string $bibCode): string
    {
        $sequence = Ticket::whereHas('participant', function ($q) use ($eventId, $categoryCode) {
            $q->where('event_id', $eventId)->where('category', $categoryCode);
        })->count() + 1;

        do {
            $code = 'ST-'.$eventId.'-'.$categoryCode.'-'.$bibCode.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
