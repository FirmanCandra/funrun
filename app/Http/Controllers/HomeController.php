<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Participant;
use App\Models\Ticket;

class HomeController extends Controller
{
    public static function getEventsPath(): string
    {
        // Saat menjalankan test, pakai file terpisah supaya suite tidak membaca
        // — apalagi menimpa — data event asli di storage/app/events.json.
        if (app()->runningUnitTests()) {
            return storage_path('app/events.testing.json');
        }

        return storage_path('app/events.json');
    }

    public static function loadEvents(): array
    {
        $path = self::getEventsPath();
        $data = file_exists($path) ? json_decode(@file_get_contents($path), true) : null;

        if (! is_array($data) || empty($data)) {
            // Seed with default events on first run or when empty
            $defaults = self::defaultEvents();
            @file_put_contents($path, json_encode($defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $defaults;
        }

        return $data;
    }

    public static function saveEvents(array $events): void
    {
        file_put_contents(
            self::getEventsPath(),
            json_encode(array_values($events), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public static function defaultEvents(): array
    {
        if (app()->runningUnitTests()) {
            return [
                ['id' => 1, 'nama' => 'VOLT RHYTHM 2026', 'lokasi' => 'Lapangan Yonif Mekanis, Jakarta', 'tanggal' => '25 Juli 2026', 'harga' => 100000, 'thumbnail' => '', 'kategori' => 'upcoming', 'urlBeli' => 'https://wa.me/6289681201941'],
                ['id' => 2, 'nama' => 'STEP UP FEST 2026', 'lokasi' => 'Gambir Expo – Kemayoran', 'tanggal' => '25-26 Juli 2026', 'harga' => 145000, 'thumbnail' => '', 'kategori' => 'upcoming', 'urlBeli' => 'https://wa.me/6289681201941'],
                ['id' => 3, 'nama' => 'KELUYURUN', 'lokasi' => 'SMAN 2 Jember', 'tanggal' => '26 Juli 2026', 'harga' => 105000, 'thumbnail' => '', 'kategori' => 'upcoming', 'urlBeli' => 'https://wa.me/6289681201941'],
                ['id' => 4, 'nama' => 'SOERATS 2026', 'lokasi' => 'Kampus Bendan SCU', 'tanggal' => '26-28 September 2026', 'harga' => 55000, 'thumbnail' => '', 'kategori' => 'upcoming', 'urlBeli' => 'https://wa.me/6289681201941'],
            ];
        }

        if (class_exists(\Database\Seeders\RealisticEventsSeeder::class)) {
            $data = \Database\Seeders\RealisticEventsSeeder::getEventsData();
            return array_map(function ($ev) {
                $ev['urlBeli'] = 'https://wa.me/6289681201941';
                unset($ev['categories']);
                return $ev;
            }, $data);
        }

        return [];
    }

    public function index()
    {
        // Auto-seed database jika database event masih kosong (misal di fresh production deployment)
        if (!app()->runningUnitTests() && \App\Models\Event::count() === 0 && class_exists(\Database\Seeders\RealisticEventsSeeder::class)) {
            try {
                (new \Database\Seeders\RealisticEventsSeeder())->run();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $events = self::loadEvents();
        $q = trim(request('q', ''));

        if ($q !== '') {
            // Filter semua event yang cocok dengan nama atau lokasi (case-insensitive)
            $filtered = array_values(array_filter(
                $events,
                fn ($e) =>
                    mb_stripos($e['nama'] ?? '', $q) !== false ||
                    mb_stripos($e['lokasi'] ?? '', $q) !== false
            ));

            $highlightEvents = array_values(array_filter($filtered, fn ($e) => ($e['kategori'] ?? '') === 'highlight'));
            $upcomingEvents  = array_values(array_filter($filtered, fn ($e) => ($e['kategori'] ?? '') === 'upcoming'));
            $endedEvents     = array_values(array_filter($filtered, fn ($e) => ($e['kategori'] ?? '') === 'ended'));

            // Jika tidak ada pembagian khusus, masukkan semua hasil ke upcoming
            if (empty($highlightEvents) && empty($upcomingEvents) && empty($endedEvents)) {
                $upcomingEvents  = $filtered;
            }
        } else {
            $highlightEvents = array_values(array_filter($events, fn ($e) => ($e['kategori'] ?? '') === 'highlight'));
            $upcomingEvents  = array_values(array_filter($events, fn ($e) => ($e['kategori'] ?? '') === 'upcoming'));
            $endedEvents     = array_values(array_filter($events, fn ($e) => ($e['kategori'] ?? '') === 'ended'));
        }

        return view('welcome', array_merge(
            compact('upcomingEvents', 'highlightEvents', 'endedEvents', 'q'),
            self::participantContext()
        ));
    }

    /**
     * Ringkasan milik peserta yang sedang login, dipakai untuk membedakan
     * tampilan dari pengunjung biasa. Guest dan admin mendapat nilai kosong.
     */
    public static function participantContext(): array
    {
        $user = auth()->user();

        if (! $user || ! $user->isUser()) {
            return [
                'myTicketCount' => 0,
                'myPendingOrders' => 0,
                'myEventIds' => collect(),
            ];
        }

        return [
            'myTicketCount' => Ticket::whereIn('status', ['valid', 'checked-in'])
                ->whereHas('participant', fn ($q) => $q->where('user_id', $user->id))
                ->count(),

            'myPendingOrders' => Order::where('user_id', $user->id)
                ->where('payment_status', Order::STATUS_WAITING)
                ->count(),

            // Dipakai untuk menandai kartu event yang sudah pernah dia daftari.
            'myEventIds' => Participant::where('user_id', $user->id)
                ->pluck('event_id')
                ->unique(),
        ];
    }

    public function showEvent($id)
    {
        $events = self::loadEvents();
        $event = collect($events)->firstWhere('id', (int) $id);

        if (! $event) {
            abort(404);
        }

        // Apply fallbacks
        if (empty($event['waktu'])) {
            $event['waktu'] = '16.00 - 23.00';
        }
        if (empty($event['deskripsi'])) {
            $event['deskripsi'] = 'Event '.$event['nama'].' hadir sebagai salah satu acara paling dinamis dan ditunggu-tunggu tahun ini! Diselenggarakan di '.$event['lokasi'].", event ini berkomitmen untuk menyatukan komunitas melalui perpaduan energi, kreativitas, dan kolaborasi.\n\nJangan lewatkan momen seru dan panggung hiburan megah yang dirancang untuk memberikan pengalaman terbaik bagi Anda dan rekan-rekan. Dapatkan tiket Anda sekarang juga sebelum kehabisan!";
        }
        if (empty($event['syarat_ketentuan'])) {
            $event['syarat_ketentuan'] = "1. Tiket yang sah dibeli secara resmi melalui platform ti.tix.com.\n2. Setiap pembelian bersifat final (non-refundable) kecuali terjadi pembatalan acara oleh pihak penyelenggara.\n3. E-Ticket yang didapat wajib ditunjukkan saat memasuki area acara untuk dipindai (check-in).\n4. Penyelenggara berhak menolak masuk bagi pemegang tiket yang tidak dapat menunjukkan bukti tiket atau jika kode tiket telah dipindai sebelumnya.\n5. Segala bentuk pelanggaran hukum di area acara akan ditindak tegas sesuai peraturan yang berlaku.\n6. Perubahan jadwal atau lokasi acara akan diumumkan secara resmi melalui saluran media sosial pihak penyelenggara.";
        }

        // Fetch ticket categories
        $categories = \App\Models\EventCategory::where('event_id', (int) $id)->get();
        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['id' => 1, 'name' => 'Regular Entry', 'code' => 'REG', 'price' => (int)($event['harga'] ?? 100000)],
                (object) ['id' => 2, 'name' => 'VIP Access Pass', 'code' => 'VIP', 'price' => (int)(($event['harga'] ?? 100000) * 1.5)],
            ]);
        }

        return view('event-detail', array_merge(
            compact('event', 'categories'),
            self::participantContext()
        ));
    }
}
