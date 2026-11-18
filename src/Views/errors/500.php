<?php
$pageTitle = '500 - Terjadi Kesalahan Server - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="max-w-md w-full text-center bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-2xl">
            500
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2">Terjadi Kesalahan Server</h1>
        <p class="text-slate-600 text-sm mb-6 leading-relaxed">
            Maaf, sistem mendapati kendala saat memproses permintaan Anda. Silakan coba beberapa saat lagi atau hubungi administrator.
        </p>

        <?php if (!empty($errorMessage) && filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)): ?>
            <div class="text-left bg-slate-900 text-red-300 p-4 rounded-xl text-xs font-mono mb-6 overflow-x-auto">
                <div class="font-bold text-red-400 mb-1">Debug Error Info:</div>
                <div><?= htmlspecialchars($errorMessage) ?></div>
                <?php if (!empty($errorTrace)): ?>
                    <pre class="mt-2 text-slate-400 text-[11px] overflow-x-auto whitespace-pre-wrap"><?= htmlspecialchars($errorTrace) ?></pre>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-center gap-3">
            <a href="/" class="btn btn-primary btn-sm">Kembali ke Beranda</a>
            <a href="javascript:location.reload()" class="btn btn-outline btn-sm">Muat Ulang</a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
