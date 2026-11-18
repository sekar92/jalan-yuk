<?php
use App\Helpers\Auth;
$currentUser = Auth::user();
$flash = Auth::getFlash();
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2 text-slate-900 font-extrabold text-xl tracking-tight group" aria-label="Jalan Yuk Beranda">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
            <span>Jalan<span class="text-blue-600">Yuk!</span></span>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-1.5" aria-label="Menu Utama">
            <a href="/" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors duration-150 <?= $currentUri === '/' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Jelajah</span>
            </a>
            <a href="/map" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors duration-150 <?= $currentUri === '/map' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                <span>Peta Master</span>
            </a>

            <?php if (Auth::isModerator() || Auth::isAdmin()): ?>
                <a href="/moderator/queue" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors duration-150 <?= $currentUri === '/moderator/queue' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <span>Kurasi Lokasi</span>
                </a>
            <?php endif; ?>

            <?php if (Auth::isAdmin()): ?>
                <div class="nav-dropdown" id="admin-dropdown-wrapper">
                    <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-slate-600 hover:text-blue-600 hover:bg-slate-50 transition-colors duration-150" id="admin-menu-btn" type="button" aria-haspopup="true" aria-expanded="false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        <span>Admin</span>
                        <svg class="dropdown-chevron w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="nav-dropdown-menu" role="menu">
                        <a href="/admin/users" class="dropdown-item" role="menuitem">Kelola Pengguna</a>
                        <a href="/admin/categories" class="dropdown-item" role="menuitem">Master Kategori</a>
                    </div>
                </div>
            <?php endif; ?>
        </nav>

        <!-- Action / Auth buttons -->
        <div class="flex items-center gap-3">
            <?php if (Auth::check()): ?>
                <a href="/locations/create" class="btn btn-primary btn-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Tambah Tempat</span>
                </a>
                <div class="user-badge-menu" id="user-dropdown-wrapper">
                    <button type="button" class="user-pill user-pill-btn flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 hover:bg-white hover:border-blue-300 transition-all cursor-pointer" id="user-menu-btn" aria-expanded="false" aria-haspopup="true">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                            <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                        </span>
                        <span class="text-xs sm:text-sm font-semibold text-slate-800"><?= htmlspecialchars($currentUser['username']) ?></span>
                        <span class="role-pill role-<?= strtolower($currentUser['role']) ?>"><?= htmlspecialchars($currentUser['role']) ?></span>
                        <svg class="dropdown-chevron w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="user-dropdown" role="menu">
                        <a href="/my-locations" class="dropdown-item" role="menuitem">
                            <svg class="dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;min-width:16px;flex-shrink:0;" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Tempat Saya</span>
                        </a>
                        <div class="h-px bg-slate-200 my-1"></div>
                        <a href="/logout" class="dropdown-item text-danger" role="menuitem">
                            <svg class="dropdown-item-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;min-width:16px;flex-shrink:0;" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span>Keluar</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="/register" class="btn btn-primary btn-sm">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($flash): ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="p-4 rounded-xl flex items-center justify-between gap-3 text-sm font-medium border shadow-xs <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($flash['type'] === 'danger' ? 'bg-red-50 text-red-800 border-red-200' : 'bg-amber-50 text-amber-800 border-amber-200') ?>" role="alert">
            <div class="flex items-center gap-2">
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
            <button type="button" class="text-lg leading-none opacity-60 hover:opacity-100 cursor-pointer" onclick="this.parentElement.remove();" aria-label="Tutup pemberitahuan">&times;</button>
        </div>
    </div>
<?php endif; ?>
