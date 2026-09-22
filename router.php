<?php
// router.php for PHP Built-in Server local compatibility

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

// If requesting any route on /api/
if (strpos($path, '/api/') === 0) {
    require_once __DIR__ . '/api/index.php';
    exit;
}

// Let static files be served directly by the server
if (file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
    return false;
}

// Fallback to index.php
require_once __DIR__ . '/index.php';
