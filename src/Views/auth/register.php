<?php
$pageTitle = 'Daftar Akun Baru - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-lg">
        <header class="text-center mb-8">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-600 mb-2">Gabung Komunitas</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Jalan Yuk!</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Mulai tambahkan dan bagikan tempat favorit Anda.</p>
        </header>

        <form action="/register" method="POST" class="space-y-4">
            <div>
                <label for="username" class="form-label required">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Contoh: travel_seeker" required autofocus>
            </div>

            <div>
                <label for="email" class="form-label required">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" required>
            </div>

            <div>
                <label for="password" class="form-label required">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
            </div>

            <div>
                <label for="password_confirm" class="form-label required">Konfirmasi Password</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg mt-2 shadow-md">Daftar Sekarang</button>

            <div class="text-center text-xs text-slate-500 pt-2">
                Sudah memiliki akun? <a href="/login" class="text-blue-600 font-semibold hover:underline">Masuk di sini</a>
            </div>
        </form>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
