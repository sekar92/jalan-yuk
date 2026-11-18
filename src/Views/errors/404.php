<?php
$pageTitle = 'Halaman Tidak Ditemukan - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-sm">
        <span class="text-6xl font-black text-blue-600 block mb-2">404</span>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mb-2">Halaman Tidak Ditemukan</h1>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">Ups! Tempat atau halaman yang Anda cari mungkin sudah dipindahkan atau tidak ada.</p>
        <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
