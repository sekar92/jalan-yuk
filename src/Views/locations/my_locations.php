<?php
$pageTitle = 'Tempat Saya - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Koleksi Tempat Saya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar semua lokasi yang telah Anda tambahkan beserta status kurasi masing-masing.</p>
        </div>
        <div>
            <a href="/locations/create" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Tempat Baru</span>
            </a>
        </div>
    </div>

    <?php if (empty($locations)): ?>
        <div class="text-center py-16 px-4 bg-white rounded-3xl border border-dashed border-slate-300 shadow-sm">
            <div class="text-5xl mb-3">📍</div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Anda Belum Menambahkan Lokasi</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">Mulai simpan tempat kuliner, tempat nongkrong, atau servis favorit Anda sekarang!</p>
            <a href="/locations/create" class="btn btn-primary">Tambah Tempat Pertama</a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3.5">Nama Tempat</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Status Kunjungan</th>
                            <th class="px-5 py-3.5">Status Kurasi</th>
                            <th class="px-5 py-3.5">Media</th>
                            <th class="px-5 py-3.5">Dibuat</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($locations as $loc): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    <a href="/locations/<?= $loc['id'] ?>" class="hover:text-blue-600 transition-colors">
                                        <?= htmlspecialchars($loc['title']) ?>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-category"><?= htmlspecialchars($loc['category_name']) ?></span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-status-<?= strtolower($loc['visit_status']) ?>">
                                        <?= htmlspecialchars($loc['visit_status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="badge badge-approval-<?= strtolower($loc['approval_status']) ?>">
                                        <?= htmlspecialchars($loc['approval_status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500"><?= $loc['media_count'] ?> media</td>
                                <td class="px-5 py-4 text-xs text-slate-500"><?= date('d M Y', strtotime($loc['created_at'])) ?></td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="/locations/<?= $loc['id'] ?>" class="btn btn-ghost btn-xs">Lihat</a>
                                        <a href="/locations/<?= $loc['id'] ?>/edit" class="btn btn-outline btn-xs">Edit</a>
                                        <form action="/locations/<?= $loc['id'] ?>/delete" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                            <button type="submit" class="btn btn-danger-outline btn-xs">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
