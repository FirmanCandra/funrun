<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantImport implements ToCollection, WithHeadingRow
{
    public int $targetEventId;
    public int $imported = 0;
    public int $skipped = 0;
    public array $skippedReasons = [];

    private ?Order $batchOrder = null;

    public function __construct(int $targetEventId)
    {
        $this->targetEventId = $targetEventId;
    }

    public function collection(Collection $rows): void
    {
        $event = Event::findOrFail($this->targetEventId);

        // Ensure event has categories — fallback to defaults if none exist.
        if ($event->categories()->count() === 0) {
            $event->categories()->createMany([
                ['name' => '3K Fun Walk', 'code' => '3K', 'bib_code' => 'FW', 'price' => 100000],
                ['name' => '5K Night Run', 'code' => '5K', 'bib_code' => 'NR', 'price' => 150000],
                ['name' => '10K Challenger', 'code' => '10K', 'bib_code' => 'CH', 'price' => 250000],
            ]);
        }

        $validCategories = EventCategory::where('event_id', $this->targetEventId)
            ->pluck('code')
            ->all();
        $defaultCategory = $validCategories[0] ?? 'UMUM';

        // Detect custom fields from the first row
        $coreKeys = [
            'id', 'nama_lengkap', 'nik', 'asal_kota', 'email', 'no_whatsapp', 'kategori', 
            'ukuran_t_shirt', 'riwayat_penyakit', 'kode_pesanan', 'kode_tiket', 
            'status_pembayaran', 'status_check_in', 'nama', 'fullname', 'name', 'full_name',
            'no_ktp', 'ktp', 'kota', 'city', 'whatsapp', 'phone', 'telepon', 'no_hp', 'hp',
            'category', 'ukuran_tshirt', 'ukuran', 'jersey_size', 't_shirt', 'tshirt', 'size',
            'medical_history', 'riwayat', 'medical', 'e_mail', 'password', 'sandi', 'kata_sandi'
        ];
        
        $customFieldKeys = [];
        if ($rows->isNotEmpty()) {
            $firstRowKeys = $rows->first()->keys();
            foreach ($firstRowKeys as $key) {
                if (!in_array($key, $coreKeys) && !empty($key) && !is_numeric($key)) {
                    $customFieldKeys[] = $key;
                    
                    // Create EventFormField if it doesn't exist
                    $exists = \App\Models\EventFormField::where('event_id', $this->targetEventId)
                        ->where('key', $key)
                        ->exists();
                        
                    if (!$exists) {
                        \App\Models\EventFormField::create([
                            'event_id' => $this->targetEventId,
                            'key' => $key,
                            'label' => \Illuminate\Support\Str::title(str_replace('_', ' ', $key)),
                            'type' => 'text',
                            'is_core' => false,
                            'enabled' => true,
                            'required' => false,
                            'sort_order' => 99
                        ]);
                    }
                }
            }
        }

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because header is row 1

            // Normalize header keys — tolerant of various labels
            $nama  = $this->getColumn($row, ['nama_lengkap', 'nama', 'fullname', 'name', 'full_name']);
            $email = $this->getColumn($row, ['email', 'e-mail', 'e_mail']);

            // Required columns check
            if (empty($nama) || empty($email)) {
                $this->skipped++;
                $this->skippedReasons[] = "Baris {$rowNum}: Nama atau Email kosong.";
                continue;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->skipped++;
                $this->skippedReasons[] = "Baris {$rowNum}: Format email '{$email}' tidak valid.";
                continue;
            }

            // Optional columns
            $nik        = $this->getColumn($row, ['nik', 'no_ktp', 'ktp']);
            $city       = $this->getColumn($row, ['asal_kota', 'kota', 'city']);
            $phone      = $this->getColumn($row, ['no_whatsapp', 'whatsapp', 'phone', 'telepon', 'no_hp', 'hp']);
            $category   = $this->getColumn($row, ['kategori', 'category']);
            $jerseySize = $this->getColumn($row, ['ukuran_t_shirt', 'ukuran_t-shirt', 'ukuran_tshirt', 'ukuran', 'jersey_size', 't-shirt', 'tshirt', 'size']);
            $medical    = $this->getColumn($row, ['riwayat_penyakit', 'medical_history', 'riwayat', 'medical']);
            $importedPassword = $this->getColumn($row, ['password', 'sandi', 'kata_sandi']);
            
            // Collect custom data
            $customData = [];
            foreach ($customFieldKeys as $cfKey) {
                $cfValue = $this->getColumn($row, [$cfKey]);
                if ($cfValue !== null && $cfValue !== '') {
                    $customData[$cfKey] = $cfValue;
                }
            }

            // Skip duplicate NIK within target event
            if (!empty($nik)) {
                $nikExists = Participant::where('event_id', $this->targetEventId)
                    ->where('nik', $nik)
                    ->exists();
                if ($nikExists) {
                    $this->skipped++;
                    $this->skippedReasons[] = "Baris {$rowNum}: NIK '{$nik}' sudah terdaftar di event tujuan.";
                    continue;
                }
            }

            // Validate category if provided, create dynamically if missing
            if (empty($category)) {
                $category = $defaultCategory;
            } else {
                $upperCategory = strtoupper($category);
                $validUpperCategories = array_map('strtoupper', $validCategories);
                if (!in_array($upperCategory, $validUpperCategories)) {
                    // Create new category dynamically
                    \App\Models\EventCategory::create([
                        'event_id' => $this->targetEventId,
                        'name'     => $category,
                        'code'     => $upperCategory,
                        'bib_code' => strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $category), 0, 2)) ?: 'XX',
                        'price'    => 0,
                    ]);
                    $validCategories[] = $upperCategory;
                }
                $category = $upperCategory;
            }

            // Find or create user
            $user = User::where('email', $email)->first();
            if (!$user) {
                $plainPassword = !empty($importedPassword) ? $importedPassword : Str::random(8);
                $user = User::create([
                    'name'           => $nama,
                    'email'          => $email,
                    'password'       => bcrypt($plainPassword),
                    'plain_password' => $plainPassword,
                    'role'           => 'user',
                ]);
            } else {
                if (!empty($importedPassword) && $user->plain_password !== $importedPassword) {
                    $user->update([
                        'password'       => bcrypt($importedPassword),
                        'plain_password' => $importedPassword,
                    ]);
                }
            }

            // Create batch order (one per import session)
            if (!$this->batchOrder) {
                $this->batchOrder = Order::create([
                    'order_code'      => 'IMP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                    'user_id'         => auth()->id(),
                    'event_id'        => $this->targetEventId,
                    'total_amount'    => 0,
                    'payment_method'  => 'Import CSV',
                    'payment_status'  => Order::STATUS_PAID,
                    'proof_of_payment' => null,
                    'verified_by'     => auth()->id(),
                    'verified_at'     => now(),
                ]);
            }

            // Create participant
            $participant = Participant::create([
                'user_id'         => $user->id,
                'event_id'        => $this->targetEventId,
                'fullname'        => $nama,
                'nik'             => $nik ?: null,
                'phone'           => $phone ?: null,
                'city'            => $city ?: null,
                'category'        => $category,
                'jersey_size'     => $jerseySize ?: null,
                'medical_history' => $medical ?: null,
                'custom_data'     => empty($customData) ? null : $customData,
            ]);

            // Generate ticket code
            $categoryModel = EventCategory::where('event_id', $this->targetEventId)
                ->where('code', $category)
                ->first();
            $bibCode = $categoryModel->bib_code ?? 'UM';

            $sequence = Ticket::whereHas('participant', function ($q) use ($category) {
                $q->where('event_id', $this->targetEventId)->where('category', $category);
            })->count();
            // No +1 because the participant we just created is already counted

            $ticketCode = null;
            do {
                $code = 'ST-' . $this->targetEventId . '-' . $category . '-' . $bibCode . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                $sequence++;
            } while (Ticket::where('ticket_code', $code)->exists());
            $ticketCode = $code;

            // Create ticket (immediately valid)
            Ticket::create([
                'participant_id' => $participant->id,
                'order_id'       => $this->batchOrder->id,
                'ticket_code'    => $ticketCode,
                'qr_code'        => 'QR-' . $ticketCode . '-' . uniqid(),
                'status'         => 'valid',
            ]);

            $this->imported++;
        }
    }

    /**
     * Tolerant column lookup — tries multiple possible header names.
     */
    private function getColumn(Collection $row, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            // maatwebsite/excel normalizes headers to snake_case lowercase
            $normalized = Str::snake(Str::lower($key));
            if ($row->has($normalized) && $row->get($normalized) !== null) {
                $val = trim((string) $row->get($normalized));
                if ($val !== '' && $val !== '-') {
                    return $val;
                }
            }
            // Also try the key as-is
            if ($row->has($key) && $row->get($key) !== null) {
                $val = trim((string) $row->get($key));
                if ($val !== '' && $val !== '-') {
                    return $val;
                }
            }
        }
        return null;
    }
}
