<?php
// Satu-satunya entrypoint PHP untuk Vercel.
// Jangan tambah file PHP lain di folder /api agar Vercel tidak salah membaca function.

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$root = dirname(__DIR__);

switch ($path) {
    case '/api.php':
        require $root . '/api.php';
        break;

    case '/setup.php':
        require $root . '/setup.php';
        break;

    case '/receipt.php':
        require $root . '/receipt.php';
        break;

    case '/print_receipt.php':
        require $root . '/print_receipt.php';
        break;

    case '/download_receipt.php':
        require $root . '/download_receipt.php';
        break;

    case '/health.php':
        require $root . '/health.php';
        break;

    default:
        require $root . '/index.php';
        break;
}
