<?php
// Vercel PHP router for POSREIYA.
// File ini wajib berada di: api/index.php

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$root = dirname(__DIR__);

$routes = [
    '/' => 'index.php',
    '/index.php' => 'index.php',
    '/api.php' => 'api.php',
    '/health.php' => 'health.php',
    '/setup.php' => 'setup.php',
    '/receipt.php' => 'receipt.php',
    '/print_receipt.php' => 'print_receipt.php',
    '/download_receipt.php' => 'download_receipt.php',
    '/preview.html' => 'preview.html',
];

if (isset($routes[$path])) {
    require $root . '/' . $routes[$path];
    exit;
}

$basename = basename($path);
$allowedPhp = [
    'api.php',
    'health.php',
    'setup.php',
    'receipt.php',
    'print_receipt.php',
    'download_receipt.php',
    'index.php',
];

if (in_array($basename, $allowedPhp, true) && file_exists($root . '/' . $basename)) {
    require $root . '/' . $basename;
    exit;
}

require $root . '/index.php';
