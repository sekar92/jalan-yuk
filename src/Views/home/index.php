<?php
$pageTitle = 'Jalan Yuk! - Jelajahi Tempat & Rekomendasi Menarik';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';

$activeCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$activeStatus = $_GET['status'] ?? '';
$searchQuery = $_GET['q'] ?? '';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1">
    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 text-white p-8 sm:p-12 shadow-xl mb-8">
        <!-- Subtle decorative glow circles -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white/5 pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -left-16 -top-16 w-64 h-64 rounded-full bg-white/5 pointer-events-none" aria-hidden="true"></div>

        <div class="relative max-w-2xl z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/15 backdrop-blur-md text-blue-100 tracking-wide mb-4">
                Teman Kurasi Lokasi & Wisata
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-3">
                Simpan, Tandai & Jelajahi Lokasi Favoritmu
            </h1>
            <p class="text-base sm:text-lg text-blue-100 font-normal leading-relaxed mb-6">
                Kumpulkan link Google Maps, review media sosial TikTok & Instagram, serta tips lifehack tempat seru dalam satu platform terpusat.
            </p>

            <!-- Search Form with Full-Text Search -->
            <form action="/" method="GET" class="w-full">
                <?php if ($activeCategory): ?>
                    <input type="hidden" name="category" value="<?= $activeCategory ?>">
                <?php endif; ?>
                <?php if ($activeStatus): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($activeStatus) ?>">
                <?php endif; ?>

                <div class="flex items-center bg-white rounded-full p-1.5 sm:p-2 shadow-2xl border border-white/20">
                    <div class="pl-3.5 pr-2 text-slate-400">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="<?= htmlspecialchars($searchQuery) ?>" 
                        placeholder="Cari tempat, tips lifehack, atau makanan... (Full-text search)"
                        class="flex-1 bg-transparent border-none outline-none text-slate-800 text-sm sm:text-base placeholder-slate-400 px-2 py-1"
                        aria-label="Pencarian tempat"
                    >
                    <?php if (!empty($searchQuery)): ?>
                        <a href="/<?= $activeCategory ? '?category=' . $activeCategory : '' ?>" class="p-1 text-slate-400 hover:text-slate-600 mr-2 text-xl leading-none" title="Hapus pencarian">&times;</a>
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm px-6 py-2.5 rounded-full transition-colors duration-150 shadow-md">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Filter & Categories Bar -->
    <section class="space-y-4 mb-8">
        <!-- Horizontal Scrollable Category Chips -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="/<?= !empty($activeStatus) ? '?status=' . urlencode($activeStatus) : '' ?>" 
               class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-150 <?= empty($activeCategory) ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' ?>">
                Semua Kategori
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="/?category=<?= $cat['id'] ?><?= !empty($activeStatus) ? '&status=' . urlencode($activeStatus) : '' ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" 
                   class="px-4 py-2 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-150 <?= $activeCategory === (int)$cat['id'] ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100 hover:text-slate-900' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Status Filter & Map Toggle Row -->
        <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Status:</span>
                <a href="/?<?= $activeCategory ? 'category=' . $activeCategory . '&' : '' ?><?= !empty($searchQuery) ? 'q=' . urlencode($searchQuery) . '&' : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors duration-150 <?= empty($activeStatus) ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                   Semua
                </a>
                <a href="/?status=Wishlist<?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors duration-150 <?= $activeStatus === 'Wishlist' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                   📌 Wishlist
                </a>
                <a href="/?status=Visited<?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors duration-150 <?= $activeStatus === 'Visited' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                   ✅ Visited
                </a>
                <a href="/?status=Favorite<?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '' ?>" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-colors duration-150 <?= $activeStatus === 'Favorite' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                   ⭐ Rekomendasi
                </a>
            </div>

            <div>
                <a href="/map" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors duration-150">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                    <span>Buka Peta Penuh</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Location Cards Section -->
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 flex items-center gap-2.5">
                <?php if (!empty($searchQuery)): ?>
                    Hasil Pencarian: "<span class="text-blue-600"><?= htmlspecialchars($searchQuery) ?></span>"
                <?php else: ?>
                    Daftar Tempat Rekomendasi
                <?php endif; ?>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700"><?= count($locations) ?> tempat</span>
            </h2>
        </div>

        <?php if (empty($locations)): ?>
            <div class="text-center py-16 px-4 bg-white rounded-3xl border border-dashed border-slate-300 shadow-sm">
                <div class="text-5xl mb-3">📍</div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Tempat Ditemukan</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Coba gunakan kata kunci lain atau bersihkan filter pencarian Anda.</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="/" class="btn btn-outline btn-sm">Reset Filter</a>
                    <a href="/locations/create" class="btn btn-primary btn-sm">+ Tambah Tempat Baru</a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($locations as $loc): ?>
                    <article class="group bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 flex flex-col">
                        <!-- Card Media Header -->
                        <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
                            <?php if (!empty($loc['thumbnail_path'])): ?>
                                <img src="/uploads/<?= htmlspecialchars($loc['thumbnail_path']) ?>" alt="<?= htmlspecialchars($loc['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-200 to-slate-100 text-slate-400">
                                    <svg class="w-12 h-12 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <!-- Badges Overlay -->
                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none">
                                <span class="badge badge-category shadow-sm"><?= htmlspecialchars($loc['category_name']) ?></span>
                                <span class="badge badge-status-<?= strtolower($loc['visit_status']) ?> shadow-sm">
                                    <?= $loc['visit_status'] === 'Favorite' ? '⭐ Favorite' : ($loc['visit_status'] === 'Visited' ? '✅ Visited' : '📌 Wishlist') ?>
                                </span>
                            </div>

                            <?php if ($loc['media_count'] > 0): ?>
                                <span class="absolute bottom-3 right-3 bg-slate-900/80 backdrop-blur-md text-white text-[11px] font-semibold px-2 py-1 rounded-md flex items-center gap-1 shadow">
                                    <svg class="w-3.5 h-3.5 text-blue-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/></svg>
                                    <?= $loc['media_count'] ?> media
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1 mb-1.5">
                                    <a href="/locations/<?= $loc['id'] ?>"><?= htmlspecialchars($loc['title']) ?></a>
                                </h3>

                                <?php if (!empty($loc['description'])): ?>
                                    <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed mb-3">
                                        <?= htmlspecialchars(mb_strimwidth($loc['description'], 0, 110, '...')) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($loc['personal_notes'])): ?>
                                    <div class="bg-amber-50/90 border-l-3 border-amber-400 p-2.5 rounded-r-lg text-xs text-amber-900 leading-snug">
                                        <span class="font-bold text-amber-800">💡 Tips:</span> <?= htmlspecialchars(mb_strimwidth($loc['personal_notes'], 0, 85, '...')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Card Footer -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="text-[11px] text-slate-500">
                                    Oleh: <span class="font-semibold text-slate-700"><?= htmlspecialchars($loc['author_name']) ?></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="<?= htmlspecialchars($loc['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Buka Google Maps">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    </a>
                                    <a href="/locations/<?= $loc['id'] ?>" class="btn btn-outline btn-xs">
                                        Detail & Video
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
