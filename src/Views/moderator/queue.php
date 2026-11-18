<?php
$pageTitle = 'Antrean Kurasi Konten - Moderator Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 space-y-10">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kurasi & Peninjauan Lokasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Tinjau kiriman lokasi dari Kontributor, setujui (ACC), tolak, atau perbaiki deskripsi dan link yang rusak.</p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <?= count($pendingLocations) ?> lokasi menunggu kurasi
            </span>
        </div>
    </div>

    <!-- Section 1: Antrean Pending -->
    <section class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>⏳</span> Menunggu Persetujuan (Pending Queue)
        </h2>

        <?php if (empty($pendingLocations)): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Kerja bagus! Tidak ada kiriman lokasi yang menunggu kurasi saat ini.</span>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($pendingLocations as $loc): ?>
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                        <div class="flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="badge badge-category"><?= htmlspecialchars($loc['category_name']) ?></span>
                                <span class="badge badge-status-<?= strtolower($loc['visit_status']) ?>"><?= htmlspecialchars($loc['visit_status']) ?></span>
                                <span class="text-slate-500">Diajukan oleh: <strong class="text-slate-700"><?= htmlspecialchars($loc['author_name']) ?></strong></span>
                                <span class="text-slate-400">&bull; <?= date('d M Y, H:i', strtotime($loc['created_at'])) ?></span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900">
                                <a href="/locations/<?= $loc['id'] ?>" target="_blank" class="hover:text-blue-600 transition-colors inline-flex items-center gap-1">
                                    <span><?= htmlspecialchars($loc['title']) ?></span>
                                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </h3>

                            <p class="text-sm text-slate-600 leading-relaxed"><?= htmlspecialchars($loc['description'] ?? 'Tidak ada deskripsi.') ?></p>

                            <?php if (!empty($loc['personal_notes'])): ?>
                                <div class="bg-amber-50/80 border-l-2 border-amber-400 px-3 py-1.5 rounded-r text-xs text-amber-900">
                                    <strong>Tips / Notes:</strong> <?= htmlspecialchars($loc['personal_notes']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex items-center gap-4 text-xs pt-1">
                                <a href="<?= htmlspecialchars($loc['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-700">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    Cek Link Google Maps
                                </a>
                                <?php if ($loc['latitude'] && $loc['longitude']): ?>
                                    <span class="font-mono text-slate-500">Lat: <?= $loc['latitude'] ?>, Lng: <?= $loc['longitude'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row md:flex-col gap-2 w-full md:w-44 flex-shrink-0">
                            <form action="/moderator/<?= $loc['id'] ?>/approve" method="POST" class="w-full">
                                <button type="submit" class="btn btn-success btn-block">
                                    ✓ Setujui (ACC)
                                </button>
                            </form>

                            <form action="/moderator/<?= $loc['id'] ?>/reject" method="POST" class="w-full" onsubmit="return confirm('Tolak lokasi ini?');">
                                <button type="submit" class="btn btn-danger-outline btn-block">
                                    ✕ Tolak (Reject)
                                </button>
                            </form>

                            <a href="/locations/<?= $loc['id'] ?>/edit" class="btn btn-outline btn-block btn-xs text-center">
                                ✎ Edit / Koreksi Link
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Section 2: Riwayat Semua Lokasi -->
    <section class="space-y-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>📋</span> Semua Riwayat Kiriman Lokasi
        </h2>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3.5">ID</th>
                            <th class="px-5 py-3.5">Nama Tempat</th>
                            <th class="px-5 py-3.5">Author</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Status Kurasi</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($allLocations as $loc): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-mono text-slate-500">#<?= $loc['id'] ?></td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    <a href="/locations/<?= $loc['id'] ?>" class="hover:text-blue-600 transition-colors">
                                        <?= htmlspecialchars($loc['title']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-xs"><?= htmlspecialchars($loc['author_name']) ?></td>
                                <td class="px-5 py-4"><span class="badge badge-category"><?= htmlspecialchars($loc['category_name']) ?></span></td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-approval-<?= strtolower($loc['approval_status']) ?>">
                                        <?= htmlspecialchars($loc['approval_status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500"><?= date('d M Y', strtotime($loc['created_at'])) ?></td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="/locations/<?= $loc['id'] ?>" class="btn btn-ghost btn-xs">Lihat</a>
                                        <a href="/locations/<?= $loc['id'] ?>/edit" class="btn btn-outline btn-xs">Koreksi/Edit</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
