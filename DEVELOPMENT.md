# DEVELOPMENT.md — SeTiket

Panduan teknis untuk developer yang mengerjakan project ini. Dokumen ini menjelaskan cara kerja internal aplikasi, bukan cara memakainya sebagai user.

> Untuk memasang dan menjalankan project, lihat [README.md](README.md).
> Untuk peragaan fitur dari sisi pengguna (skenario langkah demi langkah), lihat [DEMO.md](DEMO.md).

---

## 1. Ringkasan

**SeTiket** adalah platform penjualan tiket event lari/festival (fun run) dengan alur pembayaran manual (transfer + upload bukti) dan verifikasi oleh admin. Ada tiga bagian besar:

| Bagian | Akses | Fungsi |
|---|---|---|
| **Landing publik** | Tanpa login | Daftar event, detail event, lihat/unduh e-ticket |
| **Area peserta** | Login `user` | `/dashboard` untuk riwayat pesanan, `/tickets` untuk e-ticket aktif saja |
| **Panel admin** | Login `admin` | Kelola peserta, verifikasi pesanan, atur formulir pendaftaran event-nya, scan QR check-in, export CSV |
| **Panel super admin** | Login `super_admin` | Semua fitur admin + kelola event, kategori event, dan akun admin |

Nama internal repo adalah `funrun`, tetapi branding produknya **SeTiket**.

---

## 2. Stack

| Komponen | Versi / Paket |
|---|---|
| PHP | ^8.3 (dev pakai 8.3.6, Laragon) |
| Framework | Laravel ^13.8 |
| Autentikasi | Laravel Breeze ^2.4 (stack Blade) |
| Database | MySQL (nama DB: `funrun`) |
| Build tool | Vite ^8 + laravel-vite-plugin ^3.1 |
| CSS | Tailwind CSS ^4.3 (via `@tailwindcss/vite`) |
| Templating | Blade (tanpa Livewire/Inertia/Vue/React) |
| PDF | `barryvdh/laravel-dompdf` ^3.1 |
| Testing | PHPUnit ^12.5 |
| Linting | Laravel Pint ^1.27 |

Frontend murni Blade + Tailwind + JavaScript vanilla. **Tidak ada** Alpine.js, tidak ada SPA, tidak ada API endpoint (kecuali satu route scan yang mengembalikan JSON).

---

## 3. Setup dari clone

```bash
composer install
```

```bash
npm install
```

```bash
cp .env.example .env
```

Isi bagian database di `.env` (`.env.example` sengaja mengosongkan kredensial DB):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=funrun
DB_USERNAME=root
DB_PASSWORD=root
```

```bash
php artisan key:generate
```

Buat database kosong bernama `funrun` (via phpMyAdmin/HeidiSQL/Laragon), lalu:

```bash
php artisan migrate --seed
```

```bash
php artisan storage:link
```

**`storage:link` wajib.** Semua bukti pembayaran dan thumbnail event disimpan di `storage/app/public` dan diakses lewat `/storage/...`. Tanpa symlink, gambar tidak muncul di panel admin.

Terakhir jalankan dev server:

```bash
composer run dev
```

Perintah itu menjalankan `php artisan serve` + `queue:listen` + `npm run dev` sekaligus lewat `concurrently`. Kalau memakai virtual host Laragon, cukup `npm run dev` saja.

### Akun default

| Email | Password | Role |
|---|---|---|
| `superadmin@setiket.com` | `admin123` | `super_admin` |
| `admin@setiket.com` | `admin123` | `admin` |
| `test@example.com` | `password` | `user` |

Super admin dibuat oleh **migration** `2026_06_25_143614_...` (bukan seeder), jadi selalu ada setelah `migrate`. Admin dan user contoh dibuat oleh seeder, jadi hanya ada kalau dijalankan dengan `--seed`.

Semua role login lewat satu pintu yang sama: **`/login`**. Lihat §8.

### Catatan lingkungan

- `create_db.php` di root memakai password MySQL kosong. Kalau MySQL Anda berpassword, script itu gagal — buat database manual saja.
- `Promosi_SeTiket.docx`, `Promosi_SeTiket_V2.docx`, `diagram_usecase.html`, dan `public/titix.html` adalah dokumen pendukung/mockup, bukan bagian dari aplikasi yang berjalan.

### Jebakan: aset tidak termuat (halaman tampil tanpa CSS)

`vite.config.js` menyetel `server.host: '0.0.0.0'`. laravel-vite-plugin menulis host itu apa adanya ke `public/hot`, sehingga browser diminta memuat aset dari `http://0.0.0.0:5173` — alamat bind, bukan alamat yang bisa dihubungi klien. Akibatnya CSS dan JS gagal total dan halaman tampil polos.

Karena itu `server.hmr.host` di-set ke `localhost` (di `resolveDevServerUrl` plugin, urutan prioritasnya `hmr.host ?? sailHost ?? server.host ?? alamat`). Binding `0.0.0.0` tetap dipertahankan agar server masih mendengarkan semua interface.

Kalau perlu tes dari HP di jaringan yang sama, ganti `hmr.host` ke IP LAN laptop — `public/hot` hanya bisa memuat satu URL.

Kalau Vite mati paksa dan `public/hot` tertinggal, halaman akan tetap mencoba memuat dari dev server yang sudah tidak ada. Hapus filenya:

```bash
rm public/hot
```

Tanpa `hot`, Laravel otomatis memakai hasil build di `public/build`.

---

## 4. Perintah harian

```bash
composer run dev
```

```bash
npm run build
```

```bash
php artisan migrate:fresh --seed
```

```bash
composer run test
```

```bash
./vendor/bin/pint
```

---

## 5. Arsitektur data — bagian terpenting

Project ini punya **dua sumber data untuk event**, dan ini sumber kebingungan terbesar saat mengembangkannya.

### 5.1 Event hidup di JSON, bukan di database

Data event yang tampil di landing page dibaca dari file:

```
storage/app/events.json
```

Dikelola lewat `HomeController::loadEvents()`, `saveEvents()`, dan `defaultEvents()`. Kalau file belum ada, `loadEvents()` otomatis membuatnya berisi 12 event contoh dari `defaultEvents()`.

File ini **tidak masuk git** (`storage/app/.gitignore` mengabaikan semua isi kecuali `private/`, `public/`, dan `.gitignore`). Jadi setiap developer yang clone akan mendapat 12 event bawaan itu, dan perubahan event di mesin Anda tidak ikut ter-commit.

Skema satu entri `events.json` (perhatikan: field berbahasa Indonesia):

```json
{
  "id": 1,
  "nama": "VOLT RHYTHM 2026",
  "lokasi": "Lapangan Yonif Mekanis, Jakarta",
  "tanggal": "25 Juli 2026",
  "harga": 100000,
  "thumbnail": "/storage/thumbnails/xxx.webp",
  "kategori": "upcoming",
  "urlBeli": "https://wa.me/6289681201941",
  "waktu": "16.00 - 23.00",
  "deskripsi": "...",
  "syarat_ketentuan": "...",
  "payment_methods": [
    { "name": "Transfer Bank BCA", "account_number": "...", "account_holder": "..." }
  ]
}
```

`kategori` hanya menerima `upcoming` atau `highlight` — ini menentukan di baris mana event muncul di landing page, bukan kategori lari.

### 5.2 Tabel `events` adalah cermin, bukan sumber

Tabel `events` di database ada supaya foreign key (`participants.event_id`, `users.event_id`, `event_categories.event_id`) punya sasaran. Isinya disinkronkan **satu arah**: JSON → DB.

Sinkronisasi terjadi di `AdminController::syncEventsToDatabase()`, yang dipanggil dari **constructor `AdminController`** — artinya berjalan di setiap request ke halaman admin manapun. Selain itu `RegistrationController` juga memanggil `Event::firstOrCreate()` sebelum menyimpan peserta.

Konsekuensi praktis:

- Menambah/mengubah event **harus** lewat `/admin/events` (super admin) agar JSON dan DB ikut ter-update. Mengedit tabel `events` langsung akan tertimpa pada request admin berikutnya.
- Field yang ikut tersinkron hanya `title`, `date`, `location`, `quota`, `payment_methods`. `quota` **selalu di-hardcode 5000** dan tidak pernah divalidasi terhadap jumlah peserta — belum ada pembatasan kuota.
- `tanggal` di JSON adalah teks bebas berbahasa Indonesia ("25-26 Juli 2026"). `AdminController::parseDateString()` menerjemahkannya ke `Y-m-d`; untuk rentang tanggal, yang dipakai adalah tanggal **akhir**. Kalau gagal parse, fallback-nya `2026-09-15`.

### 5.3 Formulir pendaftaran hidup di database, per event

Data apa saja yang harus diisi peserta **tidak sama untuk semua event**. Definisinya ada di tabel `event_form_fields`, satu baris per field, dan diatur admin lewat `/admin/form-fields`. Lihat §7.1.

### 5.4 Kategori lomba (3K/5K/10K) hidup di database

Berbeda dengan event, kategori lomba disimpan di tabel `event_categories` dan dikelola di `/admin/events/{id}/categories`. Kategori inilah yang menentukan **harga** dan **kode BIB**.

Kategori default (3K Fun Walk / 5K Night Run / 10K Challenger) di-seed secara *lazy* di **tiga tempat berbeda** — kalau sebuah event belum punya kategori sama sekali, kategori default dibuat otomatis oleh:

1. Migration `2026_06_25_162648_create_event_categories_table.php`
2. `RegistrationController::showRegistrationForm()`
3. `AdminController::eventCategories()`

Kalau menambah tempat ketiga yang butuh kategori, ikuti pola yang sama atau kategori akan kosong.

Perhatikan juga: `events.harga` di JSON **tidak dipakai untuk transaksi**. Harga yang ditagihkan selalu diambil dari `event_categories.price`. `harga` di JSON hanya angka "mulai dari" untuk tampilan landing page.

---

## 6. Skema database

```
users ──┬──< participants >──┬── events
        │         │           │
        │         └──< tickets >── orders
        │                          
        └── event_id ──────────── events
                                    │
                                    └──< event_categories
```

**users** — `id, name, email, password, role, remember_token, event_id, timestamps`
`role` bernilai `user` | `admin` | `super_admin` (default `user`). Nilai lama `participant` sudah dinormalkan menjadi `user` oleh migration `2026_07_28_235000_normalize_user_roles`. `event_id` hanya diisi untuk `admin`, menandai event mana yang boleh dia kelola; `super_admin` membiarkannya `null`.

**events** — `id, title, date, location, quota, payment_methods (text, cast array), timestamps`

**event_categories** — `id, event_id, name, code, bib_code, price, timestamps`
`code` = kode kategori (`3K`), `bib_code` = kode BIB (`FW`), keduanya masuk ke format kode tiket.

**participants** — `id, user_id, event_id, fullname, nik, phone, dob, gender, address, city, medical_history, jersey_size, emergency_contact, category, custom_data, timestamps`
`category` menyimpan **`code`** kategori sebagai string (misal `"5K"`), bukan foreign key ke `event_categories`. Mengganti `code` sebuah kategori akan memutus kaitan peserta lama.
`custom_data` adalah kolom JSON berisi jawaban field tambahan, dikunci dengan `event_form_fields.key`.
Kolom `dob`, `gender`, `address`, `city`, `medical_history`, `jersey_size`, `emergency_contact` semuanya **nullable** — semuanya bisa dimatikan admin, jadi tidak boleh ada yang `NOT NULL`. (`city` semula `NOT NULL`; diubah oleh migration `2026_07_29_020000`.)

**orders** — `id, order_code (unique), user_id, event_id, total_amount, payment_method, payment_status, proof_of_payment, rejection_reason, verified_by, verified_at, timestamps`
Satu pesanan = satu kali transfer, berisi satu atau banyak tiket. `payment_status`: `waiting_verification` → `paid` | `rejected`. `event_id` disimpan langsung di sini supaya penyaringan admin per-event tidak perlu `whereHas` berlapis.

**event_form_fields** — `id, event_id, key, label, type, is_core, enabled, required, placeholder, help_text, sort_order, timestamps`
Definisi formulir pendaftaran milik satu event. `key` unik per event. Lihat §7.1.

**tickets** — `id, participant_id, order_id, ticket_code (unique), qr_code, status, timestamps`
`status`: `pending` → `valid` → `checked-in`.

Tabel `payments` **sudah tidak ada** — digantikan `orders` oleh migration `2026_07_29_010000_create_orders_table`, yang memindahkan baris lama menjadi pesanan berisi satu tiket sebelum tabelnya dibuang. `down()` mengembalikannya.

Semua model memakai `protected $guarded = []` (mass assignment terbuka penuh) kecuali `User` yang memakai atribut PHP `#[Fillable([...])]`.

---

## 7. Alur bisnis end-to-end

Ini alur utama yang harus dipahami sebelum menyentuh controller manapun.

### 7.1 Formulir pendaftaran per event

Data yang diminta dari peserta ditentukan per event lewat tabel `event_form_fields`, diatur di **`/admin/form-fields`**. Admin hanya bisa mengatur event yang ditugaskan kepadanya; super admin bisa memilih event mana pun lewat dropdown.

Ada tiga lapis:

| Lapis | Contoh | Bisa diatur admin? |
|---|---|---|
| **Terkunci** | `fullname`, `nik`, `phone`, `category` | Tidak. Sistem bergantung padanya: NIK mencegah tiket ganda, WhatsApp untuk kirim e-ticket, kategori menentukan harga & kode BIB |
| **Bawaan** (`is_core = true`) | `dob`, `gender`, `jersey_size`, `emergency_contact`, `address`, `city`, `medical_history` | Label, wajib/opsional, aktif/nonaktif, urutan. **Tipe tidak bisa diubah** karena kolomnya sudah tetap di tabel `participants` |
| **Tambahan** (`is_core = false`) | "Nama Komunitas Lari", "Saya menyatakan sehat" | Semuanya, termasuk tipe dan penghapusan |

Tipe untuk field tambahan: `text`, `textarea`, `number`, `date`, `consent`. Jawabannya masuk ke `participants.custom_data` (JSON).

`EventFormField::ensureCoreFields($eventId)` membuat baris bawaan yang belum ada, dipanggil saat form dibuka maupun sebelum validasi — jadi event lama yang dibuat sebelum fitur ini tetap punya konfigurasi standar tanpa migrasi data.

**Alur datanya:**

1. `EventFormField::activeFor($eventId)` mengembalikan field aktif terurut
2. `register.blade.php` merender tiap field lewat `partials/form-field.blade.php`. Field bawaan memakai nama `participants[i][key]`, field tambahan `participants[i][custom][key]` supaya tidak bertabrakan
3. `RegistrationController::submitRegistration()` **menyusun aturan validasi dari konfigurasi itu**, bukan dari daftar tetap. `rulesForField()` menerjemahkan tipe + wajib jadi aturan Laravel
4. Saat menyimpan, hanya field yang aktif yang ikut ditulis; field bawaan ke kolomnya masing-masing, field tambahan ke `custom_data`

Karena aturan validasi disusun dari database, `event_id` **harus divalidasi lebih dulu secara terpisah** di awal `submitRegistration()`. Tanpa itu, `event_id` palsu membuat `ensureCoreFields()` menulis baris dengan foreign key yang tidak ada dan request gagal dengan integrity violation, bukan pesan validasi yang wajar.

Menghapus field tambahan tidak menghapus jawaban yang sudah masuk — datanya tetap di `custom_data`, hanya tidak lagi ditanyakan dan tidak lagi muncul di CSV.

Jawaban field tambahan muncul di halaman edit peserta dan di export CSV (satu kolom per field; peserta dari event lain dibiarkan kosong).

### 7.2 Alur pembelian

**0. Pembeli login** — seluruh alur pembelian berada di balik middleware `['auth', 'role:user']`.
Guest yang menekan "Beli Tiket Sekarang" diarahkan ke `/login`, lalu dikembalikan ke form pembelian oleh mekanisme *intended URL* Laravel. Admin dan super admin **tidak bisa** membeli tiket (403) — akun mereka untuk mengelola, bukan memesan.

**1. Peserta membuka form** — `GET /register-event?event_id=N` → `RegistrationController::showRegistrationForm()`
Menampilkan form beserta kategori dan metode pembayaran milik event tersebut. Nama diisi otomatis dari akun, dan email dikunci (`readonly`) ke email akun agar jelas pesanan tercatat ke akun mana. Kalau event belum ada di DB, dibuat di sini.

**2. Submit pesanan** — `POST /register-event` → `submitRegistration()`
Form mengirim array `participants[]`, satu entri per tiket. Yang dipakai bersama untuk seluruh pesanan hanya `event_id`, `payment_method`, dan satu file `proof`.

1. Validasi. Selain aturan per peserta (NIK 16 digit angka, jersey, kategori), ada tiga yang menjaga integritas pesanan:
   - `event_id` wajib `exists:events,id` — datang dari input tersembunyi, bisa diubah pembeli
   - `participants.*.nik` memakai **`distinct`** — NIK tidak boleh berulang di dalam satu pesanan
   - `rejectNiksAlreadyRegistered()` menolak NIK yang sudah terdaftar di event itu dari pesanan sebelumnya
2. Pemilik pesanan diambil dari `$request->user()` — selalu ada karena route dilindungi `role:user`
3. `resolveEvent()` memastikan event ada di DB beserta kategori bawaannya
4. Dalam satu `DB::transaction()`: buat `Order`, lalu untuk tiap peserta buat `Participant` + `Ticket` berstatus `pending`
5. `total_amount` dijumlah dari `event_categories.price` tiap peserta — jadi kategori boleh berbeda-beda dalam satu pesanan
6. Bukti bayar disimpan sekali ke `storage/app/public/proofs` dan menempel di `Order`, bukan di tiap tiket

Nama boleh sama (kembar atau nama umum memang bisa sama); **NIK** yang wajib berbeda, karena itu identitas resmi yang mencegah satu orang mendapat dua tiket di event yang sama. NIK yang sama tetap boleh mendaftar di event berbeda.

Batasnya `RegistrationController::MAX_TICKETS_PER_ORDER` (10).

`event_id` datang dari input tersembunyi di form, jadi validasi `exists` bukan formalitas: tanpa itu pembeli bisa mengirim `event_id` sembarang dan pesanannya tidak masuk ke antrian admin mana pun.

**2b. Pesanan masuk ke antrian admin event tersebut**
Tidak ada tabel penugasan terpisah. Perutean terjadi lewat pencocokan dua kolom:

```
participants.event_id  ==  users.event_id   (untuk user dengan role 'admin')
```

Super admin menetapkan `users.event_id` seorang admin lewat `/admin/admins`. Sejak itu, setiap query di `AdminController` menyaring dengan kolom tersebut:

| Halaman | Cara penyaringan |
|---|---|
| `orders()` | `where('event_id', $user->event_id)` — langsung, karena `orders` menyimpan `event_id` |
| `participants()` | `where('event_id', $user->event_id)` |
| `dashboard()` | KPI tiket/check-in memakai `whereHas`; pendapatan langsung dari `orders` |
| `pendingOrdersCount()` | Jumlah **pesanan** (bukan tiket) berstatus `waiting_verification` untuk event itu |
| `approveOrder()`, `rejectOrder()`, `scanTicket()`, `editParticipant()`, `downloadEticket()` | `abort(403)` kalau `event_id` tidak cocok |

Super admin melewati semua penyaringan ini dan melihat seluruh event.

Contoh: super admin men-set `admin@setiket.com` dengan `event_id = 1` (VOLT RHYTHM 2026). Semua pesanan untuk event 1 muncul di panelnya — lengkap dengan badge jumlah di menu **Pesanan** dan banner di dashboard — sementara pesanan event lain tidak terlihat sama sekali dan tidak bisa disetujui olehnya.

**3. Admin memverifikasi** — `POST /admin/orders/{id}/approve` → `AdminController::approveOrder()`
Satu approve untuk satu pesanan: `order.payment_status` → `paid`, lalu **seluruh** tiket di dalamnya diubah jadi `valid` dan diberi `qr_code` `QR-{ticket_code}-{uniqid()}`. Notifikasi WhatsApp dikirim per peserta (lihat §9). Kolom `verified_by` dan `verified_at` mencatat siapa yang menyetujui. **Sebelum di-approve, `qr_code` masih null dan tiket tidak bisa di-scan.**

Admin juga bisa **menolak** lewat `POST /admin/orders/{id}/reject` dengan alasan wajib. Status pesanan jadi `rejected`, alasannya tampil di dashboard pembeli, dan pembeli bisa mengunggah ulang bukti lewat `POST /orders/{order}/proof` yang mengembalikan status ke `waiting_verification`.

**4. Peserta membuka e-ticket** — `GET /ticket/{ticket_code}` atau unduh PDF di `/ticket/{ticket_code}/pdf`
Tidak ada autentikasi di sini: siapa pun yang tahu kode tiket bisa membukanya.

**5. Check-in di lokasi** — `/admin/scanner` → `POST /admin/scan`
Scan kamera memakai `html5-qrcode`, hasilnya dikirim via `fetch()` sebagai JSON. Endpoint menerima `qr_code` **maupun** `ticket_code` sehingga input manual tetap bisa. Tiket harus berstatus `valid`; sekali di-scan statusnya jadi `checked-in` dan scan kedua ditolak.

### Halaman peserta

| Route | Isi |
|---|---|
| `/dashboard` | Daftar **pesanan**: kode, jumlah tiket, status pembayaran, total, peserta di dalamnya |
| `/tickets` | Daftar **tiket saja** — hanya yang sudah `valid`/`checked-in`, dengan QR, tanpa info pembayaran |
| `/orders/{order}` | Detail satu pesanan + unggah ulang bukti kalau ditolak |

### Format kode tiket

```
ST-{event_id}-{category_code}-{bib_code}-{nomor urut 4 digit}
contoh: ST-1-5K-NR-0007
```

ID event **wajib** ada di dalam kode. `tickets.ticket_code` unik secara global, sementara nomor BIB dihitung ulang dari 1 di tiap event — tanpa ID event, peserta 5K pertama di event A dan event B menghasilkan kode identik dan penyimpanan gagal dengan integrity constraint violation.

Nomor urut dihitung dengan `COUNT` tiket pada event+kategori lalu `+1`, dan `nextTicketCode()` menaikkan nomornya sampai menemukan kode yang belum terpakai. Ini menutup tabrakan dari pesanan yang masuk bersamaan, walaupun untuk trafik tinggi tetap lebih baik memakai penomoran berbasis tabel sekuens.

### Kepemilikan pesanan

`OrderController::show()` dan `updateProof()` memeriksa `order->user_id === $request->user()->id` dan `abort(403)` kalau bukan miliknya, jadi pesanan orang lain tidak bisa dibuka atau diunggahi bukti sekalipun ID-nya ditebak.

---

## 8. Autentikasi dan peran

Autentikasi memakai **Laravel Breeze** (stack Blade). Ada tiga role, disimpan di kolom `users.role`:

| Role | Konstanta | Area | Cakupan |
|---|---|---|---|
| `user` | `User::ROLE_USER` | `/dashboard` | Hanya data miliknya sendiri |
| `admin` | `User::ROLE_ADMIN` | `/admin/*` | Terbatas pada `users.event_id` miliknya |
| `super_admin` | `User::ROLE_SUPER_ADMIN` | `/admin/*` | Seluruh event, plus kelola event & admin |

### Satu pintu login

Semua role masuk lewat `/login`. Pengarahan setelah login ditentukan oleh `User::homeRoute()`:

```php
public function homeRoute(): string
{
    return $this->isAdmin() ? route('admin.dashboard') : route('dashboard');
}
```

Method itu dipakai di tiga tempat sekaligus agar perilakunya konsisten:

1. `AuthenticatedSessionController::store()` — redirect setelah login berhasil.
2. Semua controller `Auth\*` lain yang aslinya hardcode `route('dashboard')` (konfirmasi password, verifikasi email). Tanpa perubahan ini, admin yang lewat jalur tersebut akan dilempar ke `/dashboard` dan kena 403.
3. `$middleware->redirectUsersTo()` di `bootstrap/app.php` — user yang sudah login lalu membuka `/login` atau `/register`.

Kalau menambah tujuan redirect baru, pakai `homeRoute()`, jangan `route('dashboard')`.

### Middleware role

`App\Http\Middleware\EnsureUserHasRole` terdaftar sebagai alias `role` di `bootstrap/app.php` dan menerima daftar role sebagai parameter:

```php
Route::middleware(['auth', 'role:user'])->group(...);              // area peserta
Route::middleware(['auth', 'role:admin,super_admin'])->group(...); // panel admin
```

Helper di model `User`: `hasRole(...$roles)`, `isUser()`, `isAdmin()` (true untuk admin **dan** super admin), `isSuperAdmin()`.

### Pengecekan yang masih manual

Dua lapis otorisasi tetap dilakukan di dalam `AdminController`, bukan lewat middleware:

- **`checkSuperAdmin()`** dipanggil manual di tiap method khusus super admin (manajemen event, kategori, admin) dan melempar 403.
- **Pembatasan per-event untuk `admin`** dengan pola `if ($user->role === 'admin' && $x->event_id !== $user->event_id) abort(403)`, diulang di hampir setiap method.

Tidak ada Policy atau Gate. Kalau menambah method admin baru, **kedua pengecekan itu harus ditulis ulang manual** — mudah terlupa, dan middleware `role:` tidak menangkapnya karena ia hanya tahu role, bukan kepemilikan event.

`bulkDestroy*` memakai `continue` alih-alih `abort` untuk item yang tidak berhak, jadi operasi massal melewati yang tidak diizinkan tanpa memberi tahu user.

### Catatan pemasangan Breeze

Breeze v2.4.2 stack Blade masih berbasis **Tailwind v3**, sedangkan project ini memakai **Tailwind v4** (CSS-first, lewat `@tailwindcss/vite`). Installer resminya (`php artisan breeze:install blade`) akan:

- menimpa `routes/web.php`, `vite.config.js`, `resources/css/app.css`, dan `resources/views/layouts/app.blade.php`;
- menurunkan `tailwindcss` ke `^3.1.0` di `package.json` serta menambah `tailwind.config.js` + `postcss.config.js`.

Karena itu scaffolding dipasang **selektif**: yang diambil hanya `app/Http/Controllers/Auth/*`, `ProfileController`, `app/Http/Requests/*`, `routes/auth.php`, dan test auth-nya. View auth ditulis ulang mengikuti tema SeTiket dengan `@extends('layouts.app')`, bukan memakai paradigma `<x-app-layout>` milik Breeze.

Konsekuensinya: **jangan pernah menjalankan `php artisan breeze:install`** di project ini — perintah itu akan menghapus routing dan setup Tailwind v4. Komponen Blade bawaan Breeze (`x-dropdown`, `x-modal`, dll.) juga tidak dipasang karena butuh Alpine.js, yang tidak dipakai project ini.

### Tabrakan nama route

Route pendaftaran **event** semula bernama `register`, bentrok dengan route pendaftaran **akun** milik Breeze. Karena `routes/auth.php` di-`require` paling akhir, Breeze-lah yang menang di lookup nama, sehingga tombol "Daftar Sekarang" akan mengarah ke form akun.

Route pendaftaran event kini bernama **`event.register`**. Jadi:

- `route('register')` → form pendaftaran akun (Breeze)
- `route('event.register')` → form pendaftaran event

---

## 9. Integrasi eksternal

| Layanan | Dipakai untuk | Catatan |
|---|---|---|
| **Fonnte** (`api.fonnte.com`) | Notifikasi WhatsApp saat pembayaran disetujui | Opsional |
| **api.qrserver.com** | Merender gambar QR untuk PDF | Wajib online saat unduh PDF |
| **unpkg.com** | `html5-qrcode` di halaman scanner | Wajib online saat check-in |
| **fonts.googleapis.com** | Font Outfit (publik) & Inter (admin) | Kosmetik |
| **SweetAlert2** | Dialog di panel admin | Kosmetik |

### Fonnte

Token dibaca dengan `env('FONNTE_TOKEN')` di `AdminController::approvePayment()`. Dua hal penting:

1. Key ini **tidak ada** di `.env` maupun `.env.example`. Tanpa token, pesan WA tidak dikirim — hanya ditulis ke `storage/logs/laravel.log` lewat `Log::info()`. Untuk mengaktifkan, tambahkan `FONNTE_TOKEN=...` ke `.env`.
2. `env()` dipanggil di luar `config/`, sehingga **nilainya jadi `null` begitu `php artisan config:cache` dijalankan** (lazimnya di production). Kalau notifikasi WA harus jalan di production, pindahkan ke `config/services.php` dan baca lewat `config()`.

### QR code

Gambar QR tidak digenerate lokal — diambil dari `api.qrserver.com`, di-encode base64, lalu ditempel ke PDF (DomPDF tidak bisa mengambil gambar remote sendiri). Ada dua lapis fallback: Laravel HTTP client, lalu `file_get_contents`. Kalau keduanya gagal, PDF tetap terunduh tapi tanpa QR.

Perlu diketahui: kedua pemanggilan itu mematikan verifikasi sertifikat SSL (`'verify' => false` dan `verify_peer => false`) — solusi umum untuk masalah sertifikat di Windows/Laragon, tapi tidak layak dibawa ke production.

Logika unduh PDF ini **diduplikasi** di `TicketController::downloadPdf()` (publik) dan `AdminController::downloadEticket()` (admin, dengan cek event). Perubahan pada satu harus diikutkan ke satunya.

---

## 10. Peta rute

### Publik

| Method | URI | Handler |
|---|---|---|
| GET | `/` | `HomeController@index` |
| GET | `/event/{id}` | `HomeController@showEvent` |
| GET | `/register-event` | `RegistrationController@showRegistrationForm` (nama: `event.register`) |
| POST | `/register-event` | `RegistrationController@submitRegistration` |
| GET | `/registration-success` | `RegistrationController@success` |
| GET | `/orders/{order}` | `OrderController@show` |
| POST | `/orders/{order}/proof` | `OrderController@updateProof` |
| GET | `/ticket/{ticket_code}` | `TicketController@showTicket` |
| GET | `/ticket/{ticket_code}/pdf` | `TicketController@downloadPdf` |
| GET | `/storage/{path}` | Closure fallback untuk file storage |

Route `/storage/{path}` di akhir `web.php` adalah fallback kalau symlink tidak tersedia (shared hosting). Route ini menyajikan **apa pun** di dalam `storage/app/public` tanpa autentikasi — termasuk semua bukti pembayaran, sama seperti symlink biasa.

### Auth (Breeze, dari `routes/auth.php`)

| Method | URI | Nama |
|---|---|---|
| GET/POST | `/login` | `login` |
| GET/POST | `/register` | `register` |
| POST | `/logout` | `logout` |
| GET/POST | `/forgot-password` | `password.request`, `password.email` |
| GET/POST | `/reset-password` | `password.reset`, `password.store` |
| GET/POST | `/confirm-password` | `password.confirm` |
| PUT | `/password` | `password.update` |

### Peserta & profil

| Method | URI | Middleware | Nama |
|---|---|---|---|
| GET | `/dashboard` | `auth`, `role:user` | `dashboard` |
| GET | `/tickets` | `auth`, `role:user` | `tickets` |
| GET | `/profile` | `auth` | `profile.edit` |
| PATCH | `/profile` | `auth` | `profile.update` |
| DELETE | `/profile` | `auth` | `profile.destroy` |

### Admin (`/admin`, middleware `auth` + `role:admin,super_admin`)

Peserta: `dashboard`, `participants`, `participants/{id}/edit|update|delete`, `participants/bulk-delete`, `export-csv`
Pesanan: `orders`, `orders/{id}/approve`, `orders/{id}/reject`, `orders/bulk-delete`
Formulir: `form-fields` + `store|update|destroy` — admin mengatur event yang dia tangani, super admin pilih event lewat `?event_id=`
Check-in: `scanner`, `scan`, `eticket/{ticket_code}/pdf`
Event *(super admin)*: `events` + `store|update|destroy|bulk-delete`
Kategori *(super admin)*: `events/{id}/categories` + `store|update|destroy`
Admin *(super admin)*: `admins` + `store|update|destroy|bulk-delete`

Tidak ada lagi `/admin/login` — login admin memakai `/login` yang sama dengan role lain.

---

## 11. Konvensi frontend

- Layout publik: `resources/views/layouts/app.blade.php` — tema gelap (`#0f172a`), font Outfit, efek *glassmorphism* lewat class `.glass-panel` dan `.gradient-text` yang didefinisikan inline di `<style>` layout.
- Layout admin: `resources/views/admin/layouts/admin.blade.php` — tema terang, font Inter, sidebar tetap.
- View auth (`resources/views/auth/*`), `dashboard.blade.php`, dan `profile/edit.blade.php` memakai `@extends('layouts.app')` mengikuti tema gelap SeTiket — **bukan** `<x-app-layout>` bawaan Breeze.
- Tailwind v4 dikonfigurasi lewat CSS (`resources/css/app.css`), **bukan** `tailwind.config.js`. Sumber scan tambahan dideklarasikan dengan `@source`.
- `resources/js/app.js` praktis kosong. Semua JavaScript ditulis inline di dalam Blade masing-masing halaman.
- Interaksi dinamis (pencarian navbar, modal form event, scanner) memakai `querySelector` + `addEventListener` biasa.
- View terbesar dan paling kompleks: `admin/events.blade.php` (446 baris) dan `register.blade.php` (403 baris).
- `admin/eticket-pdf.blade.php` dirender oleh DomPDF — CSS-nya harus konservatif (hindari flexbox/grid, gunakan tabel dan `float`).

---

## 12. Testing

35 test, semuanya lulus:

- `tests/Feature/Auth/*` dan `tests/Feature/ProfileTest.php` — bawaan Breeze (login, registrasi, reset password, verifikasi email, update profil).
- `tests/Feature/RoleAccessTest.php` — matriks akses tiga role: redirect setelah login per role, `user` ditolak di `/admin/*`, `admin` ditolak di `/dashboard` dan di halaman khusus super admin, guest diarahkan ke `/login`, registrasi publik selalu menghasilkan role `user`, dan dashboard peserta hanya menampilkan pendaftaran miliknya sendiri.
- `tests/Feature/TicketPurchaseFlowTest.php` (22 test) — alur beli tiket, multi-tiket, dan isolasi antar-event: pembelian wajib login, admin tidak bisa membeli, pesanan terikat akun pembeli, `event_id` palsu ditolak, satu pesanan berisi tiga tiket dengan kategori berbeda dan total yang benar, NIK kembar dalam satu pesanan ditolak, NIK yang sudah terdaftar di event itu ditolak, NIK sama boleh mendaftar di event lain, batas 10 tiket ditegakkan, admin event hanya melihat & hanya bisa menyetujui pesanan event-nya, satu approve menerbitkan seluruh tiket, penolakan wajib beralasan, `/tickets` hanya menampilkan tiket terbit milik sendiri, dan pesanan orang lain tidak bisa diakses.
- `tests/Feature/EventFormFieldTest.php` (21 test) — formulir per event: admin hanya bisa mengatur event sendiri, super admin bisa pilih event, field bawaan ter-seed otomatis, key custom tidak menabrak nama kolom bawaan, field bawaan tidak bisa dihapus, mematikan field bawaan menghilangkannya dari form dan validasi, field opsional boleh kosong, field wajib tetap memblokir, jawaban tersimpan di `custom_data`, persetujuan wajib harus dicentang, tipe angka menolak teks, konfigurasi terisolasi antar-event, dan jawaban ikut ke CSV.
- `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php` — bawaan Laravel.

Belum ada test untuk penerbitan PDF e-ticket dan notifikasi WhatsApp.

Saat test berjalan, `HomeController::getEventsPath()` mengarah ke `storage/app/events.testing.json` (lihat `app()->runningUnitTests()`), sehingga suite tidak membaca — apalagi menimpa — data event asli di `events.json`.

`phpunit.xml` menyetel `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`, jadi test tidak menyentuh database `funrun`.

```bash
composer run test
```

---

## 13. Konvensi kode yang berlaku di repo ini

Ikuti gaya yang sudah ada agar diff tetap konsisten:

- Controller memakai **FQCN inline** (`\App\Models\Participant::create(...)`) alih-alih `use` statement. Hanya `Request` dan `Pdf` yang di-import.
- Validasi ditulis langsung di controller dengan `$request->validate([...])` — tidak ada Form Request.
- Tidak ada Service/Repository layer; semua logika ada di controller.
- Pesan sukses/error dikirim lewat `->with('success'|'error', ...)` dan ditampilkan di layout.
- Bahasa campur: pesan untuk fitur lama berbahasa Inggris, fitur baru (event, kategori, admin) berbahasa Indonesia.
- Format kode: jalankan `./vendor/bin/pint` sebelum commit.

---

## 14. Hal yang perlu diwaspadai

Ringkasan utang teknis yang sudah terlihat dari kode, diurut dari yang paling mungkin menggigit:

1. **Bukti pembayaran dapat diakses publik.** File di `storage/app/public/proofs` bisa dibuka siapa saja yang tahu/menebak nama filenya, lewat symlink maupun route fallback `/storage/{path}`. Ini foto bukti transfer berisi data rekening.
3. **E-ticket tanpa autentikasi** — `/ticket/{ticket_code}` dan PDF-nya terbuka bagi siapa pun yang tahu kodenya, dan formatnya berurutan (`...-0001`, `-0002`) sehingga mudah ditebak.
4. **Verifikasi SSL dimatikan** pada pengambilan QR di dua tempat.
5. **`env()` di controller** membuat notifikasi WA mati diam-diam setelah `config:cache`.
6. **Race condition pada nomor tiket** (lihat §7).
7. **`quota` tidak pernah ditegakkan** — pendaftaran tidak akan berhenti walau kuota terlampaui.
8. **`App\Mail\TicketApproved` dan `resources/views/emails/ticket.blade.php` tidak pernah dipanggil.** Notifikasi berjalan lewat WhatsApp, bukan email. Kode ini sisa desain awal.
9. **`syncEventsToDatabase()` di constructor** membuat setiap request admin menulis ke tabel `events` sebanyak jumlah event — boros, dan menimpa perubahan manual di DB.
10. **Logika PDF terduplikasi** di `TicketController` dan `AdminController`.
11. **`down()` pada migration `add_proof_to_payments_table` kosong.** Tabel `payments` sekarang dibuang oleh migration `create_orders_table`, tapi kalau rollback dijalankan sampai ke migration itu, kolom `proof_of_payment` tidak ikut terhapus.
