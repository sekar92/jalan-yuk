<?php
use App\Helpers\Auth;
$pageTitle = 'Edit ' . htmlspecialchars($location['title']) . ' - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 flex-1">
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm">
        <header class="mb-8 pb-5 border-b border-slate-100">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Edit Lokasi: <?= htmlspecialchars($location['title']) ?></h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui informasi, perbaiki link peta, atau kelola media tersemat.</p>
        </header>

        <form action="/locations/<?= $location['id'] ?>/edit" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- 1. Google Maps Link & Auto Extractor -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">1. Tautan Google Maps & Koordinat</legend>
                
                <div>
                    <label for="maps_url" class="form-label required">Link Google Maps</label>
                    <div class="flex gap-2">
                        <input 
                            type="url" 
                            name="maps_url" 
                            id="maps_url" 
                            class="form-control" 
                            value="<?= htmlspecialchars($location['maps_url']) ?>" 
                            required
                        >
                        <button type="button" id="btn-extract-coords" class="btn btn-secondary flex-shrink-0 flex items-center gap-1.5 text-xs">
                            <svg class="w-3.5 h-3.5 icon-spin-hidden" id="extract-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-linecap="round"></circle></svg>
                            <span>Deteksi Ulang</span>
                        </button>
                    </div>
                    <small class="form-hint" id="extract-status-hint">Anda bisa mengganti URL atau mengekstrak ulang koordinat.</small>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" class="form-control" value="<?= htmlspecialchars($location['latitude'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" class="form-control" value="<?= htmlspecialchars($location['longitude'] ?? '') ?>">
                    </div>
                </div>
            </fieldset>

            <!-- 2. Detail Informasi Tempat -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">2. Informasi & Kategori Tempat</legend>

                <div>
                    <label for="title" class="form-label required">Nama Tempat</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($location['title']) ?>" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="form-label required">Kategori</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (int)$cat['id'] === (int)$location['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="visit_status" class="form-label required">Status Kunjungan</label>
                        <select name="visit_status" id="visit_status" class="form-select" required>
                            <option value="Wishlist" <?= $location['visit_status'] === 'Wishlist' ? 'selected' : '' ?>>📌 Ingin Dikunjungi (Wishlist)</option>
                            <option value="Visited" <?= $location['visit_status'] === 'Visited' ? 'selected' : '' ?>>✅ Pernah Dikunjungi (Visited)</option>
                            <option value="Favorite" <?= $location['visit_status'] === 'Favorite' ? 'selected' : '' ?>>⭐ Rekomendasi Pilihan (Favorite)</option>
                        </select>
                    </div>
                </div>

                <?php if (Auth::isModerator() || Auth::isAdmin()): ?>
                    <div>
                        <label for="approval_status" class="form-label">Status Kurasi (Moderator/Admin)</label>
                        <select name="approval_status" id="approval_status" class="form-select">
                            <option value="Pending" <?= $location['approval_status'] === 'Pending' ? 'selected' : '' ?>>Menunggu Kurasi (Pending)</option>
                            <option value="Published" <?= $location['approval_status'] === 'Published' ? 'selected' : '' ?>>Disetujui (Published)</option>
                            <option value="Rejected" <?= $location['approval_status'] === 'Rejected' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                        </select>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="description" class="form-label">Deskripsi / Ulasan</label>
                    <textarea name="description" id="description" rows="3" class="form-control"><?= htmlspecialchars($location['description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label for="personal_notes" class="form-label">Catatan Personal & Tips Khusus (Lifehacks)</label>
                    <textarea name="personal_notes" id="personal_notes" rows="2" class="form-control"><?= htmlspecialchars($location['personal_notes'] ?? '') ?></textarea>
                </div>
            </fieldset>

            <!-- 3. Existing Media & Add New Media -->
            <fieldset class="p-5 sm:p-6 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                <legend class="text-xs font-bold uppercase tracking-wider text-blue-600 px-2">3. Kelola Media Tersemat</legend>

                <?php if (!empty($location['media'])): ?>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Media Saat Ini:</h4>
                        <div class="space-y-2">
                            <?php foreach ($location['media'] as $med): ?>
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200 text-xs">
                                    <div class="flex items-center gap-2 overflow-hidden mr-2">
                                        <span class="media-tag media-tag-<?= strtolower($med['media_type']) ?>"><?= htmlspecialchars($med['media_type']) ?></span>
                                        <span class="truncate text-slate-600 max-w-xs" title="<?= htmlspecialchars($med['media_url']) ?>"><?= htmlspecialchars($med['media_url']) ?></span>
                                    </div>
                                    <form action="/locations/media/<?= $med['id'] ?>/delete" method="POST" onsubmit="return confirm('Hapus media ini?');">
                                        <input type="hidden" name="location_id" value="<?= $location['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-1.5 py-0.5 rounded text-sm cursor-pointer" title="Hapus media">&times;</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="new_media_url" class="form-label">Tambah Tautan Media Baru</label>
                    <input type="url" name="new_media_url" id="new_media_url" class="form-control" placeholder="https://www.tiktok.com/@... atau https://youtu.be/...">
                </div>

                <div>
                    <label for="image_file" class="form-label">Upload Foto / Banner Baru</label>
                    <input type="file" name="image_file" id="image_file" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
            </fieldset>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="/locations/<?= $location['id'] ?>" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg">Perbarui Lokasi</button>
            </div>
        </form>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
