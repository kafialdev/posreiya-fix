<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$messages = [];
$error = null;

function column_exists(PDO $pdo, string $table, string $column): bool
{
    global $config;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$config['db']['name'], $table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function index_exists(PDO $pdo, string $table, string $index): bool
{
    global $config;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->execute([$config['db']['name'], $table, $index]);
    return (int) $stmt->fetchColumn() > 0;
}

function add_column_if_missing(PDO $pdo, string $table, string $column, string $definition): bool
{
    if (column_exists($pdo, $table, $column)) {
        return false;
    }

    $pdo->exec("ALTER TABLE `$table` ADD COLUMN $definition");
    return true;
}

function add_index_if_missing(PDO $pdo, string $table, string $index, string $statement): bool
{
    if (index_exists($pdo, $table, $index)) {
        return false;
    }

    $pdo->exec($statement);
    return true;
}

function fill_missing_unique_values(PDO $pdo, string $table, string $column, string $prefix): void
{
    $stmt = $pdo->query("SELECT id FROM `$table` WHERE `$column` IS NULL OR `$column` = ''");
    $update = $pdo->prepare("UPDATE `$table` SET `$column` = ? WHERE id = ?");

    foreach ($stmt->fetchAll() as $row) {
        $update->execute([$prefix . '-' . $row['id'] . '-' . bin2hex(random_bytes(3)), $row['id']]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = db();
        $pdo->exec("CREATE TABLE IF NOT EXISTS products (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sku VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            category VARCHAR(100) NOT NULL DEFAULT 'Umum',
            brand VARCHAR(100) NOT NULL DEFAULT 'Tanpa Merek',
            variant VARCHAR(120) NOT NULL DEFAULT '-',
            price DECIMAL(14,2) NOT NULL DEFAULT 0,
            stock INT NOT NULL DEFAULT 0,
            image VARCHAR(255) DEFAULT NULL,
            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_products_active (active),
            INDEX idx_products_category (category),
            INDEX idx_products_brand (brand),
            INDEX idx_products_catalog (category, brand)
        ) ENGINE=InnoDB");

        if (add_column_if_missing($pdo, 'products', 'category', "category VARCHAR(100) NOT NULL DEFAULT 'Umum' AFTER name")) {
            $messages[] = 'Kolom jenis produk berhasil ditambahkan.';
        }
        if (add_column_if_missing($pdo, 'products', 'brand', "brand VARCHAR(100) NOT NULL DEFAULT 'Tanpa Merek' AFTER category")) {
            $messages[] = 'Kolom merek berhasil ditambahkan.';
        }
        if (add_column_if_missing($pdo, 'products', 'variant', "variant VARCHAR(120) NOT NULL DEFAULT '-' AFTER brand")) {
            $messages[] = 'Kolom shade/varian berhasil ditambahkan.';
        }
        add_column_if_missing($pdo, 'products', 'image', "image VARCHAR(255) DEFAULT NULL AFTER stock");
        add_column_if_missing($pdo, 'products', 'active', "active TINYINT(1) NOT NULL DEFAULT 1 AFTER image");
        add_column_if_missing($pdo, 'products', 'created_at', "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER active");
        add_column_if_missing($pdo, 'products', 'updated_at', "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
        add_index_if_missing($pdo, 'products', 'idx_products_active', 'ALTER TABLE products ADD INDEX idx_products_active (active)');
        add_index_if_missing($pdo, 'products', 'idx_products_category', 'ALTER TABLE products ADD INDEX idx_products_category (category)');
        add_index_if_missing($pdo, 'products', 'idx_products_brand', 'ALTER TABLE products ADD INDEX idx_products_brand (brand)');
        add_index_if_missing($pdo, 'products', 'idx_products_catalog', 'ALTER TABLE products ADD INDEX idx_products_catalog (category, brand)');

        $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            transaction_code VARCHAR(60) NOT NULL UNIQUE,
            receipt_token VARCHAR(64) NOT NULL UNIQUE,
            customer_name VARCHAR(150) DEFAULT NULL,
            customer_phone VARCHAR(30) DEFAULT NULL,
            payment_method ENUM('Tunai','QRIS','Transfer','Kartu') NOT NULL DEFAULT 'Tunai',
            subtotal DECIMAL(14,2) NOT NULL DEFAULT 0,
            discount DECIMAL(14,2) NOT NULL DEFAULT 0,
            total DECIMAL(14,2) NOT NULL DEFAULT 0,
            paid DECIMAL(14,2) NOT NULL DEFAULT 0,
            change_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
            notes VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_transactions_created_at (created_at),
            INDEX idx_transactions_phone (customer_phone)
        ) ENGINE=InnoDB");
        add_column_if_missing($pdo, 'transactions', 'transaction_code', "transaction_code VARCHAR(60) DEFAULT NULL AFTER id");
        add_column_if_missing($pdo, 'transactions', 'receipt_token', "receipt_token VARCHAR(64) DEFAULT NULL AFTER transaction_code");
        add_column_if_missing($pdo, 'transactions', 'customer_name', "customer_name VARCHAR(150) DEFAULT NULL AFTER receipt_token");
        add_column_if_missing($pdo, 'transactions', 'customer_phone', "customer_phone VARCHAR(30) DEFAULT NULL AFTER customer_name");
        add_column_if_missing($pdo, 'transactions', 'payment_method', "payment_method ENUM('Tunai','QRIS','Transfer','Kartu') NOT NULL DEFAULT 'Tunai' AFTER customer_phone");
        add_column_if_missing($pdo, 'transactions', 'subtotal', "subtotal DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER payment_method");
        add_column_if_missing($pdo, 'transactions', 'discount', "discount DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER subtotal");
        add_column_if_missing($pdo, 'transactions', 'total', "total DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER discount");
        add_column_if_missing($pdo, 'transactions', 'paid', "paid DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER total");
        add_column_if_missing($pdo, 'transactions', 'change_amount', "change_amount DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER paid");
        add_column_if_missing($pdo, 'transactions', 'notes', "notes VARCHAR(255) DEFAULT NULL AFTER change_amount");
        add_column_if_missing($pdo, 'transactions', 'created_at', "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER notes");
        fill_missing_unique_values($pdo, 'transactions', 'transaction_code', 'TRX-MIG');
        fill_missing_unique_values($pdo, 'transactions', 'receipt_token', 'TOKEN');
        $pdo->exec('ALTER TABLE transactions MODIFY transaction_code VARCHAR(60) NOT NULL');
        $pdo->exec('ALTER TABLE transactions MODIFY receipt_token VARCHAR(64) NOT NULL');
        if (!index_exists($pdo, 'transactions', 'transaction_code')) {
            add_index_if_missing($pdo, 'transactions', 'uniq_transactions_code', 'ALTER TABLE transactions ADD UNIQUE INDEX uniq_transactions_code (transaction_code)');
        }
        if (!index_exists($pdo, 'transactions', 'receipt_token')) {
            add_index_if_missing($pdo, 'transactions', 'uniq_transactions_receipt_token', 'ALTER TABLE transactions ADD UNIQUE INDEX uniq_transactions_receipt_token (receipt_token)');
        }
        add_index_if_missing($pdo, 'transactions', 'idx_transactions_created_at', 'ALTER TABLE transactions ADD INDEX idx_transactions_created_at (created_at)');
        add_index_if_missing($pdo, 'transactions', 'idx_transactions_phone', 'ALTER TABLE transactions ADD INDEX idx_transactions_phone (customer_phone)');

        $pdo->exec("CREATE TABLE IF NOT EXISTS transaction_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            transaction_id BIGINT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            price DECIMAL(14,2) NOT NULL,
            quantity INT NOT NULL,
            line_total DECIMAL(14,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_items_transaction (transaction_id),
            INDEX idx_items_product (product_id)
        ) ENGINE=InnoDB");
        add_column_if_missing($pdo, 'transaction_items', 'product_name', "product_name VARCHAR(255) NOT NULL DEFAULT '-' AFTER product_id");
        add_column_if_missing($pdo, 'transaction_items', 'price', "price DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER product_name");
        add_column_if_missing($pdo, 'transaction_items', 'quantity', "quantity INT NOT NULL DEFAULT 1 AFTER price");
        add_column_if_missing($pdo, 'transaction_items', 'line_total', "line_total DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER quantity");
        add_column_if_missing($pdo, 'transaction_items', 'created_at', "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER line_total");
        $pdo->exec('ALTER TABLE transaction_items MODIFY product_name VARCHAR(255) NOT NULL');
        add_index_if_missing($pdo, 'transaction_items', 'idx_items_transaction', 'ALTER TABLE transaction_items ADD INDEX idx_items_transaction (transaction_id)');
        add_index_if_missing($pdo, 'transaction_items', 'idx_items_product', 'ALTER TABLE transaction_items ADD INDEX idx_items_product (product_id)');

        // Menonaktifkan hanya produk demo bakery bawaan versi lama, tanpa menyentuh produk pengguna lainnya.
        $oldDemoSkus = ['BRD-001','BRD-002','BRD-003','DRK-001','BRD-004','CKE-001'];
        $placeholders = implode(',', array_fill(0, count($oldDemoSkus), '?'));
        $disableOld = $pdo->prepare("UPDATE products SET active = 0 WHERE sku IN ($placeholders)");
        $disableOld->execute($oldDemoSkus);

        $seed = $pdo->prepare(
            'INSERT INTO products (sku, name, category, brand, variant, price, stock, image, active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
             ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), brand=VALUES(brand), variant=VALUES(variant), price=VALUES(price), image=VALUES(image), active=1'
        );
        $products = [
            ['WRD-BDK-01','Colorfit Velvet Powder Foundation','Bedak','Wardah','01 Light Beige',89000,24,'assets/img/skincare/wardah-bedak-light-beige.svg'],
            ['WRD-BDK-02','Colorfit Velvet Powder Foundation','Bedak','Wardah','02 Natural',89000,18,'assets/img/skincare/wardah-bedak-natural.svg'],
            ['MKO-BDK-W22','Powerstay Matte Powder Foundation','Bedak','Make Over','W22 Warm Ivory',159000,14,'assets/img/skincare/makeover-bedak-w22.svg'],
            ['MKO-BDK-N30','Powerstay Matte Powder Foundation','Bedak','Make Over','N30 Natural Beige',159000,12,'assets/img/skincare/makeover-bedak-n30.svg'],
            ['EMN-BDK-01','Bare With Me Mineral Compact Powder','Bedak','Emina','01 Fair',54000,27,'assets/img/skincare/emina-bedak-fair.svg'],
            ['WRD-LIP-01','Colorfit Velvet Matte Lip Mousse','Lipstik','Wardah','01 Brown Dreamer',79000,32,'assets/img/skincare/wardah-lip-brown.svg'],
            ['WRD-LIP-05','Colorfit Velvet Matte Lip Mousse','Lipstik','Wardah','05 Artisan Mauve',79000,21,'assets/img/skincare/wardah-lip-mauve.svg'],
            ['HNS-LIP-02','Mattedorable Lip Cream','Lipstik','Hanasui','02 Chic',35000,36,'assets/img/skincare/hanasui-lip-chic.svg'],
            ['HNS-LIP-04','Mattedorable Lip Cream','Lipstik','Hanasui','04 Pink Latte',35000,29,'assets/img/skincare/hanasui-lip-pink.svg'],
            ['SKN-SRM-5X','5X Ceramide Barrier Repair Serum','Serum','Skintific','20 ml',129000,20,'assets/img/skincare/skintific-serum-ceramide.svg'],
            ['SKN-SRM-NIA','10% Niacinamide Brightening Serum','Serum','Skintific','20 ml',139000,16,'assets/img/skincare/skintific-serum-niacinamide.svg'],
            ['AZR-SUN-HYD','Hydrasoothe Sunscreen Gel SPF45','Sunscreen','Azarine','50 ml',69000,31,'assets/img/skincare/azarine-sunscreen-hydra.svg'],
            ['AZR-SUN-TUP','Tone Up Mineral Sunscreen Serum SPF50','Sunscreen','Azarine','40 ml',75000,25,'assets/img/skincare/azarine-sunscreen-toneup.svg'],
            ['EMN-FW-BRT','Bright Stuff Face Wash','Facial Wash','Emina','50 ml',32000,40,'assets/img/skincare/emina-wash-bright.svg'],
            ['EMN-FW-ACN','Ms. Pimple Acne Solution Face Wash','Facial Wash','Emina','50 ml',38000,22,'assets/img/skincare/emina-wash-pimple.svg'],
            ['MKO-FND-W22','Powerstay Weightless Liquid Foundation','Foundation','Make Over','W22 Warm Ivory',189000,13,'assets/img/skincare/makeover-foundation-w22.svg'],
            ['MKO-FND-N30','Powerstay Weightless Liquid Foundation','Foundation','Make Over','N30 Natural Beige',189000,10,'assets/img/skincare/makeover-foundation-n30.svg'],
            ['WRD-FND-21N','Colorfit Matte Foundation','Foundation','Wardah','21N Shell Ivory',95000,19,'assets/img/skincare/wardah-foundation-shell.svg'],
        ];
        foreach ($products as $product) {
            $seed->execute($product);
        }

        $messages[] = 'Tabel database dan struktur katalog bertingkat berhasil disiapkan.';
        $messages[] = '18 produk contoh skincare/beauty berhasil ditambahkan atau diperbarui.';
        $messages[] = 'Produk demo bakery lama dinonaktifkan. Data transaksi lama tetap aman.';
        $messages[] = 'Di Vercel, setup.php boleh dipakai sekali untuk migrasi lalu sebaiknya route setup dihapus dari vercel.json.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalasi REIYA Beauty POS</title>
    <style>
        body{font-family:Inter,Arial,sans-serif;background:linear-gradient(135deg,#f6f0f2,#eee2e7);color:#2d2228;margin:0;display:grid;place-items:center;min-height:100vh}.card{width:min(670px,90vw);background:white;border-radius:26px;padding:36px;box-shadow:0 18px 60px rgba(69,44,58,.15)}h1{margin-top:0;font-family:Georgia,serif}.ok{padding:12px 14px;background:#f2e8ec;border-radius:12px;margin:8px 0}.err{padding:12px 14px;background:#fff0f1;color:#a52a3e;border-radius:12px;margin:12px 0}button,a{display:inline-block;border:0;border-radius:12px;background:#4d3040;color:white;padding:12px 18px;text-decoration:none;font-weight:700;cursor:pointer}code{background:#f4edef;padding:2px 6px;border-radius:6px}
    </style>
</head>
<body>
<div class="card">
    <h1>Instalasi REIYA Beauty POS</h1>
    <p>Pastikan database <code><?= htmlspecialchars($config['db']['name']) ?></code> sudah dibuat dan konfigurasi pada <code>config.php</code> sudah benar.</p>
    <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php foreach ($messages as $message): ?><div class="ok"><?= htmlspecialchars($message) ?></div><?php endforeach; ?>
    <?php if (!$messages): ?>
        <form method="post"><button type="submit">Migrasi dan Tambahkan Data Skincare</button></form>
    <?php else: ?>
        <a href="index.php">Buka REIYA Beauty POS</a>
    <?php endif; ?>
</div>
</body>
</html>
