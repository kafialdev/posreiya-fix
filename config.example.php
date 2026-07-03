<?php
// Konfigurasi final untuk Vercel + database MySQL online.
// Tidak perlu edit file ini. Isi data database lewat Vercel Environment Variables.

function env_value(array $keys, string $default = ''): string
{
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false && trim((string) $value) !== '') {
            return trim((string) $value);
        }
    }
    return $default;
}

return [
    'db' => [
        // Bisa pakai DB_* manual atau PLANETSCALE_* dari Vercel Marketplace.
        'host' => env_value(['DB_HOST', 'MYSQL_HOST', 'PLANETSCALE_DB_HOST'], 'localhost'),
        'port' => env_value(['DB_PORT', 'MYSQL_PORT', 'PLANETSCALE_DB_PORT'], '3306'),
        'name' => env_value(['DB_NAME', 'MYSQL_DATABASE', 'PLANETSCALE_DB'], 'elegant_pos'),
        'user' => env_value(['DB_USER', 'MYSQL_USER', 'PLANETSCALE_DB_USERNAME'], 'root'),
        'pass' => env_value(['DB_PASS', 'DB_PASSWORD', 'MYSQL_PASSWORD', 'PLANETSCALE_DB_PASSWORD'], ''),
        'charset' => env_value(['DB_CHARSET'], 'utf8mb4'),
        // Opsional untuk provider MySQL yang mewajibkan SSL, misalnya PlanetScale.
        'ssl_ca' => env_value(['MYSQL_ATTR_SSL_CA', 'DB_SSL_CA', 'PLANETSCALE_SSL_CERT_PATH'], ''),
    ],
    'app' => [
        'name' => env_value(['APP_NAME'], 'REIYA POS'),
        'store_name' => env_value(['STORE_NAME'], 'REIYA Beauty'),
        'store_address' => env_value(['STORE_ADDRESS'], 'Beauty & Skincare Store'),
        'store_phone' => env_value(['STORE_PHONE'], '081234567890'),
        'base_url' => env_value(['APP_BASE_URL'], ''),
        'timezone' => env_value(['APP_TIMEZONE'], 'Asia/Jakarta'),
    ],
];
