<?php
use App\Helpers\Auth;
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Jalan Yuk! - Penanda Lokasi & Wisata Favorit') ?></title>
    
    <!-- Meta tags for PWA & SEO -->
    <meta name="description" content="Simpan link Google Maps, review media sosial TikTok/IG/YouTube, dan temukan tempat nongkrong, kuliner, dan servis favorit Anda bersama Jalan Yuk!">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/assets/icons/icon.svg">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">

    <!-- Leaflet CSS (Local high-performance with CDN fallback) -->
    <link rel="stylesheet" href="/assets/leaflet/leaflet.css">

    <!-- App Stylesheet (with auto-cache busting) -->
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= file_exists(dirname(__DIR__, 3) . '/public/assets/css/app.css') ? filemtime(dirname(__DIR__, 3) . '/public/assets/css/app.css') : time() ?>">
</head>
<body>
<div class="site-wrapper">
