<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPaymentAccount;
use App\Models\EventFormField;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

class RealisticEventsSeeder extends Seeder
{
    public static function getEventsData(): array
    {
        return [
            [
                'id' => 1,
                'nama' => 'SOUNDCHECK Vol.1 Night Party',
                'lokasi' => 'Kopi Nakal, Kemang, Jakarta Selatan',
                'kota' => 'Jakarta Selatan',
                'tanggal' => '9 Oktober 2026',
                'harga' => 35000,
                'thumbnail' => '/images/thumbnails/soundcheck-vol1.jpg',
                'kategori' => 'highlight',
                'tag' => 'Komunitas & Perkumpulan • Musik • K-Pop',
                'penyelenggara' => 'LOKET',
                'organizer_badge' => 'Verified Organizer',
                'waktu' => '20:30 - 23:55 WIB',
                'deskripsi' => "SOUND CHECK Vol. 1 adalah night party khusus buat kamu para K-Popers! 🎉✨\n\nENGENE, V.I.P, ARMY, STAY, dan K-Pop stan lainnya, merapat! Yuk, seru-seruan bareng sambil nyanyi, dance, dan party semalaman! Ada Noraebang, Random Play Dance, DJ Set, Free Drink, sampai Photobooth, dan semuanya bisa kamu dapetin cuma mulai Rp35.000!\n\nJangan lupa bawa lightstick kamu karena ada Exclusive Merchandise khusus buat kamu yang bawa lightstick ke SOUND CHECK Vol. 1. Selain itu, bakal ada voucher spesial, hadiah seru buat pemenang Random Play Dance, serta kesempatan memenangkan Official Album & Lightstick untuk lucky winners! 🎁",
                'syarat_ketentuan' => "1. Tiket yang sah dibeli secara resmi melalui platform SeTiket.\n2. Wajib berusia minimal 17 tahun ke atas (tunjukkan e-KTP saat masuk).\n3. E-Ticket yang didapat wajib ditunjukkan saat memasuki area acara untuk dipindai (check-in QR code).\n4. Tiket yang sudah dibeli bersifat non-refundable (tidak dapat diuangkan kembali).\n5. Dilarang membawa senjata tajam, minuman beralkohol dari luar, dan obat-obatan terlarang.\n6. Penyelenggara berhak menolak masuk pengunjung yang tidak mematuhi protokol keamanan.",
                'categories' => [
                    ['name' => 'Early Bird Entry (Limited)', 'code' => 'EB', 'bib_code' => 'SC1', 'price' => 35000],
                    ['name' => 'Presale Entry + Free Soft Drink', 'code' => 'PS', 'bib_code' => 'SC2', 'price' => 50000],
                    ['name' => 'VIP Entry + Unlimited Photobooth', 'code' => 'VIP', 'bib_code' => 'SCV', 'price' => 85000],
                ],
            ],
            [
                'id' => 2,
                'nama' => 'YE JAKARTA 2026 WORLD TOUR',
                'lokasi' => 'Stadion Madya GBK, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '14 November 2026',
                'harga' => 850000,
                'thumbnail' => '/images/thumbnails/ye-jakarta-2026.jpg',
                'kategori' => 'highlight',
                'tag' => 'Konser Musik • Hip Hop & Rap',
                'penyelenggara' => 'Raw Vision Collective',
                'organizer_badge' => 'Promotor Resmi',
                'waktu' => '19:00 - 23:00 WIB',
                'deskripsi' => "Konser megah spektakuler YE JAKARTA 2026 menghadirkan panggung audio-visual futuristik 360 derajat di Stadion Madya Gelora Bung Karno! ⚡🔥\n\nSaksikan penampilan live legendaris dengan tata panggung audio visual kelas dunia, tata suara berkekuatan lebih dari 100.000 watt, dan atmosfer stadion yang tak terlupakan.\n\nTiket tersedia dalam kuota sangat terbatas. Pastikan Anda memesan tiket resmi hanya melalui platform SeTiket.",
                'syarat_ketentuan' => "1. 1 akun hanya dapat membeli maksimal 4 tiket.\n2. Penonton wajib menukar e-ticket dengan wristband fisik di lokasi H-1 atau hari H.\n3. Dilarang membawa kamera profesional (DSLR/Mirrorless) atau perlengkapan rekaman audio profesional.\n4. Kategori Standing tidak disarankan untuk anak di bawah usia 14 tahun atau ibu hamil.\n5. Pembelian tiket resmi terikat dengan syarat & ketentuan promotor Raw Vision Collective.",
                'categories' => [
                    ['name' => 'Tribun Silver Seated', 'code' => 'SLV', 'bib_code' => 'T1', 'price' => 850000],
                    ['name' => 'Festival Standing Gold', 'code' => 'GLD', 'bib_code' => 'F1', 'price' => 1450000],
                    ['name' => 'VIP Lounge + Soundcheck Pass', 'code' => 'VIP', 'bib_code' => 'V1', 'price' => 2850000],
                ],
            ],
            [
                'id' => 3,
                'nama' => 'Boyz II Men + Dewa 19 Feat Ari Lasso Live',
                'lokasi' => 'Istora Senayan, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '9 Desember 2026',
                'harga' => 750000,
                'thumbnail' => '/images/thumbnails/boyz-dewa19.jpg',
                'kategori' => 'highlight',
                'tag' => 'Konser Musik • Nostalgia & Pop Rock',
                'penyelenggara' => 'Rajawali Indonesia',
                'organizer_badge' => 'Promotor Berpengalaman',
                'waktu' => '19:30 - 22:30 WIB',
                'deskripsi' => "Kolaborasi bersejarah antara legenda R&B dunia Boyz II Men bersama mahakarya band rock legendaris Indonesia DEWA 19 feat. Ari Lasso! 🎹🎤\n\nBawakan lagu-lagu hits abadi 'End of the Road', 'I'll Make Love to You', 'Kangen', 'Roman Picisan', dan puluhan hits abadi yang menemani generasi ke generasi dalam satu malam magis di Istora Senayan.",
                'syarat_ketentuan' => "1. Seluruh nomor kursi kategori Seated akan dialokasikan otomatis sesuai urutan pembayaran.\n2. Pintu venue dibuka pukul 17:30 WIB, konser dimulai tepat waktu pukul 19:30 WIB.\n3. Pengunjung dilarang membawa makanan dan minuman dari luar ke dalam area konser.\n4. Tiket yang sudah terbit memiliki QR Code unik yang hanya bisa dipindai satu kali.",
                'categories' => [
                    ['name' => 'Bronze Tribune', 'code' => 'BRZ', 'bib_code' => 'BZ', 'price' => 750000],
                    ['name' => 'Silver Center Seated', 'code' => 'SLV', 'bib_code' => 'SV', 'price' => 1200000],
                    ['name' => 'Gold VIP Front Row + Merchandise', 'code' => 'GLD', 'bib_code' => 'GD', 'price' => 2100000],
                ],
            ],
            [
                'id' => 4,
                'nama' => 'BIGBANG 2026-2027 WORLD TOUR <RE-BOOT>',
                'lokasi' => 'Jakarta International Stadium (JIS), Jakarta Utara',
                'kota' => 'Jakarta Utara',
                'tanggal' => '16 Januari 2027',
                'harga' => 1550000,
                'thumbnail' => '/images/thumbnails/bigbang-worldtour.jpg',
                'kategori' => 'highlight',
                'tag' => 'Konser Musik • K-Pop World Tour',
                'penyelenggara' => 'PK Entertainment',
                'organizer_badge' => 'Verified Promoter',
                'waktu' => '18:30 - 22:00 WIB',
                'deskripsi' => "The Kings of K-Pop are BACK! BIGBANG resmi menggelar konser spektakuler dunia di Jakarta International Stadium! 👑💥\n\nPersiapkan dirimu untuk lautan lightstick kuning mahkota, tata panggung raksasa dengan tata cahaya laser tercanggih, dan penampilan memukau sepanjang malam.",
                'syarat_ketentuan' => "1. Pembelian maksimal 2 tiket per transaksi dengan NIK terdaftar.\n2. Penonton kategori standing wajib mengantre sesuai nomor antrean (Queue Number) di e-ticket.\n3. Dilarang membawa tongsis, selfie stick, dan tas berukuran lebih dari 30x30 cm.\n4. E-Ticket akan divalidasi dengan identitas resmi (KTP/Paspor).",
                'categories' => [
                    ['name' => 'CAT 3 Upper Tribune', 'code' => 'CAT3', 'bib_code' => 'C3', 'price' => 1550000],
                    ['name' => 'CAT 2 Lower Tribune', 'code' => 'CAT2', 'bib_code' => 'C2', 'price' => 2400000],
                    ['name' => 'VIP Soundcheck Package', 'code' => 'VIP', 'bib_code' => 'VP', 'price' => 3850000],
                ],
            ],
            [
                'id' => 12,
                'nama' => 'Home Sweet Loan The Musical [Selasa, 6 Oktober 2026] (Alt. Show)',
                'lokasi' => 'Graha Bhakti Budaya, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '6 Oktober 2026',
                'harga' => 175000,
                'thumbnail' => '/images/thumbnails/home-sweet-loan.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Teater & Musikal • Drama & Seni Pertunjukan',
                'penyelenggara' => 'Visinema Live',
                'organizer_badge' => 'Promotor Resmi',
                'waktu' => '19:30 - 22:00 WIB',
                'deskripsi' => "Pertunjukan musikal mengharukan 'Home Sweet Loan The Musical' di Graha Bhakti Budaya Taman Ismail Marzuki!\n\nDiadaptasi dari novel & film laris karya Almira Bastari, kisahkan perjalanan Kaluna memperjuangkan mimpi memiliki rumah sendiri di tengah dinamika keluarga sandwich generation.",
                'syarat_ketentuan' => "1. Pembelian tiket resmi melalui SeTiket.\n2. Pintu teater ditutup 10 menit sebelum pertunjukan dimulai.\n3. Dilarang mengambil foto/video selama pertunjukan berlangsung.",
                'categories' => [
                    ['name' => 'Balkon', 'code' => 'BLK', 'bib_code' => 'HSL1', 'price' => 175000],
                    ['name' => 'Reguler', 'code' => 'REG', 'bib_code' => 'HSL2', 'price' => 275000],
                    ['name' => 'VIP Center', 'code' => 'VIP', 'bib_code' => 'HSL3', 'price' => 450000],
                ],
            ],
            [
                'id' => 13,
                'nama' => 'Home Sweet Loan The Musical [Rabu, 7 Oktober 2026]',
                'lokasi' => 'Graha Bhakti Budaya, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '7 Oktober 2026',
                'harga' => 175000,
                'thumbnail' => '/images/thumbnails/home-sweet-loan.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Teater & Musikal • Drama & Seni Pertunjukan',
                'penyelenggara' => 'Visinema Live',
                'organizer_badge' => 'Promotor Resmi',
                'waktu' => '19:30 - 22:00 WIB',
                'deskripsi' => "Pertunjukan musikal mengharukan 'Home Sweet Loan The Musical' di Graha Bhakti Budaya Taman Ismail Marzuki hari kedua!",
                'syarat_ketentuan' => "1. Pembelian tiket resmi melalui SeTiket.\n2. Pintu teater ditutup 10 menit sebelum pertunjukan dimulai.",
                'categories' => [
                    ['name' => 'Balkon', 'code' => 'BLK', 'bib_code' => 'HSL4', 'price' => 175000],
                    ['name' => 'Reguler', 'code' => 'REG', 'bib_code' => 'HSL5', 'price' => 275000],
                    ['name' => 'VIP Center', 'code' => 'VIP', 'bib_code' => 'HSL6', 'price' => 450000],
                ],
            ],
            [
                'id' => 14,
                'nama' => 'Home Sweet Loan The Musical [Kamis, 8 Oktober 2026]',
                'lokasi' => 'Graha Bhakti Budaya, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '8 Oktober 2026',
                'harga' => 175000,
                'thumbnail' => '/images/thumbnails/home-sweet-loan.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Teater & Musikal • Drama & Seni Pertunjukan',
                'penyelenggara' => 'Visinema Live',
                'organizer_badge' => 'Promotor Resmi',
                'waktu' => '19:30 - 22:00 WIB',
                'deskripsi' => "Pertunjukan musikal mengharukan 'Home Sweet Loan The Musical' di Graha Bhakti Budaya Taman Ismail Marzuki hari ketiga!",
                'syarat_ketentuan' => "1. Pembelian tiket resmi melalui SeTiket.\n2. Pintu teater ditutup 10 menit sebelum pertunjukan dimulai.",
                'categories' => [
                    ['name' => 'Balkon', 'code' => 'BLK', 'bib_code' => 'HSL7', 'price' => 175000],
                    ['name' => 'Reguler', 'code' => 'REG', 'bib_code' => 'HSL8', 'price' => 275000],
                    ['name' => 'VIP Center', 'code' => 'VIP', 'bib_code' => 'HSL9', 'price' => 450000],
                ],
            ],
            [
                'id' => 5,
                'nama' => 'JAKARTA NIGHT MARATHON 2026',
                'lokasi' => 'Plaza Barat Gelora Bung Karno, Senayan, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '24 Oktober 2026',
                'harga' => 150000,
                'thumbnail' => '/images/thumbnails/jakarta-night-marathon.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Olahraga • Fun Run & Marathon',
                'penyelenggara' => 'Milenial Sports ID',
                'organizer_badge' => 'Komunitas Lari Resmi',
                'waktu' => '18:00 - 23:00 WIB',
                'deskripsi' => "Lari malam paling megah dan penuh energi di jantung kota Jakarta! 🏃💨🌃\n\nMenyusuri jalan protokol Sudirman-Thamrin yang bebas kendaraan bermotor dengan instalasi neon glow, cheering stations di setiap 1 KM, serta live DJ di garis finis.\n\nFasilitas peserta: Running Jersey Eksklusif DRI-FIT, Medali Finisher Glow-In-The-Dark, BIB Number dengan Timing Chip, Refreshment & Isotonic, Asuransi Kecelakaan.",
                'syarat_ketentuan' => "1. Peserta menyatakan dalam kondisi sehat jasmani untuk mengikuti aktivitas lari.\n2. BIB number dan race pack wajib diambil pada Race Expo H-2 hingga H-1 di Senayan.\n3. Peserta wajib mengenakan BIB di dada depan selama perlombaan berlangsung.\n4. Cut-Off Time (COT): 5K (1 Jam), 10K (2 Jam), 21K (3.5 Jam).",
                'categories' => [
                    ['name' => '5K Fun Glow Run', 'code' => '5K', 'bib_code' => 'NR5', 'price' => 150000],
                    ['name' => '10K Timed Challenge', 'code' => '10K', 'bib_code' => 'NR1', 'price' => 250000],
                    ['name' => 'Half Marathon 21K Race', 'code' => '21K', 'bib_code' => 'HM2', 'price' => 375000],
                ],
            ],
            [
                'id' => 6,
                'nama' => 'INDIE SOUNDWAVE FESTIVAL 2026',
                'lokasi' => 'Gambir Expo Kemayoran, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '17-18 Oktober 2026',
                'harga' => 125000,
                'thumbnail' => '/images/thumbnails/indie-soundwave.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Festival Musik • Indie Pop & Rock',
                'penyelenggara' => 'Kolektif Nada Bersama',
                'organizer_badge' => 'Verified Organizer',
                'waktu' => '13:00 - 23:00 WIB',
                'deskripsi' => "Selebrasi 2 hari musik independen tanah air di Gambir Expo Kemayoran! 🎸🌴\n\nLineup spektakuler: HINDIA, DANILLA, PAMUNGKAS, .FEAST, FOURTWNTY, REALITY CLUB, THE ADAMS, dan MORFEM. Selain 3 panggung musik megah, ada Creative Market, Vinyl Record Fair, F&B Street Bazaar, dan Visual Art Exhibition.",
                'syarat_ketentuan' => "1. Tiket berlaku sesuai jenis hari (Day 1, Day 2, atau 2-Day Pass).\n2. Dilarang membawa makanan/minuman dari luar, senjata, drone, dan flare.\n3. Anak di bawah usia 5 tahun gratis masuk dengan pendamping dewasa.\n4. Penyelenggara menyediakan area medis darurat dan pos ramah disabilitas.",
                'categories' => [
                    ['name' => 'Day 1 Pass (Saturday)', 'code' => 'D1', 'bib_code' => 'ID1', 'price' => 125000],
                    ['name' => 'Day 2 Pass (Sunday)', 'code' => 'D2', 'bib_code' => 'ID2', 'price' => 125000],
                    ['name' => '2-Days All Access Pass', 'code' => '2D', 'bib_code' => 'IDA', 'price' => 210000],
                ],
            ],
            [
                'id' => 7,
                'nama' => 'STAND UP COMEDY SPECIAL: TAWA TANPA BATAS',
                'lokasi' => 'Balai Sarbini, Semanggi, Jakarta Selatan',
                'kota' => 'Jakarta Selatan',
                'tanggal' => '7 November 2026',
                'harga' => 150000,
                'thumbnail' => '/images/thumbnails/tawa-tanpa-batas.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Stand Up Comedy • Hiburan & Komedi',
                'penyelenggara' => 'Majelis Lucu Indonesia',
                'organizer_badge' => 'Official Organizer',
                'waktu' => '19:00 - 21:30 WIB',
                'deskripsi' => "Pertunjukan komedi tunggal terlucu tahun ini! 🎤🤣\n\nMenghadirkan materi segar 100% baru yang belum pernah dibawakan di platform mana pun oleh jajaran komika teratas tanah air. Siapkan tawa lepasmu selama 2.5 jam penuh canda cerdas, kritik sosial menggelitik, dan tawa tanpa jeda!",
                'syarat_ketentuan' => "1. Batas usia penonton minimal 18 tahun (konten dewasa).\n2. Dilarang merekam video atau suara selama pertunjukan berlangsung (pelanggar akan dikeluarkan).\n3. Pintu teater akan ditutup tepat waktu pukul 19:00 WIB.",
                'categories' => [
                    ['name' => 'Balkon Atas Seated', 'code' => 'BLK', 'bib_code' => 'SB1', 'price' => 150000],
                    ['name' => 'Reguler Center Row', 'code' => 'REG', 'bib_code' => 'SR1', 'price' => 250000],
                    ['name' => 'VIP Front Row + Meet & Greet', 'code' => 'VIP', 'bib_code' => 'SV1', 'price' => 450000],
                ],
            ],
            [
                'id' => 8,
                'nama' => 'INDONESIA TECH & AI SUMMIT 2026',
                'lokasi' => 'ICE BSD City Hall 5-6, Tangerang, Banten',
                'kota' => 'Tangerang',
                'tanggal' => '28-29 Oktober 2026',
                'harga' => 180000,
                'thumbnail' => '/images/thumbnails/indonesia-tech-summit.jpg',
                'kategori' => 'upcoming',
                'tag' => 'Workshop & Seminar • Teknologi & Startup',
                'penyelenggara' => 'Tech Innovator Asia',
                'organizer_badge' => 'Verified Organizer',
                'waktu' => '09:00 - 17:00 WIB',
                'deskripsi' => "Konferensi teknologi & kecerdasan buatan terbesar di Asia Tenggara! 🚀🤖\n\nMenghadirkan 40+ pembicara global dari raksasa teknologi dan startup ternama. Membahas Autonomous AI Agents, Cloud Scale Architecture, Cyber Defense, dan masa depan Software Engineering di era Artificial Intelligence. Dilengkapi dengan Career Fair & Exhibition Booth 80+ perusahaan.",
                'syarat_ketentuan' => "1. Tiket mencakup e-sertifikat resmi, materi presentasi speaker, lunch & coffee break.\n2. Peserta disarankan membawa laptop untuk sesi hands-on tech workshop.\n3. Tiket dapat dipindahtangankan paling lambat H-3 melalui konfirmasi panitia.",
                'categories' => [
                    ['name' => 'Student & Early Career Pass', 'code' => 'STU', 'bib_code' => 'TS1', 'price' => 180000],
                    ['name' => 'Professional 2-Day All Access', 'code' => 'PRO', 'bib_code' => 'TP2', 'price' => 380000],
                    ['name' => 'VIP Pass + Networking Dinner', 'code' => 'VIP', 'bib_code' => 'TVP', 'price' => 850000],
                ],
            ],
            [
                'id' => 9,
                'nama' => 'JAKARTA COLOR RUN FESTIVAL 2025',
                'lokasi' => 'Stadion Gelora Bung Karno (GBK), Jakarta',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '19 Juli 2025',
                'harga' => 120000,
                'thumbnail' => '/images/thumbnails/jakarta-color-run-2025.jpg',
                'kategori' => 'ended',
                'tag' => 'Olahraga • Fun Run & Color Festival',
                'penyelenggara' => 'Color Festival Asia',
                'organizer_badge' => 'Event Sukses',
                'waktu' => '06:00 - 11:00 WIB',
                'deskripsi' => "Jakarta Color Run Festival 2025 sukses diselenggarakan dengan penuh kemeriahan! Ribuan peserta dari berbagai kota telah bergabung merayakan pesta warna, kebersamaan, dan kesehatan di kawasan GBK Senayan.",
                'syarat_ketentuan' => "Event telah selesai diselenggarakan pada 19 Juli 2025. Penjualan tiket resmi telah ditutup.",
                'categories' => [
                    ['name' => '5K Fun Color Runner Pass', 'code' => '5K', 'bib_code' => 'CR1', 'price' => 120000],
                ],
            ],
            [
                'id' => 10,
                'nama' => 'JAVA JAZZ EXPERIENCE 2025',
                'lokasi' => 'Grand Hall JCC Senayan, Jakarta',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '7-9 Maret 2025',
                'harga' => 450000,
                'thumbnail' => '/images/thumbnails/java-jazz-2025.jpg',
                'kategori' => 'ended',
                'tag' => 'Konser Musik • International Jazz Festival',
                'penyelenggara' => 'Java Festival Production',
                'organizer_badge' => 'Event Sukses',
                'waktu' => '15:00 - 23:30 WIB',
                'deskripsi' => "Selebrasi 3 hari mahakarya musik jazz kelas dunia di Grand Hall JCC Senayan yang telah dihadiri lebih dari 35.000 penikmat musik jazz tanah air dan internasional.",
                'syarat_ketentuan' => "Event telah selesai diselenggarakan pada 7-9 Maret 2025. Penjualan tiket resmi telah ditutup.",
                'categories' => [
                    ['name' => 'Daily Pass Friday', 'code' => 'D1', 'bib_code' => 'JJ1', 'price' => 450000],
                ],
            ],
            [
                'id' => 11,
                'nama' => 'JAKARTA INTERNATIONAL COFFEE FEST 2025',
                'lokasi' => 'JIExpo Kemayoran, Jakarta Pusat',
                'kota' => 'Jakarta Pusat',
                'tanggal' => '14-16 November 2025',
                'harga' => 50000,
                'thumbnail' => '/images/thumbnails/jakarta-coffee-fest-2025.jpg',
                'kategori' => 'ended',
                'tag' => 'Pameran & Expo • Kopi Nusantara',
                'penyelenggara' => 'Indonesian Coffee Community',
                'organizer_badge' => 'Event Sukses',
                'waktu' => '10:00 - 20:00 WIB',
                'deskripsi' => "Pameran industri kopi terbesar di Indonesia yang mempertemukan barista terbaik, roastery ternama, dan ribuan pecinta kopi nusantara.",
                'syarat_ketentuan' => "Event telah selesai diselenggarakan pada 14-16 November 2025. Penjualan tiket resmi telah ditutup.",
                'categories' => [
                    ['name' => 'Public Visitor Pass', 'code' => 'PUB', 'bib_code' => 'CF1', 'price' => 50000],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $eventsData = self::getEventsData();

        // 1. Simpan ke storage/app/events.json
        $eventsJson = array_map(function ($ev) {
            $copy = $ev;
            unset($copy['categories']);
            unset($copy['kota']);
            unset($copy['organizer_badge']);
            $copy['urlBeli'] = 'https://wa.me/6289681201941';
            if (empty($copy['slug']) && !empty($copy['nama'])) {
                $copy['slug'] = \Illuminate\Support\Str::slug($copy['nama']);
            }
            return $copy;
        }, $eventsData);

        file_put_contents(
            HomeController::getEventsPath(),
            json_encode(array_values($eventsJson), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // 2. Sinkronkan ke tabel database
        foreach ($eventsData as $ev) {
            $date = AdminController::parseDateString($ev['tanggal']);
            
            $dbEvent = Event::updateOrCreate(
                ['id' => $ev['id']],
                [
                    'title' => $ev['nama'],
                    'date' => $date,
                    'location' => $ev['lokasi'],
                    'quota' => 5000,
                ]
            );

            // Simpan atau update kategori tiket
            if (!empty($ev['categories'])) {
                // Bersihkan kategori lama event ini jika ada
                EventCategory::where('event_id', $dbEvent->id)->delete();
                
                foreach ($ev['categories'] as $cat) {
                    EventCategory::create([
                        'event_id' => $dbEvent->id,
                        'name' => $cat['name'],
                        'code' => $cat['code'],
                        'bib_code' => $cat['bib_code'],
                        'price' => $cat['price'],
                    ]);
                }
            }

            // Buat rekening pembayaran resmi jika belum ada
            if (EventPaymentAccount::where('event_id', $dbEvent->id)->count() === 0) {
                EventPaymentAccount::create([
                    'event_id' => $dbEvent->id,
                    'bank_name' => 'BCA',
                    'account_number' => '8012398471',
                    'account_holder' => 'PT SeTiket Kreasi Indonesia',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);

                EventPaymentAccount::create([
                    'event_id' => $dbEvent->id,
                    'bank_name' => 'Mandiri',
                    'account_number' => '1370098231456',
                    'account_holder' => 'SeTiket Event Management',
                    'is_active' => true,
                    'sort_order' => 2,
                ]);
            }

            // Pastikan form fields bawaan tersedia
            EventFormField::ensureCoreFields($dbEvent->id);
        }
    }
}
