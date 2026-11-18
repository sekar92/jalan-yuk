<?php
$pageTitle = 'Master Kategori - Admin Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Master Kategori</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola daftar pengelompokan tempat (Kulineran, Hidden Gem, Servis Motor, dll.).</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm" id="btn-open-create-category-modal">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-5 py-3.5">Nama Kategori</th>
                        <th class="px-5 py-3.5">Slug</th>
                        <th class="px-5 py-3.5">Deskripsi</th>
                        <th class="px-5 py-3.5">Dibuat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 text-xs font-mono text-slate-500">#<?= $cat['id'] ?></td>
                            <td class="px-5 py-4 font-bold text-slate-900"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="px-5 py-4"><code class="text-xs font-mono px-2 py-1 bg-slate-100 text-slate-700 rounded"><?= htmlspecialchars($cat['slug']) ?></code></td>
                            <td class="px-5 py-4 text-xs text-slate-600 max-w-xs truncate"><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                            <td class="px-5 py-4 text-xs text-slate-500"><?= date('d M Y', strtotime($cat['created_at'])) ?></td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline btn-xs btn-edit-category"
                                        data-id="<?= $cat['id'] ?>"
                                        data-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>"
                                        data-slug="<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>"
                                        data-description="<?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES) ?>"
                                    >
                                        Edit
                                    </button>

                                    <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
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
</main>

<!-- Modal Native Dialog Tambah Kategori -->
<dialog id="modal-create-category" class="m-auto border-none rounded-3xl p-0 shadow-2xl max-w-lg w-[90%] bg-white backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm" aria-labelledby="dialog-create-cat-title">
    <div class="p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <h2 id="dialog-create-cat-title" class="text-xl font-bold text-slate-900">Tambah Kategori Baru</h2>
            <button type="button" class="text-2xl text-slate-400 hover:text-slate-600 leading-none cursor-pointer" onclick="document.getElementById('modal-create-category').close();" aria-label="Tutup dialog">&times;</button>
        </div>
        <form action="/admin/categories" method="POST" class="space-y-4">
            <div>
                <label for="new_cat_name" class="form-label required">Nama Kategori</label>
                <input type="text" name="name" id="new_cat_name" class="form-control" placeholder="Contoh: Street Food Malam" required autofocus>
            </div>
            <div>
                <label for="new_cat_slug" class="form-label">Slug (Opsional)</label>
                <input type="text" name="slug" id="new_cat_slug" class="form-control" placeholder="street-food-malam (otomatis jika kosong)">
            </div>
            <div>
                <label for="new_cat_desc" class="form-label">Deskripsi</label>
                <textarea name="description" id="new_cat_desc" rows="3" class="form-control" placeholder="Penjelasan tentang kategori tempat ini"></textarea>
            </div>
            <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-create-category').close();">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Native Dialog Edit Kategori -->
<dialog id="modal-edit-category" class="m-auto border-none rounded-3xl p-0 shadow-2xl max-w-lg w-[90%] bg-white backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm" aria-labelledby="dialog-edit-cat-title">
    <div class="p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <h2 id="dialog-edit-cat-title" class="text-xl font-bold text-slate-900">Edit Kategori</h2>
            <button type="button" class="text-2xl text-slate-400 hover:text-slate-600 leading-none cursor-pointer" onclick="document.getElementById('modal-edit-category').close();" aria-label="Tutup dialog">&times;</button>
        </div>
        <form id="form-edit-category" action="" method="POST" class="space-y-4">
            <div>
                <label for="edit_cat_name" class="form-label required">Nama Kategori</label>
                <input type="text" name="name" id="edit_cat_name" class="form-control" required>
            </div>
            <div>
                <label for="edit_cat_slug" class="form-label required">Slug</label>
                <input type="text" name="slug" id="edit_cat_slug" class="form-control" required>
            </div>
            <div>
                <label for="edit_cat_desc" class="form-label">Deskripsi</label>
                <textarea name="description" id="edit_cat_desc" rows="3" class="form-control"></textarea>
            </div>
            <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit-category').close();">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
            </div>
        </form>
    </div>
</dialog>

<script>
document.getElementById('btn-open-create-category-modal')?.addEventListener('click', () => {
    document.getElementById('modal-create-category').showModal();
});

document.querySelectorAll('.btn-edit-category').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        document.getElementById('form-edit-category').action = '/admin/categories/' + id + '/edit';
        document.getElementById('edit_cat_name').value = btn.dataset.name;
        document.getElementById('edit_cat_slug').value = btn.dataset.slug;
        document.getElementById('edit_cat_desc').value = btn.dataset.description;
        document.getElementById('modal-edit-category').showModal();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
