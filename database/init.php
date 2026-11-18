<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

echo "=== Jalan Yuk! Database Initializer ===\n";
Database::loadEnv(__DIR__ . '/../.env');

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';
echo "Target connection: {$driver}\n";

if ($driver === 'mysql') {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_DATABASE'] ?? 'jalan_yuk';
    $user = $_ENV['DB_USERNAME'] ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';

    // Try to ensure database exists if user has permissions
    try {
        $serverPdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        echo "Database `{$dbname}` verified/ready.\n";
    } catch (Throwable $e) {
        // Pre-existing database or restricted user privileges
    }
}

try {
    $pdo = Database::getConnection();
    $activeDriver = Database::getDriver();
    echo "Connected using driver: {$activeDriver}\n";

    if ($activeDriver === 'mysql') {
        $dbname = $_ENV['DB_DATABASE'] ?? 'jalan_yuk';
        $pdo->exec("USE `{$dbname}`;");

        echo "Executing database/schema.sql on `{$dbname}`...\n";
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($schema);
        echo "Executing database/seeds.sql on `{$dbname}`...\n";
        $seeds = file_get_contents(__DIR__ . '/seeds.sql');
        $pdo->exec($seeds);
        echo "MySQL setup complete on database `{$dbname}`!\n";
    } else {
        echo "SQLite auto-setup executed successfully!\n";
    }

    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM users");
    echo "Total users: " . $stmt->fetch()['c'] . "\n";
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM locations");
    echo "Total locations: " . $stmt->fetch()['c'] . "\n";
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM categories");
    echo "Total categories: " . $stmt->fetch()['c'] . "\n";
    echo "Database ready to use!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
