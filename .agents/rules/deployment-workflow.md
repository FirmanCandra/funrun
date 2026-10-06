# FunRun Deployment & Git Workflow Rules

## ⚠️ WAJIB DIIKUTI — Baca sebelum melakukan push apapun

---

## Struktur Branch

| Branch | Fungsi |
|--------|--------|
| `main` | Branch utama development, sumber kontribusi GitHub |
| `production-baseline` | Branch yang di-deploy otomatis ke Hostinger |

---

## Workflow Push — Setiap Perubahan Kode

### 1. Pastikan ada perubahan yang perlu di-deploy ke Hostinger?

**Jika ada perubahan PHP / Composer:**
```bash
# Jalankan lokal terlebih dahulu
composer update --prefer-dist --no-interaction

# WAJIB: Regenerate package cache setelah update composer
php artisan package:discover --ansi

# Stage semua perubahan termasuk vendor dan cache
git add composer.json composer.lock vendor/ bootstrap/cache/packages.php bootstrap/cache/services.php
```

**Jika ada perubahan CSS / JS / Assets:**
```bash
# Build dulu sebelum commit
npm run build

# Stage hasil build
git add public/build/
```

**Untuk semua perubahan:**
```bash
git add .
```

### 2. Commit

```bash
git commit -m "tipe: deskripsi singkat perubahan"
```

**Format commit message:**
- `feat:` — fitur baru
- `fix:` — bugfix
- `chore:` — maintenance (update dependency, config, dll)
- `style:` — perubahan tampilan/CSS
- `refactor:` — refaktor kode

### 3. Push ke KEDUA branch (WAJIB!)

```bash
# Push ke main (untuk kontribusi GitHub profile)
git push origin main

# Push ke production-baseline (untuk auto-deploy Hostinger)
git push origin main:production-baseline
```

> ❗ **JANGAN PERNAH** push hanya ke salah satu branch. Selalu push ke keduanya dalam satu sesi.

---

## Kenapa vendor/ dan public/build/ harus di-commit?

Hostinger shared hosting **menonaktifkan `proc_open`** di PHP, sehingga:
- `composer install` tidak bisa menjalankan scripts (`@php artisan ...`)
- `npm run build` tidak tersedia di server

Solusinya: commit hasil build langsung ke repo agar Hostinger tinggal clone tanpa perlu build.

**File yang HARUS ada di repo untuk Hostinger:**
- `vendor/` — PHP dependencies
- `public/build/` — compiled CSS/JS assets
- `bootstrap/cache/packages.php` — package manifest
- `bootstrap/cache/services.php` — service manifest

---

## Setup Environment Lokal (Fresh Clone)

```bash
# 1. Clone repo
git clone https://github.com/FirmanCandra/funrun.git
cd funrun

# 2. Copy env
cp .env.example .env

# 3. Install dependencies (sudah ada vendor/ tapi jalankan untuk dev packages)
composer install

# 4. Generate key
php artisan key:generate

# 5. Setup database di .env lalu migrate
php artisan migrate --seed

# 6. Install node & build
npm install
npm run dev
```

---

## Setup di Hostinger (via SSH — lakukan HANYA sekali saat pertama deploy)

```bash
cd ~/public_html

# 1. Buat .env (sesuaikan isinya)
cp .env.example .env
nano .env

# 2. Generate app key
php artisan key:generate --force

# 3. Set permission
chmod -R 775 storage bootstrap/cache

# 4. Storage symlink
php artisan storage:link

# 5. Migrasi database
php artisan migrate --force

# 6. Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Setup .htaccess & index.php di Root (Wajib untuk Hostinger)

Hostinger mengarahkan domain utama ke `~/domains/<domain>/public_html`.
Karena seluruh file Laravel di-deploy langsung ke dalam folder `public_html/`, kita WAJIB memiliki:

1. **File: `.htaccess` di root proyek:**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Proteksi file & direktori sensitif
    RewriteRule ^(\.env|\.git|composer\.(json|lock)|package(-lock)?\.json|artisan|phpunit\.xml) - [F,L,NC]
    RewriteRule ^(app|bootstrap|config|database|resources|routes|storage|tests|vendor)/ - [F,L,NC]

    # Handle Front Controller
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^$ public/index.php [L]

    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L,QSA]
</IfModule>
```

2. **File: `index.php` di root proyek:**
Sebagai fallback front-controller jika mod_rewrite memanggil directory index root.

Kedua file ini sudah disertakan di root repositori dan otomatis ter-deploy ke Hostinger.

---

## Konfigurasi .env untuk Hostinger

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://namadomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u267893077_namadb
DB_USERNAME=u267893077_namadb
DB_PASSWORD=PasswordDatabase

SESSION_DRIVER=file
CACHE_STORE=file
```

---

## Checklist Sebelum Push ke Production

- [ ] Sudah `npm run build` dan hasilnya di-commit (`public/build/`)
- [ ] Sudah `php artisan package:discover` setelah update composer
- [ ] `composer.json` TIDAK ada `@php artisan` di scripts `post-autoload-dump` atau `post-update-cmd` (akan gagal di Hostinger karena `proc_open` diblokir)
- [ ] Push ke `main` ✅
- [ ] Push ke `production-baseline` ✅
- [ ] Cek Hostinger deployment log tidak ada error

---

## Troubleshooting

### Error: `proc_open is not available`
→ Ada `@php artisan` di `composer.json` scripts. Hapus semua `@php artisan` dari `post-autoload-dump` dan `post-update-cmd`. Jalankan `package:discover` lokal lalu commit hasilnya.

### Error: 403 Forbidden di Hostinger
→ Buat `.htaccess` di `public_html/` yang redirect ke `public/` (lihat section di atas).

### Error: `No application encryption key has been specified`
→ Jalankan `php artisan key:generate --force` di server via SSH.

### Kontribusi GitHub tidak terhitung
→ Pastikan selalu push ke `main`. Branch `production-baseline` tidak terhitung sebagai kontribusi karena bukan default branch.

### Perubahan sudah push tapi Hostinger belum update
→ Masuk Hostinger panel → Penempatan → klik "Deploy ulang" secara manual.
