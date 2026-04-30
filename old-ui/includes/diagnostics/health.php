<?php
header('Content-Type: text/plain; charset=utf-8');

echo "UNIDA Gateway Includes Health Check\n";
echo "==================================\n\n";

$root = dirname(__DIR__, 2);

$files = [
    'includes/config.php',
    'includes/header.php',
    'includes/footer.php',
    'includes/sidebar.php',
    'assets/css/main.css',
    'assets/css/dashboard.css',
    'assets/css/legal.css',
    'assets/js/main.js',
];

foreach ($files as $file) {
    echo str_pad($file, 36) . ': ' . (file_exists($root . '/' . $file) ? 'FOUND' : 'MISSING') . "\n";
}

echo "\nLoading config...\n";

try {
    require_once $root . '/includes/config.php';
    echo "config.php: OK\n";
    echo "BASE_URL: " . BASE_URL . "\n";

    try {
        db();
        echo "database: OK\n";
    } catch (Throwable $e) {
        echo "database: FAILED - " . $e->getMessage() . "\n";
    }
} catch (Throwable $e) {
    echo "config.php: FAILED - " . $e->getMessage() . "\n";
}

echo "\nDelete this file after testing.\n";
