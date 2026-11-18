<?php
use App\Helpers\Auth;
$pageTitle = 'Kelola Pengguna - Admin Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola akses, role (Admin, Moderator, Contributor), dan kredensial pengguna sistem.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm" id="btn-open-create-user-modal">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-5 py-3.5">Username</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Terdaftar Sejak</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 text-xs font-mono text-slate-500">#<?= $u['id'] ?></td>
                            <td class="px-5 py-4">
                                <strong class="text-slate-900"><?= htmlspecialchars($u['username']) ?></strong>
                                <?php if ($u['id'] === Auth::id()): ?>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 ml-1.5">Anda</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="px-5 py-4">
                                <span class="role-pill role-<?= strtolower($u['role']) ?>">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-500"><?= date('d M Y, H:i', strtotime($u['created_at'])) ?></td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline btn-xs btn-edit-user"
                                        data-id="<?= $u['id'] ?>"
                                        data-username="<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                                        data-role="<?= $u['role'] ?>"
                                    >
                                        Edit
                                    </button>

                                    <?php if ($u['id'] !== Auth::id()): ?>
                                        <form action="/admin/users/<?= $u['id'] ?>/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna <?= htmlspecialchars($u['username'], ENT_QUOTES) ?>?');">
                                            <button type="submit" class="btn btn-danger-outline btn-xs">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Native Dialog Tambah Pengguna -->
<dialog id="modal-create-user" class="m-auto border-none rounded-3xl p-0 shadow-2xl max-w-lg w-[90%] bg-white backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm" aria-labelledby="dialog-create-title">
    <div class="p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <h2 id="dialog-create-title" class="text-xl font-bold text-slate-900">Tambah Pengguna Baru</h2>
            <button type="button" class="text-2xl text-slate-400 hover:text-slate-600 leading-none cursor-pointer" onclick="document.getElementById('modal-create-user').close();" aria-label="Tutup dialog">&times;</button>
        </div>
        <form action="/admin/users" method="POST" class="space-y-4">
            <div>
                <label for="new_username" class="form-label required">Username</label>
                <input type="text" name="username" id="new_username" class="form-control" required autofocus>
            </div>
            <div>
                <label for="new_email" class="form-label required">Email</label>
                <input type="email" name="email" id="new_email" class="form-control" required>
            </div>
            <div>
                <label for="new_password" class="form-label required">Password</label>
                <input type="password" name="password" id="new_password" class="form-control" required minlength="6">
            </div>
            <div>
                <label for="new_role" class="form-label required">Role Sistem</label>
                <select name="role" id="new_role" class="form-select" required>
                    <option value="Contributor">Contributor (Tambah & kelola konten sendiri)</option>
                    <option value="Moderator">Moderator (Kurasi & edit konten semua pengguna)</option>
                    <option value="Admin">Admin (Akses penuh ke seluruh sistem)</option>
                </select>
            </div>
            <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-create-user').close();">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Modal Native Dialog Edit Pengguna -->
<dialog id="modal-edit-user" class="m-auto border-none rounded-3xl p-0 shadow-2xl max-w-lg w-[90%] bg-white backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm" aria-labelledby="dialog-edit-title">
    <div class="p-6 sm:p-8">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
            <h2 id="dialog-edit-title" class="text-xl font-bold text-slate-900">Edit Pengguna</h2>
            <button type="button" class="text-2xl text-slate-400 hover:text-slate-600 leading-none cursor-pointer" onclick="document.getElementById('modal-edit-user').close();" aria-label="Tutup dialog">&times;</button>
        </div>
        <form id="form-edit-user" action="" method="POST" class="space-y-4">
            <div>
                <label for="edit_username" class="form-label required">Username</label>
                <input type="text" name="username" id="edit_username" class="form-control" required>
            </div>
            <div>
                <label for="edit_email" class="form-label required">Email</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div>
                <label for="edit_role" class="form-label required">Role Sistem</label>
                <select name="role" id="edit_role" class="form-select" required>
                    <option value="Contributor">Contributor</option>
                    <option value="Moderator">Moderator</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div>
                <label for="edit_password" class="form-label">Ubah Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="edit_password" class="form-control" placeholder="Password baru (opsional)" minlength="6">
            </div>
            <div class="pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-edit-user').close();">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Data</button>
            </div>
        </form>
    </div>
</dialog>

<script>
document.getElementById('btn-open-create-user-modal')?.addEventListener('click', () => {
    document.getElementById('modal-create-user').showModal();
});

document.querySelectorAll('.btn-edit-user').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        document.getElementById('form-edit-user').action = '/admin/users/' + id + '/edit';
        document.getElementById('edit_username').value = btn.dataset.username;
        document.getElementById('edit_email').value = btn.dataset.email;
        document.getElementById('edit_role').value = btn.dataset.role;
        document.getElementById('edit_password').value = '';
        document.getElementById('modal-edit-user').showModal();
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
