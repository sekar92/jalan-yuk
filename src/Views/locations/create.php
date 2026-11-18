<?php
$pageTitle = 'Tambah Lokasi Baru - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 flex-1">
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
        <header class="mb-8 pb-5 border-b border-slate-100">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Tambah Lokasi Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Masukkan tautan Google Maps tempat favorit Anda dan sematkan ulasan media sosial terkait.</p>
        </header>

        <form action="/locations" method="POST" enctype="multipart/form-data" class="space-y-8" id="create-location-form">
            <!-- 1. Google Maps Link & Auto Extractor -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">1. Tautan Google Maps & Koordinat</legend>
                
                <div>
                    <label for="maps_url" class="form-label required">Link Google Maps / Shortlink</label>
                    <div class="flex gap-2">
                        <input 
                            type="url" 
                            name="maps_url" 
                            id="maps_url" 
                            class="form-control" 
                            placeholder="https://maps.app.goo.gl/... atau https://maps.google.com/?q=..." 
                            required
                        >
                        <button type="button" id="btn-extract-coords" class="btn btn-secondary flex-shrink-0 flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 icon-spin-hidden" id="extract-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-linecap="round"></circle></svg>
                            <span>Deteksi Koordinat</span>
                        </button>
                    </div>
                    <small class="form-hint" id="extract-status-hint">Masukkan shortlink atau link Maps, sistem PHP akan mengekstrak Latitude & Longitude otomatis.</small>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" class="form-control" placeholder="-6.2088">
                    </div>
                    <div>
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" class="form-control" placeholder="106.8456">
                    </div>
                </div>
            </fieldset>

            <!-- 2. Detail Informasi Tempat -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">2. Informasi & Kategori Tempat</legend>

                <div>
                    <label for="title" class="form-label required">Nama Tempat</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Kopi Toko Djawa, Gultik Blok M..." required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="form-label required">Kategori</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="visit_status" class="form-label required">Status Kunjungan</label>
                        <select name="visit_status" id="visit_status" class="form-select" required>
                            <option value="Wishlist">📌 Ingin Dikunjungi (Wishlist)</option>
                            <option value="Visited">✅ Pernah Dikunjungi (Visited)</option>
                            <option value="Favorite">⭐ Rekomendasi Pilihan (Favorite)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">Deskripsi / Ulasan Singkat</label>
                    <textarea name="description" id="description" rows="3" class="form-control" placeholder="Ceritakan tentang daya tarik tempat ini, menu andalan, suasana..."></textarea>
                </div>

                <div>
                    <label for="personal_notes" class="form-label">Catatan Personal & Tips Khusus (Lifehacks)</label>
                    <textarea name="personal_notes" id="personal_notes" rows="2" class="form-control" placeholder="Contoh: Datang sebelum jam 5 sore biar dapat tempat duduk outdoor; parkir motor di sebelah timur..."></textarea>
                    <small class="form-hint">Tips berguna yang ingin Anda bagikan untuk pengunjung berikutnya.</small>
                </div>
            </fieldset>

            <!-- 3. Smart Embeds & Media -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">3. Media Sosial & Foto (Smart Embed)</legend>

                <div>
                    <label class="form-label">Link Media Sosial Terkait (TikTok, Instagram, YouTube)</label>
                    <div id="media-inputs-container" class="space-y-2">
                        <div class="media-input-row">
                            <input type="url" name="media_urls[]" class="form-control" placeholder="https://www.tiktok.com/@... atau https://youtu.be/... atau Instagram">
                        </div>
                    </div>
                    <button type="button" id="btn-add-media-input" class="mt-2 text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 cursor-pointer">
                        + Tambah Kolom Link Media Lainnya
                    </button>
                    <small class="form-hint">Sistem akan secara cerdas mengubah URL menjadi player video atau ulasan tersemat.</small>
                </div>

                <div>
                    <label for="image_file" class="form-label">Upload Foto / Banner (Opsional)</label>
                    <input type="file" name="image_file" id="image_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <div id="image-preview"></div>
                </div>
            </fieldset>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="/" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">Simpan & Publikasikan</button>
            </div>
        </form>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
