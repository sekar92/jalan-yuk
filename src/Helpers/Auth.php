<?php

namespace App\Helpers;

use App\Config\Database;
use PDO;

class Auth
{
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::initSession();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::initSession();
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        // Return cached or fetch fresh
        if (!isset($_SESSION['user_data'])) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();
            if (!$user) {
                self::logout();
                return null;
            }
            $_SESSION['user_data'] = $user;
        }

        return $_SESSION['user_data'];
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): string
    {
        $user = self::user();
        return $user['role'] ?? 'Guest';
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'Admin';
    }

    public static function isModerator(): bool
    {
        $role = self::role();
        return $role === 'Moderator' || $role === 'Admin';
    }

    public static function isContributor(): bool
    {
        return self::role() === 'Contributor';
    }

    public static function canManageLocation(array $location): bool
    {
        if (self::isAdmin() || self::isModerator()) {
            return true;
        }
        return self::id() !== null && (int)$location['user_id'] === self::id();
    }

    public static function login(string $usernameOrEmail, string $password): bool
    {
        self::initSession();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([
            ':username' => $usernameOrEmail,
            ':email' => $usernameOrEmail,
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_data'] = [
                'id' => (int)$user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'created_at' => $user['created_at'],
            ];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function refresh(): void
    {
        self::initSession();
        unset($_SESSION['user_data']);
        self::user();
    }

    public static function setFlash(string $type, string $message): void
    {
        self::initSession();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function getFlash(): ?array
    {
        self::initSession();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
