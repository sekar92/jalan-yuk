<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        require_once dirname(__DIR__) . '/Views/auth/login.php';
    }

    public function login(): void
    {
        $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usernameOrEmail) || empty($password)) {
            Auth::setFlash('danger', 'Username/Email dan Password wajib diisi.');
            header('Location: /login');
            exit;
        }

        try {
            if (Auth::login($usernameOrEmail, $password)) {
                Auth::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars(Auth::user()['username']) . '!');
                header('Location: /');
                exit;
            }

            Auth::setFlash('danger', 'Username/Email atau Password tidak valid.');
            header('Location: /login');
            exit;
        } catch (\Throwable $e) {
            error_log("[Login Error] " . $e->getMessage());
            Auth::setFlash('danger', 'Terjadi kesalahan sistem saat proses masuk: ' . htmlspecialchars($e->getMessage()));
            header('Location: /login');
            exit;
        }
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
        require_once dirname(__DIR__) . '/Views/auth/register.php';
    }

    public function register(): void
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            Auth::setFlash('danger', 'Semua kolom registrasi wajib diisi.');
            header('Location: /register');
            exit;
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            Auth::setFlash('danger', 'Username harus antara 3 - 50 karakter.');
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Auth::setFlash('danger', 'Format email tidak valid.');
            header('Location: /register');
            exit;
        }

        if ($password !== $passwordConfirm) {
            Auth::setFlash('danger', 'Konfirmasi password tidak cocok.');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            Auth::setFlash('danger', 'Password minimal 6 karakter.');
            header('Location: /register');
            exit;
        }

        try {
            User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => 'Contributor',
            ]);

            Auth::login($username, $password);
            Auth::setFlash('success', 'Akun berhasil dibuat! Selamat bergabung di Jalan Yuk!');
            header('Location: /');
            exit;
        } catch (\PDOException $e) {
            $msg = 'Gagal mendaftar. Username atau email mungkin sudah terdaftar.';
            Auth::setFlash('danger', $msg);
            header('Location: /register');
            exit;
        }
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
