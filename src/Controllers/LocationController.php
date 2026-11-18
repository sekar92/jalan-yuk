<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\MapsHelper;
use App\Helpers\MediaHelper;
use App\Models\Category;
use App\Models\Location;
use App\Models\LocationMedia;

class LocationController
{
    public function show(int $id): void
    {
        $location = Location::findWithDetails($id);
        if (!$location) {
            http_response_code(404);
            require_once dirname(__DIR__) . '/Views/errors/404.php';
            return;
        }

        // Check if unapproved and user is not admin/moderator and not author
        if ($location['approval_status'] !== 'Published') {
            if (!Auth::check() || (!Auth::isAdmin() && !Auth::isModerator() && (int)$location['user_id'] !== Auth::id())) {
                http_response_code(403);
                require_once dirname(__DIR__) . '/Views/errors/403.php';
                return;
            }
        }

        require_once dirname(__DIR__) . '/Views/locations/show.php';
    }

    public function create(): void
    {
        if (!Auth::check()) {
            Auth::setFlash('warning', 'Silakan masuk terlebih dahulu untuk menambahkan tempat.');
            header('Location: /login');
            exit;
        }

        $categories = Category::all();
        require_once dirname(__DIR__) . '/Views/locations/create.php';
    }

    public function store(): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $mapsUrl = trim($_POST['maps_url'] ?? '');
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $visitStatus = $_POST['visit_status'] ?? 'Wishlist';
        $description = trim($_POST['description'] ?? '');
        $personalNotes = trim($_POST['personal_notes'] ?? '');

        if (empty($title) || empty($categoryId) || empty($mapsUrl)) {
            Auth::setFlash('danger', 'Nama Tempat, Kategori, dan Link Google Maps wajib diisi.');
            header('Location: /locations/create');
            exit;
        }

        // Auto extract coordinates if empty
        if ($latitude === null || $longitude === null) {
            $extracted = MapsHelper::extractCoordinates($mapsUrl);
            if ($extracted['latitude'] !== null && $extracted['longitude'] !== null) {
                $latitude = $extracted['latitude'];
                $longitude = $extracted['longitude'];
            }
        }

        // Approval status: Contributor => Pending; Moderator/Admin => Published
        $approvalStatus = (Auth::isAdmin() || Auth::isModerator()) ? 'Published' : 'Pending';

        $locationId = Location::create([
            'user_id' => Auth::id(),
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'personal_notes' => $personalNotes,
            'maps_url' => $mapsUrl,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'visit_status' => in_array($visitStatus, ['Wishlist', 'Visited', 'Favorite']) ? $visitStatus : 'Wishlist',
            'approval_status' => $approvalStatus,
        ]);

        // Process media URLs
        if (!empty($_POST['media_urls']) && is_array($_POST['media_urls'])) {
            foreach ($_POST['media_urls'] as $mUrl) {
                $mUrl = trim($mUrl);
                if (!empty($mUrl)) {
                    $mediaType = MediaHelper::detectMediaType($mUrl);
                    LocationMedia::create([
                        'location_id' => $locationId,
                        'media_type' => $mediaType,
                        'media_url' => $mUrl,
                        'file_path' => null,
                    ]);
                }
            }
        }

        // Process file upload (image)
        if (!empty($_FILES['image_file']['name'])) {
            $this->handleFileUpload($locationId, $_FILES['image_file']);
        }

        if ($approvalStatus === 'Pending') {
            Auth::setFlash('success', 'Lokasi berhasil ditambahkan! Lokasi Anda akan ditinjau oleh Moderator sebelum dipublikasikan.');
        } else {
            Auth::setFlash('success', 'Lokasi baru berhasil dipublikasikan!');
        }

        header('Location: /locations/' . $locationId);
        exit;
    }

    public function edit(int $id): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $location = Location::findWithDetails($id);
        if (!$location) {
            http_response_code(404);
            require_once dirname(__DIR__) . '/Views/errors/404.php';
            return;
        }

        if (!Auth::canManageLocation($location)) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            return;
        }

        $categories = Category::all();
        require_once dirname(__DIR__) . '/Views/locations/edit.php';
    }

    public function update(int $id): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $location = Location::findWithDetails($id);
        if (!$location || !Auth::canManageLocation($location)) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $mapsUrl = trim($_POST['maps_url'] ?? '');
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $visitStatus = $_POST['visit_status'] ?? 'Wishlist';
        $description = trim($_POST['description'] ?? '');
        $personalNotes = trim($_POST['personal_notes'] ?? '');

        if (empty($title) || empty($categoryId) || empty($mapsUrl)) {
            Auth::setFlash('danger', 'Nama Tempat, Kategori, dan Link Google Maps wajib diisi.');
            header('Location: /locations/' . $id . '/edit');
            exit;
        }

        if ($latitude === null || $longitude === null) {
            $extracted = MapsHelper::extractCoordinates($mapsUrl);
            if ($extracted['latitude'] !== null && $extracted['longitude'] !== null) {
                $latitude = $extracted['latitude'];
                $longitude = $extracted['longitude'];
            }
        }

        $dataToUpdate = [
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'personal_notes' => $personalNotes,
            'maps_url' => $mapsUrl,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'visit_status' => in_array($visitStatus, ['Wishlist', 'Visited', 'Favorite']) ? $visitStatus : 'Wishlist',
        ];

        // Moderator or Admin can also update approval status directly from edit form if provided
        if ((Auth::isAdmin() || Auth::isModerator()) && !empty($_POST['approval_status'])) {
            $dataToUpdate['approval_status'] = $_POST['approval_status'];
        }

        Location::update($id, $dataToUpdate);

        // Add additional media if supplied
        if (!empty($_POST['new_media_url'])) {
            $newUrl = trim($_POST['new_media_url']);
            $mediaType = MediaHelper::detectMediaType($newUrl);
            LocationMedia::create([
                'location_id' => $id,
                'media_type' => $mediaType,
                'media_url' => $newUrl,
                'file_path' => null,
            ]);
        }

        // Add file upload if provided
        if (!empty($_FILES['image_file']['name'])) {
            $this->handleFileUpload($id, $_FILES['image_file']);
        }

        Auth::setFlash('success', 'Data lokasi berhasil diperbarui.');
        header('Location: /locations/' . $id);
        exit;
    }

    public function delete(int $id): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $location = Location::findWithDetails($id);
        if (!$location || !Auth::canManageLocation($location)) {
            http_response_code(403);
            require_once dirname(__DIR__) . '/Views/errors/403.php';
            return;
        }

        LocationMedia::deleteByLocation($id);
        Location::delete($id);

        Auth::setFlash('success', 'Lokasi berhasil dihapus.');
        header('Location: /');
        exit;
    }

    public function deleteMedia(int $mediaId): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $locationId = (int)($_POST['location_id'] ?? 0);
        $location = Location::findWithDetails($locationId);
        if (!$location || !Auth::canManageLocation($location)) {
            http_response_code(403);
            exit;
        }

        LocationMedia::delete($mediaId);
        Auth::setFlash('success', 'Media berhasil dihapus.');
        header('Location: /locations/' . $locationId . '/edit');
        exit;
    }

    public function myLocations(): void
    {
        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $locations = Location::getByUser(Auth::id());
        require_once dirname(__DIR__) . '/Views/locations/my_locations.php';
    }

    private function handleFileUpload(int $locationId, array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            return;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'loc_' . $locationId . '_' . uniqid() . '.' . $ext;
        $targetDir = dirname(__DIR__, 2) . '/public/uploads';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            LocationMedia::create([
                'location_id' => $locationId,
                'media_type' => 'Image',
                'media_url' => '/uploads/' . $filename,
                'file_path' => $filename,
            ]);
        }
    }
}
