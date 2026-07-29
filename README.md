# SeTiket

Platform penjualan tiket event lari (fun run) dengan pembayaran transfer manual dan verifikasi oleh panitia.

- **Peserta** membeli satu atau beberapa tiket sekaligus, mengunggah bukti transfer, lalu memantau statusnya sampai e-ticket terbit.
- **Admin event** memverifikasi pesanan yang masuk untuk event yang ditugaskan kepadanya, mengatur data apa saja yang diminta di formulir pendaftaran, dan memindai QR saat check-in.
- **Super admin** mengelola event, kategori lomba, dan akun admin.

Dibangun dengan Laravel 13, Blade, Tailwind CSS 4, dan Laravel Breeze untuk autentikasi.


## Kebutuhan

| Kebutuhan | Versi minimum | Dipakai saat dikembangkan |
|---|---|---|
| PHP | 8.3 | 8.3.6 |
| Composer | 2.x | 2.8.9 |
| Node.js | 20.x | 20.17.0 |
| npm | 10.x | 10.8.2 |
| MySQL | 5.7 / 8.x | bawaan Laragon |

Ekstensi PHP yang dibutuhkan sudah tersedia di paket standar Laragon/XAMPP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`.

---

## Cara menjalankan

### 1. Ambil kode dan pasang dependensi

```bash
git clone https://github.com/FirmanCandra/funrun.git
```

```bash
cd funrun
```

```bash
composer install
```

```bash
npm install
```

### 2. Siapkan file konfigurasi

```bash
cp .env.example .env
```

Buka `.env`. Bawaannya masih menunjuk ke SQLite dan baris MySQL-nya dikomentari:

```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Ganti seluruh bagian itu menjadi — perhatikan tanda `#` **harus dihapus**, dan sesuaikan `DB_USERNAME`/`DB_PASSWORD` dengan MySQL Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=funrun
DB_USERNAME=root
DB_PASSWORD=root
```

Project ini memakai MySQL. Kalau `DB_CONNECTION` dibiarkan `sqlite`, migrasi akan gagal karena file databasenya tidak ada.

### 3. Buat kunci aplikasi

```bash
php artisan key:generate
```

### 4. Buat database kosong

Buat database bernama **`funrun`** lewat phpMyAdmin, HeidiSQL, atau menu Database di Laragon. Nama harus sama dengan `DB_DATABASE` di `.env`.

### 5. Jalankan migrasi dan data awal

```bash
php artisan migrate --seed
```

Perintah ini membuat seluruh tabel sekaligus akun super admin, admin, dan peserta contoh.

### 6. Sambungkan folder penyimpanan

```bash
php artisan storage:link
```

**Langkah ini wajib.** Bukti transfer dan thumbnail event tersimpan di `storage/app/public` dan diakses lewat `/storage/...`. Tanpa symlink ini, gambar tidak akan muncul di panel admin.

### 7. Jalankan aplikasi

```bash
composer run dev
```

Satu perintah itu menjalankan tiga proses sekaligus: web server, antrean, dan Vite. Buka **http://127.0.0.1:8000**.

Kalau Anda memakai virtual host Laragon (misalnya `http://funrun.test`), server-nya sudah disediakan Laragon — cukup jalankan Vite saja:

```bash
npm run dev
```

---

## Akun bawaan

Semua peran masuk lewat halaman yang sama: **`/login`**.

| Peran | Email | Password | Sesudah login |
|---|---|---|---|
| Super Admin | `superadmin@setiket.com` | `admin123` | `/admin/dashboard` |
| Admin Event | `admin@setiket.com` | `admin123` | `/admin/dashboard` |
| Peserta | `test@example.com` | `password` | `/dashboard` |

Ganti password akun-akun ini sebelum dipakai di lingkungan sungguhan.

---

## Perintah yang sering dipakai

Menjalankan server, antrean, dan Vite sekaligus:

```bash
composer run dev
```

Build aset untuk produksi:

```bash
npm run build
```

Menjalankan seluruh test:

```bash
composer run test
```

Merapikan gaya penulisan kode:

```bash
./vendor/bin/pint
```

Mengulang database dari nol beserta data awal:

```bash
php artisan migrate:fresh --seed
```

---

## Kalau ada yang tidak beres

| Gejala | Penyebab | Cara mengatasi |
|---|---|---|
| Halaman tampil polos tanpa CSS | Vite mati tapi file penanda `public/hot` tertinggal | Hapus `public/hot`, atau jalankan `npm run dev` lagi |
| Gambar bukti transfer tidak muncul | Symlink storage belum dibuat | Jalankan `php artisan storage:link` |
| `SQLSTATE[HY000] [1049] Unknown database` | Database `funrun` belum dibuat | Ulangi langkah 4 |
| `SQLSTATE[HY000] [1045] Access denied` | `DB_USERNAME` / `DB_PASSWORD` di `.env` salah | Sesuaikan dengan kredensial MySQL Anda |
| Perubahan `.env` tidak terbaca | Konfigurasi masih ter-cache | Jalankan `php artisan config:clear` |
| QR code tidak muncul di tiket atau PDF | Gambar QR diambil dari layanan online `api.qrserver.com` | Pastikan ada koneksi internet |
| Notifikasi WhatsApp tidak terkirim | `FONNTE_TOKEN` belum diisi di `.env` | Wajar. Tanpa token, isi pesannya dicatat ke `storage/logs/laravel.log` |

Menghapus semua cache sekaligus kalau aplikasi berperilaku aneh:

```bash
php artisan optimize:clear
```

---

## Dokumentasi lain

| Berkas | Isi |
|---|---|
| [DEVELOPMENT.md](DEVELOPMENT.md) | Cara kerja internal: arsitektur data, skema database, alur bisnis, peran & hak akses, peta rute, konvensi kode |
| [DEMO.md](DEMO.md) | 13 skenario peragaan langkah demi langkah, lengkap dengan hasil yang diharapkan |

Sebelum menyentuh kode, **baca DEVELOPMENT.md §5 dan §7 lebih dulu**. Ada dua hal yang tidak biasa di project ini dan mudah menjebak kalau belum tahu:

1. Data event disimpan di file JSON (`storage/app/events.json`), bukan di tabel `events`. Tabel database hanya cerminannya.
2. Formulir pendaftaran berbeda-beda tiap event, dan aturan validasinya dibaca dari database — bukan daftar tetap di controller.
