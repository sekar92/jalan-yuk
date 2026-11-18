# Jalan Yuk! 📍

Aplikasi web responsif dan PWA-ready untuk penanda lokasi, manajemen tautan Google Maps, kurasi ulasan media sosial (TikTok, Instagram, YouTube), dan pencarian tempat favorit (kuliner, bengkel, wisata, kafe) berbasis PHP & MySQL.

Dibuat sesuai spesifikasi **Product Requirements Document (PRD v1.0)**.

---

## 🌟 Fitur Utama

1. **Manajemen Lokasi & Peta:**
   - **Input Link Maps:** Mendukung URL standar Google Maps dan shortlink (`maps.app.goo.gl`, `goo.gl/maps`).
   - **Auto-Extract Coordinates:** Deteksi otomatis nilai *Latitude* dan *Longitude* dari tautan Google Maps via backend PHP (dengan tombol deteksi langsung via AJAX).
   - **Master Map View:** Halaman peta interaktif gabungan berbasis **Leaflet.js** dan OpenStreetMap, lengkap dengan filter kategori, status kunjungan, dan sinkronisasi daftar tempat pada sidebar.
   - **Single Spot Mini Map:** Peta interaktif pada halaman detail tempat.

2. **Pengayaan Informasi (Rich Content):**
   - **Smart Embed Player:** Mengubah tautan TikTok, Instagram, dan YouTube menjadi player interaktif atau preview card tersemat.
   - **Catatan Personal & Lifehacks:** Kotak khusus tips berkunjung (waktu terbaik, spot parkir, menu rahasia).
   - **Upload Banner/Foto:** Dukungan upload file gambar lokal ke server.

3. **Kategorisasi & Status Kunjungan:**
   - **Kategori Dinamis:** Kulineran, Hidden Gem, Servis Motor, Tempat Nongkrong & Kafe, Wisata Alam, dll.
   - **Visit Status:** `Wishlist` (📌 Ingin Dikunjungi), `Visited` (✅ Pernah Dikunjungi), `Favorite` (⭐ Rekomendasi Pilihan).

4. **Pencarian Lanjutan (Advanced Search):**
   - **MySQL Full-Text Search:** Pencarian cepat berbasis `MATCH(...) AGAINST(...) IN BOOLEAN MODE` pada kolom `title`, `description`, dan `personal_notes`.
   - **Filter Multi-Dimensi:** Kombinasi Kategori + Status Kunjungan + Pencarian Kata Kunci.

5. **Role-Based Access Control (RBAC):**
   - **Admin:** Akses penuh ke seluruh sistem, kelola pengguna (CRUD, ubah role, ubah password), dan kelola Master Kategori.
   - **Moderator:** Kurasi konten (menyetujui *Pending* $\rightarrow$ *Published* atau *Rejected*), mengedit deskripsi & memperbaiki link rusak semua tempat.
   - **Contributor:** Menambahkan tempat baru (status awal *Pending*), mengedit/menghapus tempat miliknya sendiri.

6. **PWA (Progressive Web App) Ready:**
   - Standalone mobile experience dengan navigasi bawah (*Bottom Navigation Bar*).
   - Web App Manifest (`manifest.webmanifest`), ikon aplikasi, dan Service Worker (`sw.js`) untuk caching aset statis secara offline.

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Prasyarat
- PHP 8.1+ (dengan ekstensi `pdo`, `pdo_mysql` atau `pdo_sqlite`, `curl`, `gd`).

### 2. Konfigurasi Database (.env)
File `.env` telah disiapkan:
```env
APP_NAME="Jalan Yuk!"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Pilihan DB_CONNECTION: mysql atau sqlite
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jalan_yuk
DB_USERNAME=root
DB_PASSWORD=

# SQLite fallback (otomatis aktif jika MySQL belum aktif/dikonfigurasi)
SQLITE_PATH=database/jalan_yuk.sqlite
AUTO_FALLBACK_SQLITE=true
```

> **Catatan Portabilitas:** Aplikasi dilengkapi dengan *auto-fallback SQLite* cerdas. Jika server MySQL lokal Anda belum dinyalakan, aplikasi tetap dapat langsung dijalankan dan dicoba tanpa hambatan!

### 3. Inisialisasi Database
Jalankan perintah berikut di terminal:
```bash
php database/init.php
```
Skrip ini akan membuat tabel-tabel sesuai spesifikasi PRD (`users`, `categories`, `locations`, `location_media`) dan mengisi data awal (seeder).

### 4. Menjalankan Server Lokal
Cukup jalankan server bawaan PHP:
```bash
php -S 127.0.0.1:8000 -t public
```
Buka browser di: **`http://localhost:8000`**

---

## 👥 Akun Uji Coba Default

Semua akun default menggunakan password: **`password123`**

| Role | Username | Email | Akses Utama |
|---|---|---|---|
| **Admin** | `admin` | `admin@jalanyuk.test` | Kelola Pengguna, Master Kategori, Kurasi, CRUD Seluruh Lokasi |
| **Moderator** | `moderator` | `mod@jalanyuk.test` | Antrean Kurasi (Approve/Reject), Edit Konten & Perbaiki Link |
| **Contributor** | `contributor` | `user@jalanyuk.test` | Tambah Lokasi (Pending ACC), Kelola Tempat Sendiri |

Tersedia tombol satu-klik pada halaman `/login` untuk langsung mengisi kredensial uji coba.

---

## 📁 Struktur Direktori

```
jalan-yuk/
├── database/
│   ├── schema.sql            # Skema DDL MySQL sesuai spesifikasi PRD
│   ├── seeds.sql             # Data awal pengguna, kategori, & tempat
│   ├── init.php              # Skrip CLI inisialisasi database
│   └── jalan_yuk.sqlite      # Database SQLite (portable fallback)
├── public/
│   ├── index.php             # Front controller & routing utama
│   ├── manifest.webmanifest  # PWA manifest
│   ├── sw.js                 # PWA service worker
│   ├── assets/
│   │   ├── css/app.css       # Stylesheet responsif modern (Mobile/Tablet/Desktop)
│   │   ├── js/app.js         # Leaflet map logic, coordinate extractor, dynamic filters
│   │   └── icons/            # Ikon PWA (SVG & PNG)
│   └── uploads/              # Direktori upload gambar pengguna
├── src/
│   ├── App.php               # Routing & application kernel
│   ├── Config/
│   │   └── Database.php      # Koneksi database PDO (MySQL & SQLite fallback)
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── LocationController.php
│   │   ├── MapController.php
│   │   ├── ModeratorController.php
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   └── ApiController.php
│   ├── Helpers/
│   │   ├── Auth.php          # Session, auth state, & RBAC helper
│   │   ├── MapsHelper.php    # Auto-extract Google Maps coordinates & shortlink expander
│   │   └── MediaHelper.php   # Smart embed parser (YouTube, TikTok, Instagram)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Location.php      # Full-Text Search & filter query builder
│   │   └── LocationMedia.php
│   └── Views/                # Layouts & tampilan antarmuka
└── prd_aplikasi_jalan_yuk.md # Dokumen spesifikasi PRD
```
