-- ============================================================
-- Seed Data for "Jalan Yuk!"
-- Password for all default accounts is 'password123'
-- Hash generated via password_hash('password123', PASSWORD_BCRYPT)
-- (Uses active database selected by connection / .env)
-- ============================================================

-- Default Users
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`) VALUES
(1, 'admin', 'admin@jalanyuk.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),
(2, 'moderator', 'mod@jalanyuk.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Moderator'),
(3, 'contributor', 'user@jalanyuk.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Contributor')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Default Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Kulineran', 'kulineran', 'Spot makan enak, jajanan legendaris, dan kuliner viral'),
(2, 'Hidden Gem', 'hidden-gem', 'Tempat tenang, estetik, dan belum banyak diketahui orang'),
(3, 'Servis Motor', 'servis-motor', 'Bengkel motor terpercaya, spesialis matic, dan tune up'),
(4, 'Tempat Nongkrong & Kafe', 'tempat-nongkrong-kafe', 'Coffee shop dengan suasana nyaman untuk WFC atau santai'),
(5, 'Wisata Alam', 'wisata-alam', 'Pemandangan alam terbuka, pantai, perbukitan, dan taman kota')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- Sample Locations
INSERT INTO `locations` (`id`, `user_id`, `category_id`, `title`, `description`, `personal_notes`, `maps_url`, `latitude`, `longitude`, `visit_status`, `approval_status`) VALUES
(1, 1, 1, 'Gultik Blok M Pak Agus Gasendra', 'Gulai Tikungan legendaris di kawasan Blok M. Kuah santan gurih dengan daging empuk dan taburan bawang goreng.', 'Datang sebelum jam 8 malam biar dagingnya masih lengkap. Minta kuah dipisah kalau dibungkus!', 'https://maps.google.com/?q=-6.244199,106.797967', -6.24419900, 106.79796700, 'Favorite', 'Published'),

(2, 2, 4, 'Kopi Toko Djawa Braga', 'Kafe bernuansa vintage di Jalan Braga Bandung dengan sajian Kopi Awan andalan dan pastry hangat.', 'Spot duduk di lantai 2 dekat jendela paling enak buat foto street photography Braga.', 'https://maps.google.com/?q=-6.917464,107.609810', -6.91746400, 107.60981000, 'Visited', 'Published'),

(3, 3, 2, 'Tebing Breksi Sleman', 'Taman wisata tebing kapur dengan ukiran artistik dan pemandangan sunset kota Yogyakarta dari ketinggian.', 'Sewa payung atau pakai topi kalau siang hari. Waktu terbaik datang jam 16:30 menjelang terbenam matahari.', 'https://maps.google.com/?q=-7.781682,110.504620', -7.78168200, 110.50462000, 'Wishlist', 'Published'),

(4, 1, 3, 'Bengkel Spesialis Vespa Matic Scooterland', 'Bengkel servis rutin, tune up CVT, dan spare parts vespa matic dengan mekanik berpengalaman.', 'Bisa booking dulu lewat WA biar tidak antre lama di weekend.', 'https://maps.google.com/?q=-6.265531,106.822604', -6.26553100, 106.82260400, 'Visited', 'Published'),

(5, 3, 5, 'Pantai Melasti Ungasan', 'Pantai berpasir putih bersih di balik tebing kapur megah di kawasan Bali Selatan.', 'Akses jalan aspal sudah sangat mulus. Ada spot sunset point di tebing atas.', 'https://maps.google.com/?q=-8.847522,115.158580', -8.84752200, 115.15858000, 'Favorite', 'Published'),

(6, 3, 1, 'Bakmi GM Melawai', 'Bakmi legendaris dengan pangsit goreng renyah dan saus asam manis khas.', 'Pesan Bakmi Spesial GM dan Pangsit Goreng 1 porsi isi 10.', 'https://maps.google.com/?q=-6.243500,106.798100', -6.24350000, 106.79810000, 'Wishlist', 'Pending')
ON DUPLICATE KEY UPDATE `title`=`title`;

-- Sample Media
INSERT INTO `location_media` (`id`, `location_id`, `media_type`, `media_url`) VALUES
(1, 1, 'YouTube', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
(2, 2, 'Instagram', 'https://www.instagram.com/p/C3XYZ123/'),
(3, 3, 'TikTok', 'https://www.tiktok.com/@explorejogja/video/7123456789'),
(4, 5, 'YouTube', 'https://www.youtube.com/watch?v=aqz-KE-bpKQ')
ON DUPLICATE KEY UPDATE `media_url`=`media_url`;
