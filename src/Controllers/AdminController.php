<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Category;
use App\Models\User;

class AdminController
{
    private function ensureAdmin(): void
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            exit;
        }
    }

    // --- User Management ---

    public function users(): void
    {
        $this->ensureAdmin();
        $users = User::all();
        require_once dirname(__DIR__) . '/Views/admin/users.php';
    }

    public function createUser(): void
    {
        $this->ensureAdmin();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'Contributor';

        if (empty($username) || empty($email) || empty($password)) {
            Auth::setFlash('danger', 'Semua bidang wajib diisi.');
            header('Location: /admin/users');
            exit;
        }

        try {
            User::create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => in_array($role, ['Admin', 'Moderator', 'Contributor']) ? $role : 'Contributor',
            ]);
            Auth::setFlash('success', "Pengguna {$username} berhasil dibuat.");
        } catch (\PDOException $e) {
            Auth::setFlash('danger', 'Gagal membuat pengguna. Username atau Email mungkin sudah digunakan.');
        }

        header('Location: /admin/users');
        exit;
    }

    public function updateUser(int $id): void
    {
        $this->ensureAdmin();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'Contributor';
        $password = $_POST['password'] ?? '';

        $data = [
            'username' => $username,
            'email' => $email,
            'role' => in_array($role, ['Admin', 'Moderator', 'Contributor']) ? $role : 'Contributor',
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        User::update($id, $data);
        Auth::setFlash('success', 'Data pengguna berhasil diperbarui.');
        header('Location: /admin/users');
        exit;
    }

    public function deleteUser(int $id): void
    {
        $this->ensureAdmin();

        if ($id === Auth::id()) {
            Auth::setFlash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
            header('Location: /admin/users');
            exit;
        }

        User::delete($id);
        Auth::setFlash('success', 'Pengguna berhasil dihapus.');
        header('Location: /admin/users');
        exit;
    }

    // --- Master Category Management ---

    public function categories(): void
    {
        $this->ensureAdmin();
        $categories = Category::all();
        require_once dirname(__DIR__) . '/Views/admin/categories.php';
    }

    public function createCategory(): void
    {
        $this->ensureAdmin();

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Auth::setFlash('danger', 'Nama kategori wajib diisi.');
            header('Location: /admin/categories');
            exit;
        }

        Category::create([
            'name' => $name,
            'slug' => !empty($slug) ? $slug : $name,
            'description' => $description,
        ]);

        Auth::setFlash('success', "Kategori '{$name}' berhasil ditambahkan.");
        header('Location: /admin/categories');
        exit;
    }

    public function updateCategory(int $id): void
    {
        $this->ensureAdmin();

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            Auth::setFlash('danger', 'Nama kategori wajib diisi.');
            header('Location: /admin/categories');
            exit;
        }

        Category::update($id, [
            'name' => $name,
            'slug' => !empty($slug) ? $slug : $name,
            'description' => $description,
        ]);

        Auth::setFlash('success', 'Kategori berhasil diperbarui.');
        header('Location: /admin/categories');
        exit;
    }

    public function deleteCategory(int $id): void
    {
        $this->ensureAdmin();

        try {
            Category::delete($id);
            Auth::setFlash('success', 'Kategori berhasil dihapus.');
        } catch (\PDOException $e) {
            Auth::setFlash('danger', 'Gagal menghapus kategori: Masih terdapat lokasi yang terdaftar pada kategori ini.');
        }

        header('Location: /admin/categories');
        exit;
    }
}
