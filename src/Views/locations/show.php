<?php
use App\Helpers\Auth;
use App\Helpers\MediaHelper;

$pageTitle = htmlspecialchars($location['title']) . ' - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1">
    <div class="mb-5">
        <a href="/" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            Kembali ke Jelajah
        </a>
    </div>

    <?php if ($location['approval_status'] === 'Pending'): ?>
        <div class="p-4 rounded-xl mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-xs" role="alert">
            <svg class="w-5 h-5 flex-shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span><strong>Status: Menunggu Kurasi (Pending).</strong> Tempat ini belum ditampilkan untuk publik hingga ditinjau oleh Moderator.</span>
        </div>
    <?php elseif ($location['approval_status'] === 'Rejected'): ?>
        <div class="p-4 rounded-xl mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm shadow-xs" role="alert">
            <svg class="w-5 h-5 flex-shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span><strong>Status: Ditolak (Rejected).</strong> Lokasi ini tidak lolos kurasi oleh Moderator.</span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Column: Info, Tips, Media -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <header class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="badge badge-category"><?= htmlspecialchars($location['category_name']) ?></span>
                    <span class="badge badge-status-<?= strtolower($location['visit_status']) ?>">
                        <?= $location['visit_status'] === 'Favorite' ? '⭐ Rekomendasi Favorite' : ($location['visit_status'] === 'Visited' ? '✅ Pernah Dikunjungi' : '📌 Ingin Dikunjungi') ?>
                    </span>
                    <?php if (Auth::isModerator() || Auth::isAdmin()): ?>
                        <span class="badge badge-approval-<?= strtolower($location['approval_status']) ?>">
                            Kurasi: <?= htmlspecialchars($location['approval_status']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight mb-2">
                    <?= htmlspecialchars($location['title']) ?>
                </h1>

                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <span>Dikurasi oleh <strong class="text-slate-700"><?= htmlspecialchars($location['author_name']) ?></strong></span>
                    <span>&bull;</span>
                    <span><?= date('d M Y', strtotime($location['created_at'])) ?></span>
                </div>
            </header>

            <!-- Personal Notes / Lifehack Callout -->
            <?php if (!empty($location['personal_notes'])): ?>
                <section class="bg-amber-50/90 border border-amber-200/80 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-amber-900 text-sm mb-1.5">
                        <span class="text-base">💡</span>
                        <h3>Catatan Khusus & Tips Lifehack:</h3>
                    </div>
                    <div class="text-xs sm:text-sm text-amber-950 leading-relaxed font-normal">
                        <?= nl2br(htmlspecialchars($location['personal_notes'])) ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Description -->
            <?php if (!empty($location['description'])): ?>
                <section class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-3">
                    <h2 class="text-lg font-bold text-slate-900">Deskripsi Tempat</h2>
                    <div class="text-sm sm:text-base text-slate-700 leading-relaxed space-y-3">
                        <?= nl2br(htmlspecialchars($location['description'])) ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Smart Media Embeds Section -->
            <section class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Media & Review Terkait</h2>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                        <?= count($location['media']) ?> media tersemat
                    </span>
                </div>

                <?php if (empty($location['media'])): ?>
                    <p class="text-sm text-slate-500 py-4 text-center">Belum ada video atau foto media sosial yang disematkan untuk tempat ini.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-6">
                        <?php foreach ($location['media'] as $media): ?>
                            <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 shadow-xs">
                                <div class="px-3.5 py-2 bg-white border-b border-slate-200 flex items-center justify-between">
                                    <span class="media-tag media-tag-<?= strtolower($media['media_type']) ?>">
                                        <?= htmlspecialchars($media['media_type']) ?>
                                    </span>
                                </div>
                                <div class="p-3">
                                    <?= MediaHelper::renderEmbed($media) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Sidebar Column: Map & Quick Actions -->
        <aside class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900">Lokasi & Navigasi</h3>

                <!-- Coordinates snippet -->
                <?php if ($location['latitude'] !== null && $location['longitude'] !== null): ?>
                    <div class="flex items-center gap-2 p-2.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-mono">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <span><?= number_format($location['latitude'], 6) ?>, <?= number_format($location['longitude'], 6) ?></span>
                    </div>

                    <!-- Mini Map Container -->
                    <div 
                        id="single-spot-map" 
                        class="single-spot-map" 
                        data-lat="<?= $location['latitude'] ?>" 
                        data-lng="<?= $location['longitude'] ?>" 
                        data-title="<?= htmlspecialchars($location['title']) ?>"
                    ></div>
                <?php else: ?>
                    <p class="text-xs text-slate-400">Koordinat peta belum terisi.</p>
                <?php endif; ?>

                <div>
                    <a href="<?= htmlspecialchars($location['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-block flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        <span>Buka di Google Maps</span>
                    </a>
                </div>
            </div>

            <!-- Moderator / Owner Actions -->
            <?php if (Auth::canManageLocation($location)): ?>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-slate-900">Kelola Lokasi</h3>
                    <div class="flex flex-col gap-2.5">
                        <a href="/locations/<?= $location['id'] ?>/edit" class="btn btn-outline btn-block flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            Edit Lokasi & Media
                        </a>

                        <form action="/locations/<?= $location['id'] ?>/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lokasi ini?');">
                            <button type="submit" class="btn btn-danger-outline btn-block flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Hapus Lokasi
                            </button>
                        </form>
                    </div>

                    <?php if ((Auth::isModerator() || Auth::isAdmin()) && $location['approval_status'] === 'Pending'): ?>
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <span class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Tindakan Kurasi:</span>
                            <div class="grid grid-cols-2 gap-2">
                                <form action="/moderator/<?= $location['id'] ?>/approve" method="POST">
                                    <button type="submit" class="btn btn-success btn-sm btn-block">Setujui (ACC)</button>
                                </form>
                                <form action="/moderator/<?= $location['id'] ?>/reject" method="POST">
                                    <button type="submit" class="btn btn-danger-outline btn-sm btn-block">Tolak</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
