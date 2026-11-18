# Product Requirements Document (PRD)
**Nama Produk:** Jalan Yuk!
**Platform:** Web Responsif (PWA Ready - Mobile, Tablet, Desktop)
**Tech Stack:** PHP (Backend), MySQL (Database), HTML/CSS/JS (Frontend)
**Versi Dokumen:** 1.0 (Draft Awal)

---

## 1. Ringkasan Eksekutif (Executive Summary)
"Jalan Yuk!" adalah aplikasi manajemen dan penanda lokasi berbasis web. Aplikasi ini memungkinkan pengguna untuk menyimpan link Google Maps, menambahkan detail informasi, mengelompokkan berdasarkan kategori khusus (seperti "Kulineran", "Servis Motor", dll.), serta menyematkan (embed) media sosial terkait (TikTok, Instagram, YouTube). Aplikasi ini dirancang agar ringan dengan performa pencarian yang optimal menggunakan arsitektur web tradisional yang didukung fungsionalitas PWA (Progressive Web App).

## 2. Tujuan Produk (Product Goals)
*   Menyediakan platform terpusat untuk menyimpan dan mengorganisasi lokasi favorit/wishlist pengguna.
*   Memudahkan pencarian lokasi dengan performa tinggi menggunakan fitur *Full-Text Search* MySQL.
*   Memfasilitasi kolaborasi kurasi tempat dengan sistem multi-user (Admin, Moderator, Kontributor).
*   Menyajikan antarmuka yang mulus di perangkat *mobile*, *tablet*, maupun *desktop*.

---

## 3. User Roles & Access Control
Sistem ini menggunakan arsitektur *Role-Based Access Control* (RBAC) dengan 3 level pengguna:

1.  **Admin:**
    *   Memiliki akses penuh ke seluruh sistem.
    *   Mengelola pengguna (CRUD user, ubah *password*, ubah *role*).
    *   Mengelola Master Kategori.
2.  **Moderator:**
    *   Bertugas sebagai kurator konten.
    *   Dapat meninjau lokasi yang ditambahkan oleh Kontributor (mengubah status dari *Pending* menjadi *Published* atau *Rejected*).
    *   Dapat mengedit deskripsi atau memperbaiki link yang rusak dari semua postingan.
3.  **Kontributor:**
    *   Dapat menambahkan lokasi baru beserta media/link.
    *   Lokasi baru yang ditambahkan berstatus *Pending* hingga di-ACC Moderator.
    *   Hanya dapat mengedit atau menghapus lokasi milik mereka sendiri.

---

## 4. Fitur Utama (Key Features)

### 4.1. Manajemen Lokasi & Peta
*   **Input Link Maps:** Form untuk memasukkan URL/Shortlink Google Maps.
*   **Auto-Extract Coordinates:** Sistem (via PHP) mencoba mengekstrak data *Latitude* dan *Longitude* dari URL yang diinputkan.
*   **Master Map View:** Halaman interaktif menampilkan *pin* lokasi di atas peta gabungan (bisa menggunakan *library* Leaflet.js).

### 4.2. Pengayaan Informasi (Rich Content)
*   **Smart Embed:** *Parsing* otomatis link TikTok, Instagram, atau YouTube menjadi *iframe/embed player* yang bisa langsung ditonton.
*   **Catatan Personal:** Area teks untuk menyimpan tips khusus (*lifehacks* lokasi tersebut).

### 4.3. Kategorisasi & Status Kunjungan
*   **Kategori Dinamis:** Pengelompokan tempat seperti "Kulineran", "Hidden Gem", "Servis Motor".
*   **Visit Status:** Pengguna bisa menandai lokasi sebagai: `Wishlist` (Belum), `Visited` (Sudah), `Favorite` (Rekomendasi).

### 4.4. Pencarian Lanjutan (Advanced Search)
*   **MySQL Full-Text Search:** Pencarian cepat dan relevan berdasarkan Nama Tempat, Deskripsi, dan Catatan.
*   **Filter Kombinasi:** Filter multi-dimensi (Kategori + Status Kunjungan + Area/Kota).

---

## 5. Struktur Database MySQL

Berikut adalah rancangan awal skema tabel database untuk mendukung fitur di atas. Fokus pada relasi yang efisien dan penggunaan tipe data yang tepat.

### 5.1. Tabel `users`
Menyimpan data pengguna dan level akses.
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Moderator', 'Contributor') DEFAULT 'Contributor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.2. Tabel `categories`
Menyimpan master data pengelompokan tempat.
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5.3. Tabel `locations`
Tabel utama penyimpan data tempat. Menggunakan index *FULLTEXT* untuk optimasi pencarian.
```sql
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    personal_notes TEXT,
    maps_url VARCHAR(500) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    visit_status ENUM('Wishlist', 'Visited', 'Favorite') DEFAULT 'Wishlist',
    approval_status ENUM('Pending', 'Published', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FULLTEXT(title, description, personal_notes) -- Index untuk performa pencarian
);
```

### 5.4. Tabel `location_media`
Mendukung *multiple* media atau *embed links* per satu lokasi.
```sql
CREATE TABLE location_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    media_type ENUM('Image', 'TikTok', 'Instagram', 'YouTube', 'OtherLink') NOT NULL,
    media_url VARCHAR(500) NOT NULL,
    file_path VARCHAR(255), -- Jika tipe 'Image' dan di-upload ke server
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);
```