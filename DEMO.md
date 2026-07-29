# DEMO.md — Skenario Demo SeTiket

Panduan peragaan aplikasi dari sisi pengguna. Tiap case berdiri sendiri: ada tujuan, langkah, dan hasil yang seharusnya muncul — jadi bisa dipakai untuk presentasi, uji terima, atau sekadar memastikan semuanya masih jalan setelah perubahan.

Untuk penjelasan teknis cara kerja internalnya, lihat [DEVELOPMENT.md](DEVELOPMENT.md).

---

## Persiapan

Jalankan aplikasi:

```bash
composer run dev
```

Buka `http://127.0.0.1:8000` (atau virtual host Laragon Anda).

### Akun yang dipakai

| Peran | Email | Password | Menangani |
|---|---|---|---|
| Super Admin | `superadmin@setiket.com` | `admin123` | Semua event |
| Admin Event | `admin@setiket.com` | `admin123` | VOLT RHYTHM 2026 (event 1) |
| Peserta | `test@example.com` | `password` | — |

Semua masuk lewat **satu halaman yang sama**: `/login`. Tidak ada halaman login terpisah untuk admin.

### Siapkan bukti transfer palsu

Beberapa case butuh file gambar untuk diunggah. Siapkan satu file JPG/PNG apa saja (maks 2 MB) — screenshot apa pun bisa.

### Mengulang demo dari awal

Menghapus seluruh pesanan, peserta, tiket, dan pertanyaan tambahan, tanpa menyentuh event dan akun:

```bash
php artisan tinker --execute="foreach (\App\Models\Order::with('tickets.participant')->get() as \$o) { foreach (\$o->tickets as \$t) { \$t->participant?->delete(); \$t->delete(); } if (\$o->proof_of_payment) { \Illuminate\Support\Facades\Storage::disk('public')->delete(\$o->proof_of_payment); } \$o->delete(); } \App\Models\EventFormField::query()->delete(); echo 'Data demo dibersihkan';"
```

Kalau ingin benar-benar dari nol (termasuk event dan akun):

```bash
php artisan migrate:fresh --seed
```

---

## Alur demo singkat (± 10 menit)

Kalau waktunya terbatas, jalankan Case **2 → 3 → 5 → 7 → 8 → 10 → 11**. Rangkaian itu menunjukkan keseluruhan siklus: super admin menyiapkan event, admin event menyesuaikan formulirnya, peserta memesan beberapa tiket, pesanan mendarat ke admin yang tepat, diverifikasi, tiket terbit, lalu check-in di lokasi.

---

## Case 1 — Satu pintu login, tiga tujuan berbeda

**Tujuan:** menunjukkan bahwa peran menentukan ke mana orang diarahkan, tanpa halaman login terpisah.

**Langkah:**

1. Buka `/login`, masuk sebagai `test@example.com` / `password`
2. Perhatikan halaman yang terbuka, lalu Logout
3. Ulangi dengan `admin@setiket.com` / `admin123`, lalu Logout
4. Ulangi dengan `superadmin@setiket.com` / `admin123`

**Hasil yang diharapkan:**

| Masuk sebagai | Diarahkan ke | Yang terlihat |
|---|---|---|
| Peserta | `/dashboard` | "Pesanan Saya" |
| Admin Event | `/admin/dashboard` | Panel admin, header menampilkan "Event: VOLT RHYTHM 2026" |
| Super Admin | `/admin/dashboard` | Panel admin + menu Manajemen Event & Manajemen Admin |

**Poin yang ditonjolkan:** menu sidebar admin biasa **tidak** memuat Manajemen Event dan Manajemen Admin — itu hanya milik super admin.

---

## Case 2 — Super admin menyiapkan event dan menugaskan admin

**Tujuan:** memperlihatkan bagaimana sebuah event lahir dan siapa yang bertanggung jawab atasnya.

**Login sebagai:** Super Admin

**Langkah:**

1. Masuk **Manajemen Event** → **Tambah Event**
2. Isi:
   - Nama: `DEMO NIGHT RUN 2026`
   - Lokasi: `Alun-Alun Kota`
   - Tanggal: `12 Desember 2026`
   - Waktu: `16.00 - 22.00`
   - Harga: `120000` (hanya angka "mulai dari" untuk kartu di beranda)
   - Kategori tampilan: `upcoming`
   - Metode pembayaran: minimal satu baris — nama, nomor rekening, atas nama
3. Simpan
4. Pada baris event yang baru, buka **Kategori**
5. Tambah dua kategori:

   | Nama | Kode | Kode BIB | Harga |
   |---|---|---|---|
   | 5K Fun Run | `5K` | `FR` | `120000` |
   | 10K Race | `10K` | `RC` | `200000` |

6. Masuk **Manajemen Admin** → tambah admin baru:
   - Nama: `Admin Demo Night Run`
   - Email: `admin.demo@setiket.com`
   - Password: `admin123`
   - Event: `DEMO NIGHT RUN 2026`

**Hasil yang diharapkan:**

- Event baru muncul di beranda publik (`/`) pada baris *Upcoming*
- Kategori tersimpan dengan harga masing-masing
- Admin baru terdaftar dengan kolom Event terisi `DEMO NIGHT RUN 2026`

**Poin yang ditonjolkan:** kategori inilah yang menentukan **harga yang ditagihkan** dan **kode BIB** pada tiket. Angka "harga" di form event hanya untuk tampilan kartu.

---

## Case 3 — Admin menyesuaikan formulir pendaftaran event-nya

**Tujuan:** menunjukkan bahwa data yang diminta ke peserta berbeda-beda tergantung event.

**Login sebagai:** `admin.demo@setiket.com` / `admin123` (admin event DEMO NIGHT RUN)

**Langkah:**

1. Buka menu **Formulir Pendaftaran**
2. Perhatikan tiga bagiannya:
   - **Selalu ditanyakan** — Nama, NIK, WhatsApp, Kategori (terkunci)
   - **Data bawaan** — bisa diatur aktif/nonaktif dan wajib/opsional
   - **Pertanyaan tambahan** — masih kosong
3. Pada data bawaan, **matikan** centang "Aktif" untuk **Alamat Lengkap**, lalu Simpan
4. Pada **Riwayat Penyakit**, centang "Wajib", lalu Simpan
5. Tambah pertanyaan baru:
   - Pertanyaan: `Nama Komunitas Lari`
   - Tipe: `Teks singkat`
   - Keterangan bantu: `Kosongkan bila tidak tergabung komunitas`
   - Wajib: tidak dicentang
6. Tambah satu lagi:
   - Pertanyaan: `Saya menyatakan sehat dan siap mengikuti lomba`
   - Tipe: `Persetujuan (centang)`
   - Wajib: **dicentang**

**Hasil yang diharapkan:**

- Daftar field menampilkan Alamat Lengkap dalam keadaan nonaktif
- Dua pertanyaan tambahan muncul di bagian bawah

**Verifikasi silang — buka dua form berdampingan:**

1. Buka `/register-event?event_id=<id DEMO NIGHT RUN>` → **tidak ada** Alamat Lengkap, **ada** Nama Komunitas dan kotak persetujuan
2. Buka `/register-event?event_id=1` (VOLT RHYTHM) → **ada** Alamat Lengkap, **tidak ada** pertanyaan tambahan

**Poin yang ditonjolkan:** aturan validasinya ikut berubah, bukan cuma tampilannya. Field yang dimatikan benar-benar tidak divalidasi dan tidak disimpan.

---

## Case 4 — Peserta membeli satu tiket

**Tujuan:** alur pembelian paling dasar.

**Login sebagai:** Peserta

**Langkah:**

1. Dari beranda, klik sebuah event → **Beli Tiket Sekarang**
2. Isi data peserta. Perhatikan Email sudah terkunci ke akun yang login
3. Klik **Lanjutkan ke Pembayaran**
4. Pilih metode pembayaran, unggah gambar bukti transfer
5. Klik **Konfirmasi Pembayaran**

**Hasil yang diharapkan:**

- Halaman "Pesanan Berhasil Dibuat!" menampilkan kode pesanan (`ORD-20261212-XXXXXX`), total, dan daftar tiket
- Tombol **Lihat Status Pesanan Saya** membawa ke `/dashboard`
- Di dashboard, pesanan berstatus **Menunggu Verifikasi**
- Buka `/tickets` → masih kosong, dengan keterangan tiket muncul setelah pembayaran diverifikasi

**Poin yang ditonjolkan:** bukti transfer diunggah **saat memesan**, bukan di halaman terpisah. Tiket belum terbit sebelum panitia memverifikasi.

---

## Case 5 — Beli beberapa tiket sekaligus, kategori boleh berbeda

**Tujuan:** pemesanan rombongan dengan satu kali transfer.

**Login sebagai:** Peserta

**Langkah:**

1. Buka form pembelian sebuah event
2. Isi Peserta 1, lalu klik **+ Tambah Peserta** dua kali sehingga ada tiga blok
3. Isi ketiganya dengan **NIK yang berbeda**, dan pilih kategori berbeda-beda, misal:

   | Peserta | Kategori |
   |---|---|
   | Budi Santoso | 5K |
   | Ani Lestari | 10K |
   | Citra Dewi | 5K |

4. Perhatikan panel **Ringkasan Pembelian** di kanan saat mengetik dan mengganti kategori
5. Lanjutkan ke pembayaran, unggah satu bukti transfer, konfirmasi

**Hasil yang diharapkan:**

- Ringkasan menampilkan tiga baris tiket dengan harga masing-masing, dan **Total** = jumlah ketiganya
- Setelah submit, satu kode pesanan berisi **tiga kode tiket** yang berbeda
- Di dashboard: satu kartu pesanan bertuliskan "3 tiket"

**Poin yang ditonjolkan:** tiga tiket, tiga peserta, tiga kode BIB, tapi **satu pesanan dan satu bukti transfer**. Admin nanti cukup memverifikasi sekali.

---

## Case 6 — Data peserta wajib berbeda

**Tujuan:** menunjukkan penjagaan agar satu orang tidak mendapat dua tiket di event yang sama.

**Login sebagai:** Peserta

**Langkah A — NIK kembar dalam satu pesanan:**

1. Buka form pembelian, tambah satu peserta sehingga ada dua blok
2. Isi keduanya dengan **NIK yang sama persis**
3. Klik **Lanjutkan ke Pembayaran**

**Hasil:** muncul peringatan *"NIK … dipakai lebih dari satu peserta"* dan form tidak lanjut. Kalau pengecekan sisi browser dilewati, server tetap menolak dengan pesan **"NIK tiap peserta harus berbeda."**

**Langkah B — NIK yang sudah terdaftar sebelumnya:**

1. Buat pesanan baru pada event yang sama
2. Isi dengan NIK yang sudah dipakai di Case 4 atau 5

**Hasil:** ditolak dengan **"NIK ini sudah terdaftar pada event tersebut."**

**Langkah C — NIK sama, event berbeda:**

1. Pakai NIK yang sama, tapi pesan tiket untuk event lain

**Hasil:** **berhasil**. Satu orang boleh ikut banyak event, hanya tidak boleh dobel di event yang sama.

**Poin yang ditonjolkan:** nama boleh sama (nama umum atau kembar memang bisa sama), NIK yang tidak boleh.

---

## Case 7 — Pesanan mendarat ke admin yang menanganinya

**Tujuan:** inti pemisahan tanggung jawab antar-event.

**Persiapan:** pastikan sudah ada pesanan untuk **dua event berbeda** (ulangi Case 4 untuk event 1 dan untuk DEMO NIGHT RUN).

**Langkah:**

1. Login sebagai `admin@setiket.com` (VOLT RHYTHM) → buka **Pesanan**
2. Catat pesanan apa saja yang terlihat
3. Logout, login sebagai `admin.demo@setiket.com` (DEMO NIGHT RUN) → buka **Pesanan**
4. Logout, login sebagai Super Admin → buka **Pesanan**

**Hasil yang diharapkan:**

| Login sebagai | Pesanan yang terlihat |
|---|---|
| Admin VOLT RHYTHM | Hanya pesanan VOLT RHYTHM |
| Admin DEMO NIGHT RUN | Hanya pesanan DEMO NIGHT RUN |
| Super Admin | Seluruhnya, dengan kolom nama event |

Perhatikan juga:

- Menu **Pesanan** di sidebar punya **badge angka** berisi jumlah pesanan yang menunggu verifikasi — angkanya berbeda untuk tiap admin
- Dashboard admin menampilkan banner *"N pesanan menunggu verifikasi — Pesanan masuk untuk event ⟨nama event⟩"*
- Angka **Total Participants** dan **Total Revenue** juga hanya menghitung event yang bersangkutan

**Poin yang ditonjolkan:** tidak ada pengaturan rute manual. Penugasan admin ke sebuah event (Case 2) yang menentukan semuanya.

---

## Case 8 — Admin memverifikasi, seluruh tiket terbit sekaligus

**Tujuan:** satu approve untuk satu transferan.

**Login sebagai:** admin event yang bersangkutan

**Langkah:**

1. Buka **Pesanan**, cari pesanan berisi tiga tiket dari Case 5
2. Klik **Lihat Bukti** untuk memeriksa gambar bukti transfer
3. Klik **Setujui & Terbitkan**, konfirmasi dialognya

**Hasil yang diharapkan:**

- Pesan sukses: *"Pesanan ORD-… disetujui! 3 tiket diterbitkan dan notifikasi WA dikirim."*
- Status pesanan berubah jadi **Lunas**
- Baris pesanan kini menampilkan tautan **PDF** untuk tiap tiket
- Badge di sidebar berkurang

**Cek pesan WhatsApp-nya:** kalau `FONNTE_TOKEN` belum diisi di `.env`, pesan tidak dikirim tapi dicatat ke log. Lihat isinya:

```bash
php artisan pail
```

Atau buka `storage/logs/laravel.log` dan cari `WA Message to`.

**Poin yang ditonjolkan:** admin menekan tombol **sekali**, tiga tiket sekaligus jadi valid dan mendapat QR.

---

## Case 9 — Menolak pesanan dan meminta bukti ulang

**Tujuan:** menunjukkan jalur perbaikan kalau bukti transfer bermasalah.

**Langkah:**

1. Sebagai admin, pada pesanan yang masih menunggu klik **Tolak**
2. Isi alasan, misal `Nominal transfer tidak sesuai dengan total pesanan.` → **Tolak Pesanan**
3. Logout, login sebagai peserta pemilik pesanan itu
4. Buka `/dashboard`

**Hasil yang diharapkan:**

- Kartu pesanan berstatus **Ditolak**, dengan kotak merah berisi alasan dari panitia
- Klik **Detail Pesanan** → ada bagian unggah ulang bukti pembayaran

**Lanjutkan:**

5. Unggah gambar bukti yang baru → **Unggah Ulang**
6. Login lagi sebagai admin → pesanan kembali muncul di antrian **Menunggu**

**Poin yang ditonjolkan:** alasan penolakan wajib diisi, supaya peserta tahu persis apa yang harus diperbaiki.

---

## Case 10 — Peserta melihat dan mengunduh tiketnya

**Tujuan:** memperlihatkan dua halaman berbeda untuk dua kebutuhan berbeda.

**Login sebagai:** peserta yang pesanannya sudah disetujui

**Langkah:**

1. Buka `/dashboard` — halaman **Pesanan Saya**
2. Klik tombol **Lihat Tiket Saya** di kanan atas → halaman `/tickets`
3. Klik **Buka E-Ticket** pada salah satu tiket
4. Kembali, klik **Unduh PDF**

**Hasil yang diharapkan:**

| Halaman | Isinya |
|---|---|
| `/dashboard` | Riwayat pesanan: kode, jumlah tiket, total, status pembayaran |
| `/tickets` | **Hanya tiket aktif**: QR, kode tiket, nama peserta, kategori — tanpa urusan pembayaran |

- PDF terunduh dengan nama `eticket-ST-…​.pdf` dan memuat QR code

**Catatan:** gambar QR diambil dari layanan online `api.qrserver.com`. Kalau demo dilakukan tanpa internet, QR tidak muncul — pakai input manual pada Case 11.

**Poin yang ditonjolkan:** `/tickets` sengaja dibuat bersih supaya cepat dibuka di gerbang masuk.

---

## Case 11 — Check-in di lokasi acara

**Tujuan:** memperagakan pemindaian tiket oleh panitia.

**Login sebagai:** admin event yang bersangkutan

**Langkah:**

1. Buka menu **QR Scanner**
2. Klik tombol untuk mengaktifkan kamera, lalu arahkan ke QR pada halaman `/tickets` peserta (bisa dari layar HP)
3. **Alternatif tanpa kamera:** ketik **kode tiket** (misal `ST-1-5K-FR-0001`) pada kolom input manual, lalu kirim

**Hasil yang diharapkan:**

- Muncul notifikasi hijau **"Check-in Berhasil!"** beserta nama peserta
- Scan **kedua kali** pada tiket yang sama → ditolak: *"Ticket already checked in!"*
- Scan tiket yang **pesanannya belum disetujui** → ditolak: *"Ticket is not valid or payment pending."*
- Scan tiket **milik event lain** → ditolak: *"Tiket ini terdaftar pada event lain!"*

**Verifikasi:** buka **Dashboard**, angka **Checked-In** bertambah.

**Poin yang ditonjolkan:** pemindai menerima QR maupun kode tiket yang diketik manual, jadi tetap bisa jalan kalau kamera bermasalah.

---

## Case 12 — Rekap data peserta

**Tujuan:** menunjukkan hasil akhir yang dipakai panitia di lapangan.

**Login sebagai:** admin event yang bersangkutan

**Langkah:**

1. Buka **Participants**
2. Coba saring berdasarkan kategori, dan cari dengan nama atau kode tiket
3. Klik **Edit** pada salah satu peserta → gulir ke bawah
4. Kembali, klik tombol export CSV
5. Buka file `participants_setiket.csv` di Excel

**Hasil yang diharapkan:**

- Halaman edit peserta menampilkan panel **Jawaban Pertanyaan Tambahan** berisi jawaban dari Case 3
- CSV memuat kolom standar (nama, NIK, kota, email, WA, kategori, jersey, kode pesanan, kode tiket, status) **ditambah satu kolom untuk tiap pertanyaan tambahan** event tersebut
- Persetujuan tercatat sebagai `Ya` / `Tidak`

**Poin yang ditonjolkan:** pertanyaan tambahan yang dibuat admin ikut sampai ke rekap, bukan berhenti di form.

---

## Case 13 — Batas akses yang harus ditolak

**Tujuan:** memperagakan bahwa pembatasannya nyata, bukan sekadar menyembunyikan menu.

Semua percobaan berikut **harus gagal**:

| Sebagai | Coba buka | Hasil yang benar |
|---|---|---|
| Belum login | `/dashboard` | Dilempar ke `/login` |
| Belum login | `/admin/dashboard` | Dilempar ke `/login` |
| Peserta | `/admin/dashboard` | 403 |
| Admin event | `/dashboard` | 403 |
| Admin event | `/admin/events` | 403 (khusus super admin) |
| Admin event | `/admin/admins` | 403 (khusus super admin) |
| Admin event | `/admin/form-fields?event_id=<event lain>` | Tetap membuka event sendiri, bukan event lain |
| Admin atau super admin | `/register-event` | 403 — akun pengelola tidak untuk membeli tiket |
| Peserta A | `/orders/<id pesanan milik Peserta B>` | 403 |

**Cara mencoba peran kedua tanpa logout terus-menerus:** buka jendela penyamaran (incognito) untuk sesi kedua.

**Poin yang ditonjolkan:** pembatasan diterapkan di sisi server, jadi mengetik URL langsung pun tetap ditolak.

---

## Lampiran — Memeriksa hasil lewat database

Kalau ingin menunjukkan apa yang tersimpan di balik layar saat demo:

**Ringkasan pesanan terakhir:**

```bash
php artisan tinker --execute="\$o = \App\Models\Order::with('tickets.participant','event','user')->latest()->first(); echo \$o->order_code.' | '.\$o->event->title.' | '.\$o->payment_status.' | Rp'.number_format((float)\$o->total_amount,0,',','.').PHP_EOL; foreach (\$o->tickets as \$t) { echo '  '.str_pad(\$t->participant->fullname,20).\$t->ticket_code.'  '.\$t->status.PHP_EOL; }"
```

**Konfigurasi formulir sebuah event** (ganti angka 1 dengan id event):

```bash
php artisan tinker --execute="foreach (\App\Models\EventFormField::where('event_id',1)->orderBy('sort_order')->get() as \$f) { echo (\$f->is_core?'[bawaan] ':'[custom] ').str_pad(\$f->label,45).str_pad(\$f->type,10).(\$f->enabled?'aktif ':'MATI  ').(\$f->required?'wajib':'opsional').PHP_EOL; }"
```

**Jawaban pertanyaan tambahan peserta terakhir:**

```bash
php artisan tinker --execute="\$p = \App\Models\Participant::latest()->first(); echo \$p->fullname.' -> '.json_encode(\$p->custom_data, JSON_UNESCAPED_UNICODE);"
```

**Siapa menangani event apa:**

```bash
php artisan tinker --execute="foreach (\App\Models\User::whereIn('role',['admin','super_admin'])->with('event')->get() as \$u) { echo str_pad(\$u->email,28).str_pad(\$u->role,12).(\$u->event->title ?? 'semua event').PHP_EOL; }"
```

---

## Lampiran — Hal yang perlu diantisipasi saat demo

| Gejala | Sebabnya | Tindakan |
|---|---|---|
| Halaman tampil tanpa CSS | Dev server Vite mati tapi `public/hot` tertinggal | Hapus `public/hot`, atau jalankan `npm run dev` lagi |
| QR tidak muncul di tiket atau PDF | Butuh internet (`api.qrserver.com`) | Pakai input manual kode tiket di scanner |
| Notifikasi WhatsApp tidak terkirim | `FONNTE_TOKEN` belum diisi di `.env` | Wajar — pesannya tercatat di `storage/logs/laravel.log` |
| Gambar bukti transfer tidak tampil | Symlink storage belum dibuat | Jalankan `php artisan storage:link` |
| Kamera scanner tidak aktif | Browser memblokir kamera di koneksi non-HTTPS | Pakai `localhost` (diizinkan browser), atau input manual |
| Tombol "Beli Tiket" malah ke halaman login | Memang begitu — pembelian wajib login | Login dulu sebagai peserta, nanti dikembalikan ke form |
