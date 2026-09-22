<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config;

Config::init();

// Set default timezone if needed
date_default_timezone_set('Asia/Jakarta');

// Common CORS and dynamic response settings
function apiResponse(int $code, bool $success, string $message, $data = null): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
