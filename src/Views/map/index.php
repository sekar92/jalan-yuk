<?php
$pageTitle = 'Master Map View - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="master-map-page">
    <div class="map-page-sidebar">
        <div class="p-5 border-b border-slate-200 bg-white">
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Peta Master Lokasi</h1>
            <p class="text-xs text-slate-500 mt-1">Jelajahi seluruh tempat rekomendasi yang tersebar di peta interaktif.</p>
        </div>

        <!-- Filter Controls for Map -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 space-y-3">
            <div>
                <label for="map-filter-category" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kategori:</label>
                <select id="map-filter-category" class="form-select text-xs py-2">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="map-filter-status" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status Kunjungan:</label>
                <select id="map-filter-status" class="form-select text-xs py-2">
                    <option value="">Semua Status</option>
                    <option value="Wishlist">📌 Wishlist</option>
                    <option value="Visited">✅ Visited</option>
                    <option value="Favorite">⭐ Favorite</option>
                </select>
            </div>

            <div>
                <label for="map-search-query" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Cari Tempat:</label>
                <input type="text" id="map-search-query" class="form-control text-xs py-2" placeholder="Ketik nama atau tips...">
            </div>
        </div>

        <!-- List of Pins in Sidebar -->
        <div class="map-spots-list-wrapper">
            <div class="px-4 py-2 bg-slate-100/80 border-b border-slate-200 text-xs font-semibold text-slate-600">
                <span id="spots-count-indicator">Memuat lokasi...</span>
            </div>
            <div id="map-spots-sidebar-list" class="map-spots-sidebar-list divide-y divide-slate-100">
                <!-- Populated dynamically via JS -->
            </div>
        </div>
    </div>

    <div class="map-page-canvas">
        <div id="master-map-container" class="master-map-container"></div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
