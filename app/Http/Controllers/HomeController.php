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
        if (! file_exists($path)) {
            // Seed with default events on first run
            $defaults = self::defaultEvents();
            file_put_contents($path, json_encode($defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $defaults;
        }
        $data = json_decode(file_get_contents($path), true);

        return is_array($data) ? $data : [];
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
        return [
            [
                'id' => 1,
                'nama' => 'VOLT RHYTHM 2026',
                'lokasi' => 'Lapangan Yonif Mekanis, Jakarta',
                'tanggal' => '25 Juli 2026',
                'harga' => 100000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 2,
                'nama' => 'STEP UP FEST 2026',
                'lokasi' => 'Gambir Expo – Kemayoran',
                'tanggal' => '25-26 Juli 2026',
                'harga' => 145000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 3,
                'nama' => 'KELUYURUN',
                'lokasi' => 'SMAN 2 Jember',
                'tanggal' => '26 Juli 2026',
                'harga' => 105000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 4,
                'nama' => 'SOERATS 2026',
                'lokasi' => 'Kampus Bendan SCU',
                'tanggal' => '26-28 September 2026',
                'harga' => 55000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 5,
                'nama' => 'THE PARADE PROJECT',
                'lokasi' => 'Kabupaten Kendal',
                'tanggal' => '3 Oktober 2026',
                'harga' => 85000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 6,
                'nama' => 'DIANA RIA FESTIVAL',
                'lokasi' => 'Pekalongan, Jawa Tengah',
                'tanggal' => '15 Agustus 2026',
                'harga' => 90000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 7,
                'nama' => 'INVESTOSIR RUN 2026',
                'lokasi' => 'Semarang',
                'tanggal' => '13-15 Agustus 2026',
                'harga' => 0,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 8,
                'nama' => 'ENERGY COLOR RUN',
                'lokasi' => 'Lapangan Pemuda',
                'tanggal' => '2 Juli 2026',
                'harga' => 80000,
                'thumbnail' => '',
                'kategori' => 'upcoming',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 9,
                'nama' => 'JAMNAS 7 TLCI',
                'lokasi' => 'Spekta Merbabu, Kab. Semarang',
                'tanggal' => '9 Juli 2027',
                'harga' => 150000,
                'thumbnail' => '',
                'kategori' => 'highlight',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 10,
                'nama' => 'JEMBER 10K',
                'lokasi' => 'Jember, Jawa Timur',
                'tanggal' => '19 Juli 2026',
                'harga' => 250000,
                'thumbnail' => '',
                'kategori' => 'highlight',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 11,
                'nama' => 'KAIJERUN 2026',
                'lokasi' => 'Universitas Jember',
                'tanggal' => '5 Juli 2026',
                'harga' => 150000,
                'thumbnail' => '',
                'kategori' => 'highlight',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
            [
                'id' => 12,
                'nama' => 'RUPIAH BOROBUDUR RUN',
                'lokasi' => 'Kawasan Candi Borobudur',
                'tanggal' => '5 Juli 2026',
                'harga' => 175000,
                'thumbnail' => '',
                'kategori' => 'highlight',
                'urlBeli' => 'https://wa.me/6289681201941',
            ],
        ];
    }

    public function index()
    {
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

            // Tampilkan hasil pencarian di kedua slot agar tidak tersembunyi
            $upcomingEvents  = array_values(array_filter($filtered, fn ($e) => ($e['kategori'] ?? '') !== 'highlight'));
            $highlightEvents = array_values(array_filter($filtered, fn ($e) => ($e['kategori'] ?? '') === 'highlight'));

            // Jika tidak ada yang masuk highlight, masukkan semua hasil ke upcoming
            if (empty($highlightEvents) && empty($upcomingEvents)) {
                $upcomingEvents  = $filtered;
            }
        } else {
            $upcomingEvents  = array_values(array_filter($events, fn ($e) => ($e['kategori'] ?? '') === 'upcoming'));
            $highlightEvents = array_values(array_filter($events, fn ($e) => ($e['kategori'] ?? '') === 'highlight'));
        }

        return view('welcome', array_merge(
            compact('upcomingEvents', 'highlightEvents', 'q'),
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

        return view('event-detail', array_merge(
            compact('event'),
            self::participantContext()
        ));
    }
}
