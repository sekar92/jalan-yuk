<?php

// Check if running via PHP built-in webserver and file exists
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($filePath)) {
        return false;
    }
}

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Run Application
try {
    \App\App::run();
} catch (\Throwable $e) {
    error_log("[JalanYuk Error] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);

    $errorMessage = $e->getMessage();
    $errorTrace = $e->getTraceAsString();

    $errorView = dirname(__DIR__) . '/src/Views/errors/500.php';
    if (file_exists($errorView)) {
        require_once $errorView;
    } else {
        echo "<h1>500 Internal Server Error</h1><p>" . htmlspecialchars($errorMessage) . "</p>";
    }
}
