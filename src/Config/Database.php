<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $driver = 'mysql';

    public static function loadEnv(string $path = __DIR__ . '/../../.env'): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                // Strip surrounding quotes
                if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                    $value = substr($value, 1, -1);
                }
                if (!isset($_ENV[$key])) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::loadEnv();

        $connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
        $fallback = filter_var($_ENV['AUTO_FALLBACK_SQLITE'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if ($connection === 'mysql') {
            try {
                $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
                $port = $_ENV['DB_PORT'] ?? '3306';
                $dbname = $_ENV['DB_DATABASE'] ?? 'jalan_yuk';
                $user = $_ENV['DB_USERNAME'] ?? 'root';
                $pass = $_ENV['DB_PASSWORD'] ?? '';

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                self::$driver = 'mysql';
                self::$instance = $pdo;
                return self::$instance;
            } catch (PDOException $e) {
                if (!$fallback) {
                    throw $e;
                }
                // Fallback to SQLite
            }
        }

        // SQLite connection
        $sqliteRel = $_ENV['SQLITE_PATH'] ?? 'database/jalan_yuk.sqlite';
        $sqlitePath = dirname(__DIR__, 2) . '/' . ltrim($sqliteRel, '/');
        $dir = dirname($sqlitePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $dsn = "sqlite:" . $sqlitePath;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("PRAGMA foreign_keys = ON;");

        self::$driver = 'sqlite';
        self::$instance = $pdo;

        // Auto-initialize schema for sqlite if empty
        self::ensureSchema($pdo, 'sqlite');

        return self::$instance;
    }

    public static function getDriver(): string
    {
        if (self::$instance === null) {
            self::getConnection();
        }
        return self::$driver;
    }

    public static function isMySQL(): bool
    {
        return self::getDriver() === 'mysql';
    }

    public static function ensureSchema(PDO $pdo, string $driver): void
    {
        // Check if users table exists
        $tableExists = false;
        try {
            if ($driver === 'sqlite') {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
                $tableExists = (bool)$stmt->fetch();
            } else {
                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                $tableExists = (bool)$stmt->fetch();
            }
        } catch (PDOException $e) {
            $tableExists = false;
        }

        if ($tableExists) {
            return;
        }

        if ($driver === 'sqlite') {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(50) NOT NULL UNIQUE,
                    email VARCHAR(100) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    role VARCHAR(20) DEFAULT 'Contributor' CHECK(role IN ('Admin', 'Moderator', 'Contributor')),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS categories (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(100) NOT NULL,
                    slug VARCHAR(100) NOT NULL UNIQUE,
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS locations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    category_id INTEGER NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    personal_notes TEXT,
                    maps_url VARCHAR(500) NOT NULL,
                    latitude REAL,
                    longitude REAL,
                    visit_status VARCHAR(20) DEFAULT 'Wishlist' CHECK(visit_status IN ('Wishlist', 'Visited', 'Favorite')),
                    approval_status VARCHAR(20) DEFAULT 'Pending' CHECK(approval_status IN ('Pending', 'Published', 'Rejected')),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
                );

                CREATE TABLE IF NOT EXISTS location_media (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    location_id INTEGER NOT NULL,
                    media_type VARCHAR(20) NOT NULL CHECK(media_type IN ('Image', 'TikTok', 'Instagram', 'YouTube', 'OtherLink')),
                    media_url VARCHAR(500) NOT NULL,
                    file_path VARCHAR(255),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
                );
            ");

            // Seed default data
            $passHash = password_hash('password123', PASSWORD_BCRYPT);
            $pdo->exec("
                INSERT INTO users (id, username, email, password_hash, role) VALUES
                (1, 'admin', 'admin@jalanyuk.test', '{$passHash}', 'Admin'),
                (2, 'moderator', 'mod@jalanyuk.test', '{$passHash}', 'Moderator'),
                (3, 'contributor', 'user@jalanyuk.test', '{$passHash}', 'Contributor');

                INSERT INTO categories (id, name, slug, description) VALUES
                (1, 'Kulineran', 'kulineran', 'Spot makan enak, jajanan legendaris, dan kuliner viral'),
                (2, 'Hidden Gem', 'hidden-gem', 'Tempat tenang, estetik, dan belum banyak diketahui orang'),
                (3, 'Servis Motor', 'servis-motor', 'Bengkel motor terpercaya, spesialis matic, dan tune up'),
                (4, 'Tempat Nongkrong & Kafe', 'tempat-nongkrong-kafe', 'Coffee shop dengan suasana nyaman untuk WFC atau santai'),
                (5, 'Wisata Alam', 'wisata-alam', 'Pemandangan alam terbuka, pantai, perbukitan, dan taman kota');

                INSERT INTO locations (id, user_id, category_id, title, description, personal_notes, maps_url, latitude, longitude, visit_status, approval_status) VALUES
                (1, 1, 1, 'Gultik Blok M Pak Agus Gasendra', 'Gulai Tikungan legendaris di kawasan Blok M. Kuah santan gurih dengan daging empuk dan taburan bawang goreng.', 'Datang sebelum jam 8 malam biar dagingnya masih lengkap. Minta kuah dipisah kalau dibungkus!', 'https://maps.google.com/?q=-6.244199,106.797967', -6.244199, 106.797967, 'Favorite', 'Published'),
                (2, 2, 4, 'Kopi Toko Djawa Braga', 'Kafe bernuansa vintage di Jalan Braga Bandung dengan sajian Kopi Awan andalan dan pastry hangat.', 'Spot duduk di lantai 2 dekat jendela paling enak buat foto street photography Braga.', 'https://maps.google.com/?q=-6.917464,107.609810', -6.917464, 107.609810, 'Visited', 'Published'),
                (3, 3, 2, 'Tebing Breksi Sleman', 'Taman wisata tebing kapur dengan ukiran artistik dan pemandangan sunset kota Yogyakarta dari ketinggian.', 'Sewa payung atau pakai topi kalau siang hari. Waktu terbaik datang jam 16:30 menjelang terbenam matahari.', 'https://maps.google.com/?q=-7.781682,110.504620', -7.781682, 110.504620, 'Wishlist', 'Published'),
                (4, 1, 3, 'Bengkel Spesialis Vespa Matic Scooterland', 'Bengkel servis rutin, tune up CVT, dan spare parts vespa matic dengan mekanik berpengalaman.', 'Bisa booking dulu lewat WA biar tidak antre lama di weekend.', 'https://maps.google.com/?q=-6.265531,106.822604', -6.265531, 106.822604, 'Visited', 'Published'),
                (5, 3, 5, 'Pantai Melasti Ungasan', 'Pantai berpasir putih bersih di balik tebing kapur megah di kawasan Bali Selatan.', 'Akses jalan aspal sudah sangat mulus. Ada spot sunset point di tebing atas.', 'https://maps.google.com/?q=-8.847522,115.158580', -8.847522, 115.158580, 'Favorite', 'Published'),
                (6, 3, 1, 'Bakmi GM Melawai', 'Bakmi legendaris dengan pangsit goreng renyah dan saus asam manis khas.', 'Pesan Bakmi Spesial GM dan Pangsit Goreng 1 porsi isi 10.', 'https://maps.google.com/?q=-6.243500,106.798100', -6.243500, 106.798100, 'Wishlist', 'Pending');

                INSERT INTO location_media (id, location_id, media_type, media_url) VALUES
                (1, 1, 'YouTube', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
                (2, 2, 'Instagram', 'https://www.instagram.com/p/C3XYZ123/'),
                (3, 3, 'TikTok', 'https://www.tiktok.com/@explorejogja/video/7123456789'),
                (4, 5, 'YouTube', 'https://www.youtube.com/watch?v=aqz-KE-bpKQ');
            ");
        }
    }
}
