<footer class="site-footer bg-white border-t border-slate-200/80 py-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
        <div>
            <span class="text-base font-extrabold text-slate-900">Jalan<span class="text-blue-600">Yuk!</span></span>
            <p class="text-xs text-slate-500 mt-0.5">Simpan, Kurasi, dan Jelajahi Tempat Menarik di Sekitar Anda.</p>
        </div>
        <div>
            <p class="text-xs text-slate-400">&copy; <?= date('Y') ?> Jalan Yuk! Platform Kurasi Tempat & Google Maps.</p>
        </div>
    </div>
</footer>

<!-- Bottom Navigation Bar for Mobile (PWA standard) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-t border-slate-200/80 z-40 flex items-center justify-around px-2 shadow-lg" aria-label="Navigasi Bawah Mobile">
    <a href="/" class="flex flex-col items-center justify-center flex-1 py-1 text-[11px] font-semibold transition-colors <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' ?>">
        <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span>Jelajah</span>
    </a>
    <a href="/map" class="flex flex-col items-center justify-center flex-1 py-1 text-[11px] font-semibold transition-colors <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/map' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' ?>">
        <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
        <span>Peta</span>
    </a>
    <a href="/locations/create" class="flex flex-col items-center justify-center flex-1 -mt-5" aria-label="Tambah Lokasi">
        <div class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </div>
        <span class="text-[10px] font-semibold text-slate-700 mt-1">Tambah</span>
    </a>
    <?php if (\App\Helpers\Auth::isModerator() || \App\Helpers\Auth::isAdmin()): ?>
        <a href="/moderator/queue" class="flex flex-col items-center justify-center flex-1 py-1 text-[11px] font-semibold transition-colors <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/moderator/queue' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' ?>">
            <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
            <span>Kurasi</span>
        </a>
    <?php endif; ?>
    <?php if (\App\Helpers\Auth::check()): ?>
        <a href="/my-locations" class="flex flex-col items-center justify-center flex-1 py-1 text-[11px] font-semibold transition-colors <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/my-locations' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' ?>">
            <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Koleksi</span>
        </a>
    <?php else: ?>
        <a href="/login" class="flex flex-col items-center justify-center flex-1 py-1 text-[11px] font-semibold transition-colors <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) === '/login' ? 'text-blue-600' : 'text-slate-500 hover:text-slate-900' ?>">
            <svg class="w-5 h-5 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            <span>Masuk</span>
        </a>
    <?php endif; ?>
</nav>

</div><!-- site-wrapper -->

<!-- Leaflet JS (Local high-performance with CDN fallback) -->
<script src="/assets/leaflet/leaflet.js"></script>

<!-- TikTok embed script -->
<script async src="https://www.tiktok.com/embed.js"></script>

<!-- App JS (with auto-cache busting) -->
<script src="/assets/js/app.js?v=<?= file_exists(dirname(__DIR__, 3) . '/public/assets/js/app.js') ? filemtime(dirname(__DIR__, 3) . '/public/assets/js/app.js') : time() ?>"></script>
</body>
</html>
