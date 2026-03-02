# SIIPUL — Sistem Informasi Izin dan Pengelolaan Urusan Lembaga

Aplikasi manajemen cuti dan izin pegawai berbasis web, dibangun dengan **Laravel 12**, **Tailwind CSS**, dan **Alpine.js**.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Tailwind CSS 3, Alpine.js 3 |
| Database | MySQL (default) / SQLite |
| Build Tool | Vite |
| Notifikasi | Twilio (WhatsApp/SMS) |
| Export | Laravel Excel, DomPDF, PHPWord |

---

## Requirements

Pastikan environment lokal kamu sudah memiliki:

- **PHP >= 8.2** (dengan ekstensi: pdo, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json)
- **Composer**
- **Node.js >= 18** & **npm**
- **MySQL** (atau SQLite untuk development ringan)
- **Git**

---

## Instalasi & Menjalankan Aplikasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd SIIPUL
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siipul
DB_USERNAME=root
DB_PASSWORD=your_password
```

>  **Jangan pernah commit file `.env` ke repository.**

#### Konfigurasi Twilio (Opsional — untuk notifikasi WhatsApp/SMS)

```env
TWILIO_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

Lewati bagian ini jika fitur notifikasi tidak diperlukan.

---

### 4. Setup Database

Buat database MySQL terlebih dahulu:

```sql
CREATE DATABASE siipul;
```

Kemudian jalankan migrasi dan seeder:

```bash
# Jalankan migrasi tabel
php artisan migrate

# Seed data awal (akun admin)
php artisan db:seed --class=AdminSeeder
```

Setelah seeder berhasil, kamu akan mendapat akun admin default:

```
NIP      : 199999999999999999
Password : admin123
```

>  **Ganti password admin segera setelah login pertama.**

---

### 5. Build Assets Frontend

```bash
# Untuk production
npm run build

# Untuk development (watch mode)
npm run dev
```

---

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di browser:

```
http://127.0.0.1:8000
```

---

## Mode Development (Semua Sekaligus)

Untuk menjalankan server, queue, log watcher, dan Vite secara bersamaan dalam satu terminal:

```bash
composer run dev
```

Ini akan menjalankan:
- `php artisan serve` — web server
- `php artisan queue:listen` — queue worker
- `php artisan pail` — log viewer
- `npm run dev` — Vite hot reload

---

## Setup Cepat (One-liner)

```bash
composer run setup
```

Perintah ini otomatis menjalankan: `composer install` → copy `.env` → generate key → migrate → `npm install` → `npm run build`.

---

## Menjalankan Test

```bash
composer run test
# atau
php artisan test
```

---

## Struktur Direktori Penting

```
SIIPUL/
├── app/
│   ├── Http/Controllers/   # Logic controller
│   ├── Models/             # Eloquent models
│   └── ...
├── database/
│   ├── migrations/         # Skema tabel
│   └── seeders/            # Data awal
├── resources/
│   ├── views/              # Blade templates
│   └── ...
├── routes/
│   └── web.php             # Definisi route
├── .env.example            # Template environment
└── composer.json
```

---

## File yang Diabaikan Git

File/folder berikut tidak di-track oleh Git dan harus disiapkan secara lokal:

| File/Folder | Keterangan |
|---|---|
| `.env` | Konfigurasi rahasia |
| `vendor/` | Install via `composer install` |
| `node_modules/` | Install via `npm install` |
| `public/build/` | Build via `npm run build` |
| `storage/logs/*` | Log otomatis dibuat |

---

## Troubleshooting

**Error: `php artisan` tidak ditemukan**
→ Pastikan `vendor/` sudah ada. Jalankan `composer install`.

**Error koneksi database**
→ Cek konfigurasi `DB_*` di `.env`. Pastikan MySQL berjalan dan database `siipul` sudah dibuat.

**Halaman blank / asset tidak muncul**
→ Jalankan `npm run build` atau `npm run dev`.

**Error permission storage**
```bash
chmod -R 775 storage bootstrap/cache
```

---

## Kontribusi

1. Buat branch baru: `git checkout -b fitur/nama-fitur`
2. Commit perubahan: `git commit -m "feat: deskripsi singkat"`
3. Push dan buat Pull Request

---

> Proyek ini menggunakan [Laravel](https://laravel.com) — The PHP Framework for Web Artisans.
