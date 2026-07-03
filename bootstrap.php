<?php
declare(strict_types=1);

// Vercel/TiDB production fix: jangan tampilkan Deprecated Warning ke response JSON.
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

function pdo_mysql_attr(string $name): ?int
{
    $newConstant = 'Pdo\\Mysql::ATTR_' . $name;
    if (class_exists('Pdo\\Mysql') && defined($newConstant)) {
        return constant($newConstant);
    }

    $oldConstant = 'PDO::MYSQL_ATTR_' . $name;
    if (defined($oldConstant)) {
        return constant($oldConstant);
    }

    return null;
}

$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Jakarta');

function db(): PDO
{
    static $pdo = null;
    global $config;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = $config['db'];

    foreach (['host' => 'DB_HOST', 'name' => 'DB_NAME', 'user' => 'DB_USER'] as $key => $envName) {
        if (trim((string) ($db[$key] ?? '')) === '') {
            throw new RuntimeException($envName . ' belum terbaca di Vercel Environment Variables.');
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'] ?? '3306',
        $db['name'],
        $db['charset'] ?? 'utf8mb4'
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $sslCa = (string) ($db['ssl_ca'] ?? '');
    $sslEnabled = (bool) ($db['ssl'] ?? false);

    $sslCaAttr = pdo_mysql_attr('SSL_CA');
    $sslVerifyAttr = pdo_mysql_attr('SSL_VERIFY_SERVER_CERT');

    if ($sslEnabled && $sslCaAttr !== null) {
        $candidateCaFiles = array_filter([
            $sslCa,
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem',
        ]);

        foreach ($candidateCaFiles as $candidateCaFile) {
            if (is_file($candidateCaFile)) {
                $options[$sslCaAttr] = $candidateCaFile;
                break;
            }
        }

        if ($sslVerifyAttr !== null) {
            $options[$sslVerifyAttr] = false;
        }
    } elseif ($sslCa !== '' && $sslCaAttr !== null && is_file($sslCa)) {
        $options[$sslCaAttr] = $sslCa;
    }

    $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);

    return $pdo;
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function input_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return $_POST;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;
}

function rupiah(float|int|string $value): string
{
    return 'Rp' . number_format((float) $value, 0, ',', '.');
}

function app_base_url(): string
{
    global $config;
    $configured = rtrim((string) ($config['app']['base_url'] ?? ''), '/');
    if ($configured !== '') {
        return $configured;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '' && getenv('VERCEL_PROJECT_PRODUCTION_URL')) {
        $host = (string) getenv('VERCEL_PROJECT_PRODUCTION_URL');
    }
    $host = $host !== '' ? $host : 'localhost';

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    $dir = rtrim(dirname($scriptName), '/.');

    return $scheme . '://' . $host . ($dir !== '' ? $dir : '');
}

function normalize_phone(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (str_starts_with($digits, '0')) {
        return '62' . substr($digits, 1);
    }
    if (str_starts_with($digits, '8')) {
        return '62' . $digits;
    }
    return $digits;
}

function transaction_code(): string
{
    return 'TRX-' . date('ymd-His') . '-' . random_int(10, 99);
}

function clean_text(string $value, int $max = 255): string
{
    $value = trim(strip_tags($value));
    return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
}


function normalize_image_path(?string $path): string
{
    $placeholder = 'assets/img/product-placeholder.svg';
    $path = trim(str_replace('\\', '/', (string) $path));

    if ($path === '') {
        return $placeholder;
    }

    if (preg_match('/[\x00-\x1F\x7F]/', $path) || str_contains($path, '..')) {
        return $placeholder;
    }

    $allowedPrefixes = ['assets/img/', 'uploads/'];
    $isAllowed = false;
    foreach ($allowedPrefixes as $prefix) {
        if (str_starts_with($path, $prefix)) {
            $isAllowed = true;
            break;
        }
    }

    if (!$isAllowed || !preg_match('/\.(svg|jpg|jpeg|png|webp)$/i', $path)) {
        return $placeholder;
    }

    return $path;
}

function ensure_writable_directory(string $directory): void
{
    if (getenv('VERCEL')) {
        throw new RuntimeException('Upload gambar lokal tidak tersedia di Vercel. Tambahkan produk tanpa upload gambar, atau pakai storage eksternal seperti Vercel Blob/Cloudinary.');
    }

    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new RuntimeException('Folder uploads tidak dapat dibuat.');
    }

    if (!is_writable($directory)) {
        throw new RuntimeException('Folder uploads tidak dapat ditulis.');
    }
}

function generate_unique_transaction_code(PDO $pdo): string
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE transaction_code = ?');

    for ($attempt = 0; $attempt < 10; $attempt++) {
        $code = transaction_code();
        $stmt->execute([$code]);
        if ((int) $stmt->fetchColumn() === 0) {
            return $code;
        }
    }

    return 'TRX-' . date('ymd-His') . '-' . bin2hex(random_bytes(3));
}

function fetch_transaction_by_token(string $token): ?array
{
    $stmt = db()->prepare('SELECT * FROM transactions WHERE receipt_token = ? LIMIT 1');
    $stmt->execute([$token]);
    $transaction = $stmt->fetch();
    if (!$transaction) {
        return null;
    }

    $items = db()->prepare(
        'SELECT ti.*, p.image FROM transaction_items ti LEFT JOIN products p ON p.id = ti.product_id WHERE ti.transaction_id = ? ORDER BY ti.id'
    );
    $items->execute([$transaction['id']]);
    $transaction['items'] = $items->fetchAll();

    return $transaction;
}
