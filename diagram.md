# Diagram Alur & Use Case — SeTiket / FunRun

Dokumen ini berisi diagram alur (flowchart) dan use case untuk sistem **SeTiket / FunRun Hore** — platform pendaftaran event lari dan festival.  
*Copy-paste kode berikut ke **[Mermaid Live Editor](https://mermaid.live)** untuk melihat gambar.*

---

## 1. Flowchart Pendaftaran Peserta

```mermaid
flowchart TD
    A[Mulai] --> B[Membuka Halaman Utama / Landing Page]
    B --> C[Memilih Event]
    C --> D[Melihat Detail Event]
    D --> E[Klik Daftar / Register]
    E --> F[Mengisi Form Registrasi]
    F --> F1[Isi: Nama, Email, No. WA, TTL, NIK, Kota, dll]
    F1 --> F2[Pilih Kategori: 3K / 5K / 10K]
    F2 --> F3[Pilih Ukuran Jersey: S/M/L/XL/XXL]
    F3 --> F4[Pilih Metode Pembayaran: Transfer / E-Wallet]
    F4 --> F5[Upload Bukti Pembayaran]
    F5 --> G[Submit Pendaftaran]

    G --> H{Sistem Validasi}
    H -- Data Valid --> I[Simpan Participant & User]
    H -- Data Tidak Valid --> H1[Tampilkan Error] --> F

    I --> J[Generate Ticket Code: ST-KATEGORI-BIB-XXXX]
    J --> K[Simpan Ticket dengan Status: pending]
    K --> L[Simpan Payment dengan Status: waiting_verification]
    L --> M[Tampilkan Halaman Sukses]
    M --> N[Peserta Menunggu Verifikasi Admin]

    N --> O{Admin Verifikasi?}
    O -- Diverifikasi --> P[Ticket jadi valid + QR Code]
    O -- Ditolak --> Q[Ticket tetap pending / ditolak]

    P --> R[Notifikasi WA ke Peserta]
    R --> S[Peserta Download E-Ticket PDF]
    S --> T[Selesai / Siap Check-in]
```

---

## 2. Flowchart Verifikasi Pembayaran (Admin)

```mermaid
flowchart TD
    A[Admin Login] --> B[Buka Menu Payments]
    B --> C[Lihat Daftar Pembayaran]
    C --> D[Filter Status: waiting_verification]
    D --> E[Lihat Detail: Nama, Kategori, Bukti Bayar]

    E --> F{Apakah Bukti Valid?}
    F -- Valid --> G[Klik Approve]
    F -- Tidak Valid --> H[Tidak Disetujui / Hapus]

    G --> I[Sistem Update Payment Status → paid]
    I --> J[Sistem Update Ticket Status → valid]
    J --> K[Sistem Generate QR Code unik]
    K --> L[Kirim Notifikasi WA ke Peserta]
    L --> M[Peserta bisa download E-Ticket PDF]
    M --> N[Selesai]

    H --> O[Payment & Ticket tetap pending] 
    O --> P[Admin hubungi peserta via WA] 
    P --> N
```

---

## 3. Flowchart Scan / Check-in Tiket

```mermaid
flowchart TD
    A[Admin Login] --> B[Buka Menu Scanner]
    B --> C[Scan QR Code / Input Kode Manual]

    C --> D{Sistem Cari Tiket}
    D -- Tidak Ditemukan --> E[Tampilkan: Tiket Tidak Ditemukan!]
    D -- Ditemukan --> F{Status Tiket?}

    F -- checked-in --> G[Tampilkan: Sudah Check-in!]
    F -- pending/waiting --> H[Tampilkan: Pembayaran Belum Lunas!]
    F -- valid --> I[Update Status → checked-in]
    I --> J[Tampilkan: Check-in Berhasil!]
    J --> K[Tampilkan Nama Peserta]

    E --> L[Selesai / Scan Berikutnya]
    G --> L
    H --> L
    K --> L
```

---

## 4. Use Case Diagram

```mermaid
usecaseDiagram
    actor Peserta as "Peserta\n(Participant)"
    actor AdminEvent as "Admin Event\n(per-event)"
    actor SuperAdmin as "Super Admin"

    rectangle "Sistem SeTiket / FunRun" {
        === PESERTA ===
        usecase UC1 as "Melihat Daftar Event"
        usecase UC2 as "Melihat Detail Event"
        usecase UC3 as "Mendaftar Event"
        usecase UC4 as "Upload Bukti Pembayaran"
        usecase UC5 as "Melihat E-Ticket"
        usecase UC6 as "Download E-Ticket PDF"

        Peserta --> UC1
        Peserta --> UC2
        Peserta --> UC3
        Peserta --> UC4
        Peserta --> UC5
        Peserta --> UC6

        UC3 ..> UC4 : <<include>>
        UC4 ..> UC5 : <<extend>>
        UC5 ..> UC6 : <<extend>>

        === ADMIN EVENT ===
        usecase UC7 as "Login Admin"
        usecase UC8 as "Melihat Dashboard"
        usecase UC9 as "Mengelola Peserta"
        usecase UC10 as "Verifikasi Pembayaran"
        usecase UC11 as "Scan Tiket Check-in"
        usecase UC12 as "Export Data CSV"

        AdminEvent --> UC7
        AdminEvent --> UC8
        AdminEvent --> UC9
        AdminEvent --> UC10
        AdminEvent --> UC11
        AdminEvent --> UC12

        UC7 ..> UC8 : <<include>>

        === SUPER ADMIN ===
        usecase UC13 as "Mengelola Event"
        usecase UC14 as "Mengelola Kategori Event"
        usecase UC15 as "Mengelola Admin Event"
        usecase UC16 as "Melihat Semua Data"

        SuperAdmin --> UC13
        SuperAdmin --> UC14
        SuperAdmin --> UC15
        SuperAdmin --> UC8
        SuperAdmin --> UC9
        SuperAdmin --> UC10
        SuperAdmin --> UC16
    }
```

---

## 5. ERD Relasi Antar Model (Tambahan)

```mermaid
erDiagram
    USER ||--o{ PARTICIPANT : "memiliki"
    EVENT ||--o{ PARTICIPANT : "mendaftar"
    EVENT ||--o{ EVENT_CATEGORY : "memiliki"
    EVENT ||--o{ USER : "dikelola oleh (admin)"
    PARTICIPANT ||--o| TICKET : "memiliki"
    TICKET ||--o{ PAYMENT : "memiliki"

    USER {
        int id PK
        string name
        string email
        string password
        string role "participant | admin | super_admin"
        int event_id FK "nullable"
    }

    EVENT {
        int id PK
        string title
        date date
        string location
        int quota
        json payment_methods
    }

    EVENT_CATEGORY {
        int id PK
        int event_id FK
        string name "3K Fun Walk"
        string code "3K"
        string bib_code "FW"
        int price
    }

    PARTICIPANT {
        int id PK
        int user_id FK
        int event_id FK
        string fullname
        string phone
        date dob
        string gender
        string address
        string nik "16 digit"
        string city
        string medical_history
        string jersey_size "S/M/L/XL/XXL"
        string emergency_contact
        string category "3K|5K|10K"
    }

    TICKET {
        int id PK
        int participant_id FK
        string ticket_code "ST-3K-FW-0001"
        string qr_code "unique"
        string status "pending|valid|checked-in"
    }

    PAYMENT {
        int id PK
        int ticket_id FK
        int amount
        string payment_method
        string payment_status "waiting_verification|paid"
        string proof_of_payment "path file"
    }
```

---

> **Cara menggunakan:**  
> 1. Buka [Mermaid Live Editor](https://mermaid.live)  
> 2. Paste kode diagram yang diinginkan (flowchart / usecase / erDiagram)  
> 3. Ekspor sebagai PNG/SVG untuk dimasukkan ke laporan

