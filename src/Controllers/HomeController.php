<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Location;

class HomeController
{
    public function index(): void
    {
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
        $categories = Category::all();

        require_once dirname(__DIR__) . '/Views/home/index.php';
    }
}
