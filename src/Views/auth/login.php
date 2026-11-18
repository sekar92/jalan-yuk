<?php
$pageTitle = 'Masuk - Jalan Yuk!';
require_once dirname(__DIR__) . '/layouts/header.php';
require_once dirname(__DIR__) . '/layouts/navbar.php';
?>

<main class="flex-1 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-10 shadow-lg">
        <header class="text-center mb-8">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-600 mb-2">Selamat Datang</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Masuk ke Jalan Yuk!</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola daftar lokasi, wishlist liburan, dan tips kuliner favorit Anda.</p>
        </header>

        <form action="/login" method="POST" class="space-y-4">
            <div>
                <label for="username_or_email" class="form-label required">Username atau Email</label>
                <input type="text" name="username_or_email" id="username_or_email" class="form-control" placeholder="admin atau admin@jalanyuk.test" required autofocus>
            </div>

            <div>
                <label for="password" class="form-label required">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg mt-2 shadow-md">Masuk</button>

            <div class="text-center text-xs text-slate-500 pt-2">
                Belum punya akun? <a href="/register" class="text-blue-600 font-semibold hover:underline">Daftar sekarang</a>
            </div>
        </form>

        <!-- Quick Demo Accounts Helper -->
        <div class="mt-8 pt-6 border-t border-dashed border-slate-200">
            <div class="text-xs font-semibold text-slate-500 mb-2.5">Akun Uji Coba Cepat (Password: <code>password123</code>):</div>
            <div class="flex gap-2">
                <button type="button" class="btn btn-outline btn-xs flex-1 btn-fill-demo cursor-pointer" data-user="admin">Admin</button>
                <button type="button" class="btn btn-outline btn-xs flex-1 btn-fill-demo cursor-pointer" data-user="moderator">Moderator</button>
                <button type="button" class="btn btn-outline btn-xs flex-1 btn-fill-demo cursor-pointer" data-user="contributor">Contributor</button>
            </div>
        </div>
    </div>
</main>

<script>
document.querySelectorAll('.btn-fill-demo').forEach(btn => {
    btn.addEventListener('click', () => {
        const u = btn.getAttribute('data-user');
        document.getElementById('username_or_email').value = u;
        document.getElementById('password').value = 'password123';
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>
