<?php
require __DIR__ . '/bootstrap.php';

try {
    $pdo = db();
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    json_response([
        'success' => true,
        'message' => 'Database terkoneksi.',
        'database' => $config['db']['name'] ?? '',
        'tables' => $tables,
    ]);
} catch (Throwable $e) {
    json_response([
        'success' => false,
        'message' => $e->getMessage(),
        'hint' => 'Cek Environment Variables: DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS. Untuk TiDB Cloud, set DB_SSL=true.',
    ], 500);
}
