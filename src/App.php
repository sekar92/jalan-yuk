<?php

namespace App;

use App\Config\Database;
use App\Controllers\AdminController;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\LocationController;
use App\Controllers\MapController;
use App\Controllers\ModeratorController;
use App\Helpers\Auth;

class App
{
    public static function run(): void
    {
        Auth::initSession();

        // Simple router
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $isGet = in_array($method, ['GET', 'HEAD'], true);

        // Trim trailing slash except root
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        // --- Public & Home ---
        if ($uri === '/' && $isGet) {
            (new HomeController())->index();
            return;
        }

        // --- Auth Routes ---
        if ($uri === '/login' && $isGet) {
            (new AuthController())->showLogin();
            return;
        }
        if ($uri === '/login' && $method === 'POST') {
            (new AuthController())->login();
            return;
        }
        if ($uri === '/register' && $isGet) {
            (new AuthController())->showRegister();
            return;
        }
        if ($uri === '/register' && $method === 'POST') {
            (new AuthController())->register();
            return;
        }
        if ($uri === '/logout' && ($isGet || $method === 'POST')) {
            (new AuthController())->logout();
            return;
        }

        // --- Master Map View ---
        if ($uri === '/map' && $isGet) {
            (new MapController())->index();
            return;
        }

        // --- Locations ---
        if ($uri === '/locations/create' && $isGet) {
            (new LocationController())->create();
            return;
        }
        if ($uri === '/locations' && $method === 'POST') {
            (new LocationController())->store();
            return;
        }
        if ($uri === '/my-locations' && $isGet) {
            (new LocationController())->myLocations();
            return;
        }

        // Location detail: /locations/{id}
        if (preg_match('~^/locations/(\d+)$~', $uri, $matches) && $isGet) {
            (new LocationController())->show((int)$matches[1]);
            return;
        }
        // Location edit: /locations/{id}/edit
        if (preg_match('~^/locations/(\d+)/edit$~', $uri, $matches)) {
            if ($isGet) {
                (new LocationController())->edit((int)$matches[1]);
                return;
            }
            if ($method === 'POST') {
                (new LocationController())->update((int)$matches[1]);
                return;
            }
        }
        // Location delete: /locations/{id}/delete
        if (preg_match('~^/locations/(\d+)/delete$~', $uri, $matches) && $method === 'POST') {
            (new LocationController())->delete((int)$matches[1]);
            return;
        }
        // Location media delete: /locations/media/{id}/delete
        if (preg_match('~^/locations/media/(\d+)/delete$~', $uri, $matches) && $method === 'POST') {
            (new LocationController())->deleteMedia((int)$matches[1]);
            return;
        }

        // --- Admin Routes ---
        if ($uri === '/admin/users' && $isGet) {
            (new AdminController())->users();
            return;
        }
        if ($uri === '/admin/users' && $method === 'POST') {
            (new AdminController())->createUser();
            return;
        }
        if (preg_match('~^/admin/users/(\d+)/edit$~', $uri, $matches) && $method === 'POST') {
            (new AdminController())->updateUser((int)$matches[1]);
            return;
        }
        if (preg_match('~^/admin/users/(\d+)/delete$~', $uri, $matches) && $method === 'POST') {
            (new AdminController())->deleteUser((int)$matches[1]);
            return;
        }

        if ($uri === '/admin/categories' && $isGet) {
            (new AdminController())->categories();
            return;
        }
        if ($uri === '/admin/categories' && $method === 'POST') {
            (new AdminController())->createCategory();
            return;
        }
        if (preg_match('~^/admin/categories/(\d+)/edit$~', $uri, $matches) && $method === 'POST') {
            (new AdminController())->updateCategory((int)$matches[1]);
            return;
        }
        if (preg_match('~^/admin/categories/(\d+)/delete$~', $uri, $matches) && $method === 'POST') {
            (new AdminController())->deleteCategory((int)$matches[1]);
            return;
        }

        // --- Moderator Routes ---
        if ($uri === '/moderator/queue' && $isGet) {
            (new ModeratorController())->queue();
            return;
        }
        if (preg_match('~^/moderator/(\d+)/approve$~', $uri, $matches) && $method === 'POST') {
            (new ModeratorController())->approve((int)$matches[1]);
            return;
        }
        if (preg_match('~^/moderator/(\d+)/reject$~', $uri, $matches) && $method === 'POST') {
            (new ModeratorController())->reject((int)$matches[1]);
            return;
        }

        // --- API Routes ---
        if ($uri === '/api/extract-coords' && $method === 'POST') {
            (new ApiController())->extractCoords();
            return;
        }
        if ($uri === '/api/map-locations' && $isGet) {
            (new ApiController())->mapLocations();
            return;
        }

        // 404 fallback
        http_response_code(404);
        require_once dirname(__DIR__) . '/src/Views/errors/404.php';
    }
}
