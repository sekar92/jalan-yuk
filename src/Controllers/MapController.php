<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Location;

class MapController
{
    public function index(): void
    {
        $categories = Category::all();
        $locations = Location::allPublished();
        require_once dirname(__DIR__) . '/Views/map/index.php';
    }
}
