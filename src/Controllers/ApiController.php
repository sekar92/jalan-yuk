<?php

namespace App\Controllers;

use App\Helpers\MapsHelper;
use App\Models\Location;

class ApiController
{
    public function extractCoords(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $url = $_POST['url'] ?? '';
        if (empty($url)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $url = $input['url'] ?? '';
        }

        $result = MapsHelper::extractCoordinates($url);
        echo json_encode([
            'success' => $result['latitude'] !== null && $result['longitude'] !== null,
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'resolved_url' => $result['resolved_url'],
            'error' => $result['error'],
        ]);
        exit;
    }

    public function mapLocations(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $categoryId = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : null;
        $visitStatus = isset($_GET['status']) && in_array($_GET['status'], ['Wishlist', 'Visited', 'Favorite']) ? $_GET['status'] : null;
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;

        $filters = [];
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        if ($visitStatus) {
            $filters['visit_status'] = $visitStatus;
        }
        if (!empty($search)) {
            $filters['search'] = $search;
        }

        $locations = Location::allPublished($filters);

        // Filter only those with coordinates
        $data = [];
        foreach ($locations as $loc) {
            if ($loc['latitude'] !== null && $loc['longitude'] !== null) {
                $data[] = [
                    'id' => (int)$loc['id'],
                    'title' => $loc['title'],
                    'latitude' => (float)$loc['latitude'],
                    'longitude' => (float)$loc['longitude'],
                    'category_name' => $loc['category_name'],
                    'category_slug' => $loc['category_slug'],
                    'visit_status' => $loc['visit_status'],
                    'personal_notes' => $loc['personal_notes'],
                    'maps_url' => $loc['maps_url'],
                    'thumbnail_url' => !empty($loc['thumbnail_path']) ? '/uploads/' . $loc['thumbnail_path'] : $loc['thumbnail_url'],
                    'detail_url' => '/locations/' . $loc['id'],
                ];
            }
        }

        echo json_encode(['locations' => $data]);
        exit;
    }
}
