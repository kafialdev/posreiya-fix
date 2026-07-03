<?php
// Final Vercel config.
// Tidak perlu isi password di file ini. Semua data database dimasukkan di Vercel > Settings > Environment Variables.

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

function env_bool(array $keys, bool $default = false): bool
{
    $value = env_value($keys, $default ? 'true' : 'false');
    return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
}

function database_url_parts(): array
{
    $url = env_value(['DATABASE_URL', 'MYSQL_URL', 'JAWSDB_URL', 'TIDB_URL']);
    if ($url === '') {
        return [];
    }

    $parts = parse_url($url);
    if (!is_array($parts)) {
        return [];
    }

    return [
        'host' => $parts['host'] ?? '',
        'port' => isset($parts['port']) ? (string) $parts['port'] : '',
        'name' => isset($parts['path']) ? ltrim((string) $parts['path'], '/') : '',
        'user' => isset($parts['user']) ? rawurldecode((string) $parts['user']) : '',
        'pass' => isset($parts['pass']) ? rawurldecode((string) $parts['pass']) : '',
    ];
}

$urlDb = database_url_parts();

return [
    'db' => [
        'host' => env_value(['DB_HOST', 'MYSQL_HOST', 'TIDB_HOST', 'PLANETSCALE_DB_HOST'], $urlDb['host'] ?? ''),
        'port' => env_value(['DB_PORT', 'MYSQL_PORT', 'TIDB_PORT', 'PLANETSCALE_DB_PORT'], $urlDb['port'] ?? '3306'),
        'name' => env_value(['DB_NAME', 'MYSQL_DATABASE', 'TIDB_DATABASE', 'TIDB_DB_NAME', 'PLANETSCALE_DB'], $urlDb['name'] ?? ''),
        'user' => env_value(['DB_USER', 'MYSQL_USER', 'TIDB_USER', 'TIDB_USERNAME', 'PLANETSCALE_DB_USERNAME'], $urlDb['user'] ?? ''),
        'pass' => env_value(['DB_PASS', 'DB_PASSWORD', 'MYSQL_PASSWORD', 'TIDB_PASSWORD', 'PLANETSCALE_DB_PASSWORD'], $urlDb['pass'] ?? ''),
        'charset' => env_value(['DB_CHARSET'], 'utf8mb4'),
        'ssl' => env_bool(['DB_SSL', 'MYSQL_SSL', 'TIDB_SSL'], false),
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
