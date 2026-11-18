<?php

require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\Database;

echo "=== Jalan Yuk! Database Initializer ===\n";
Database::loadEnv(__DIR__ . '/../.env');

$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';
echo "Target connection: {$driver}\n";

try {
    $pdo = Database::getConnection();
    $activeDriver = Database::getDriver();
    echo "Connected using driver: {$activeDriver}\n";

    if ($activeDriver === 'mysql') {
        echo "Executing database/schema.sql...\n";
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($schema);
        echo "Executing database/seeds.sql...\n";
        $seeds = file_get_contents(__DIR__ . '/seeds.sql');
        $pdo->exec($seeds);
        echo "MySQL setup complete!\n";
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
