<?php

namespace App\Services;

use App\Http\Controllers\HomeController;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventFormField;
use App\Models\EventPaymentAccount;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestoreService
{
    /**
     * Pastikan data dump Masta Unimus dan Explore The Moment sudah direstore ke database dan events.json.
     * Bersifat idempotent & aman dijalankan berkali-kali.
     */
    public static function ensureRestored(bool $force = false): bool
    {
        try {
            // Pastikan events.json juga sudah sinkron
            $events = HomeController::loadEvents();
            $event1Valid = false;
            $event2Valid = false;
            foreach ($events as $ev) {
                if (($ev['id'] ?? 0) === 1 && str_contains(strtolower($ev['nama'] ?? ''), 'masta')) {
                    $event1Valid = true;
                }
                if (($ev['id'] ?? 0) === 2 && str_contains(strtolower($ev['nama'] ?? ''), 'explore')) {
                    $event2Valid = true;
                }
            }
            if (!$event1Valid || !$event2Valid) {
                if (class_exists(\Database\Seeders\RealisticEventsSeeder::class)) {
                    (new \Database\Seeders\RealisticEventsSeeder())->syncEventsJsonOnly();
                }
            }

            $isAlreadyRestored = Order::where('event_id', 1)->count() >= 1000
                && Event::where('id', 1)->where('title', 'like', '%Masta%')->exists()
                && Event::where('id', 2)->where('title', 'like', '%EXPLORE%')->exists();

            if ($isAlreadyRestored && !$force) {
                return true;
            }

            return self::runRestore();
        } catch (\Throwable $e) {
            Log::error('RestoreService error: ' . $e->getMessage(), ['exception' => $e]);
            return false;
        }
    }

    public static function extractStatements(string $sqlPath): array
    {
        $handle = @fopen($sqlPath, 'r');
        if (!$handle) {
            return [];
        }

        $targetTables = ['events', 'event_categories', 'event_form_fields', 'event_payment_accounts', 'users', 'orders'];
        $regex = '/^INSERT INTO [`"]?(' . implode('|', $targetTables) . ')[`"]?/i';
        $statements = [];
        $capturing = false;
        $currentStmt = '';

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);
            if (!$capturing && preg_match($regex, $trimmed)) {
                $capturing = true;
                $currentStmt = $line;
            } elseif ($capturing) {
                $currentStmt .= $line;
            }

            if ($capturing && str_ends_with($trimmed, ';')) {
                $statements[] = $currentStmt;
                $capturing = false;
                $currentStmt = '';
            }
        }
        fclose($handle);

        return $statements;
    }

    public static function runRestore(): bool
    {
        $sqlPath = base_path('u408401204_setiket.sql');
        if (!file_exists($sqlPath)) {
            Log::warning('RestoreService: u408401204_setiket.sql file not found at ' . $sqlPath);
            return false;
        }

        // Pastikan Event 1 adalah Merchandise Inagurasi Masta Unimus
        Event::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Merchandise Inagurasi Masta Unimus',
                'date' => '2026-09-15',
                'location' => 'Universitas Muhammadiyah Semarang',
                'quota' => 5000,
            ]
        );

        // Pastikan Event 2 adalah EXPLORE THE MOMENT
        Event::updateOrCreate(
            ['id' => 2],
            [
                'title' => 'EXPLORE THE MOMENT',
                'date' => '2026-09-20',
                'location' => 'Spekta Merbabu, Kab. Semarang',
                'quota' => 5000,
            ]
        );

        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            $statements = self::extractStatements($sqlPath);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Jalankan seluruh statements dari dump
            foreach ($statements as $stmt) {
                // Gunakan INSERT IGNORE agar tidak bentrok dengan user/order/akun yang sudah ada
                $ignoreStmt = preg_replace('/^INSERT INTO/i', 'INSERT IGNORE INTO', trim($stmt));
                try {
                    DB::unprepared($ignoreStmt);
                } catch (\Throwable $e) {
                    Log::warning('Restore statement warning: ' . $e->getMessage());
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            // Environment non-MySQL (misal test in-memory SQLite)
            $catMasta = [
                ['name' => 'Paket Lengkap', 'code' => 'Paket Lengkap', 'bib_code' => 'Paket Lengkap', 'price' => 99999],
                ['name' => 'T-Shirt Only', 'code' => 'T-Shirt Only', 'bib_code' => 'T-Shirt Only', 'price' => 87000],
                ['name' => 'Keycahin Only', 'code' => 'Keycahin Only', 'bib_code' => 'Keycahin Only', 'price' => 8000],
                ['name' => 'Handfan Only', 'code' => 'Handfan Only', 'bib_code' => 'Handfan Only', 'price' => 6000],
                ['name' => 'T-Shirt+Keychain', 'code' => 'T-Shirt+Keychain', 'bib_code' => 'T-Shirt+Keychain', 'price' => 95000],
                ['name' => 'T-Shirt+Handfan', 'code' => 'T-Shirt+Handfan', 'bib_code' => 'T-Shirt+Handfan', 'price' => 93000],
                ['name' => 'Keychain+Handfan', 'code' => 'Keychain+Handfan', 'bib_code' => 'Keychain+Handfan', 'price' => 14000],
            ];
            foreach ($catMasta as $c) {
                EventCategory::updateOrCreate(['event_id' => 1, 'code' => $c['code']], $c);
            }
        }

        // Kategori Event 2 jika belum ada
        if (EventCategory::where('event_id', 2)->count() === 0) {
            EventCategory::create([
                'event_id' => 2,
                'name' => 'Presale Entry Pass (2 Hari)',
                'code' => 'PS2',
                'bib_code' => 'ETM1',
                'price' => 150000,
            ]);
            EventCategory::create([
                'event_id' => 2,
                'name' => 'VIP Community Pass + F&B Voucher',
                'code' => 'VIP',
                'bib_code' => 'ETMV',
                'price' => 250000,
            ]);
        }

        // Rekening pembayaran Event 2 jika belum ada
        if (EventPaymentAccount::where('event_id', 2)->count() === 0) {
            EventPaymentAccount::create([
                'event_id' => 2,
                'bank_name' => 'BCA',
                'account_number' => '8012398471',
                'account_holder' => 'PT SeTiket Kreasi Indonesia',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        EventFormField::ensureCoreFields(2);

        // Sinkronkan data ke events.json
        if (class_exists(\Database\Seeders\RealisticEventsSeeder::class)) {
            (new \Database\Seeders\RealisticEventsSeeder())->syncEventsJsonOnly();
        }

        Log::info('RestoreService: Data dump Masta Unimus & Explore The Moment berhasil direstore.');
        return true;
    }
}
