<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($action === 'products' && $method === 'GET') {
        $stmt = db()->query('SELECT id, sku, name, category, brand, variant, price, stock, image FROM products WHERE active = 1 ORDER BY category, brand, name, variant');
        $products = array_map(static function (array $product): array {
            $product['image'] = normalize_image_path($product['image'] ?? '');
            return $product;
        }, $stmt->fetchAll());
        json_response(['success' => true, 'products' => $products]);
    }

    if ($action === 'dashboard' && $method === 'GET') {
        $pdo = db();
        $summary = $pdo->query(
            "SELECT
                COALESCE(SUM(total), 0) AS revenue_today,
                COUNT(*) AS transactions_today,
                COALESCE(AVG(total), 0) AS average_order
             FROM transactions
             WHERE DATE(created_at) = CURDATE()"
        )->fetch();
        $summary['active_products'] = (int) $pdo->query('SELECT COUNT(*) FROM products WHERE active = 1')->fetchColumn();

        $recent = $pdo->query(
            'SELECT transaction_code, customer_name, payment_method, total, created_at, receipt_token
             FROM transactions ORDER BY id DESC LIMIT 8'
        )->fetchAll();

        $chart = $pdo->query(
            "SELECT DATE(created_at) AS sale_date, COALESCE(SUM(total),0) AS revenue
             FROM transactions
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(created_at)
             ORDER BY sale_date"
        )->fetchAll();

        json_response(['success' => true, 'summary' => $summary, 'recent' => $recent, 'chart' => $chart]);
    }

    if ($action === 'transactions' && $method === 'GET') {
        $stmt = db()->query(
            'SELECT transaction_code, customer_name, customer_phone, payment_method, total, paid, change_amount, created_at, receipt_token
             FROM transactions ORDER BY id DESC LIMIT 100'
        );
        json_response(['success' => true, 'transactions' => $stmt->fetchAll()]);
    }

    if ($action === 'save_product' && $method === 'POST') {
        $pdo = db();
        $id = (int) ($_POST['id'] ?? 0);
        $sku = strtoupper(clean_text((string) ($_POST['sku'] ?? ''), 50));
        $name = clean_text((string) ($_POST['name'] ?? ''), 150);
        $category = clean_text((string) ($_POST['category'] ?? 'Umum'), 100);
        $brand = clean_text((string) ($_POST['brand'] ?? ''), 100);
        $variant = clean_text((string) ($_POST['variant'] ?? ''), 120);
        $price = max(0, (float) ($_POST['price'] ?? 0));
        $stock = max(0, (int) ($_POST['stock'] ?? 0));
        $existingImage = normalize_image_path(clean_text((string) ($_POST['existing_image'] ?? ''), 255));

        if ($sku === '' || $name === '' || $category === '' || $brand === '' || $variant === '' || $price <= 0) {
            json_response(['success' => false, 'message' => 'SKU, nama, jenis, merek, varian, dan harga wajib diisi.'], 422);
        }

        $imagePath = $existingImage;
        if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                json_response(['success' => false, 'message' => 'Gagal mengunggah gambar.'], 422);
            }
            if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
                json_response(['success' => false, 'message' => 'Ukuran gambar maksimal 3 MB.'], 422);
            }
            $allowed = ['jpg' => 'jpg', 'jpeg' => 'jpg', 'png' => 'png', 'webp' => 'webp'];
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            $mime = function_exists('mime_content_type') ? (string) @mime_content_type($file['tmp_name']) : '';
            if (!isset($allowed[$extension]) || @getimagesize($file['tmp_name']) === false || ($mime !== '' && !in_array($mime, $allowedMime, true))) {
                json_response(['success' => false, 'message' => 'Gambar harus JPG, PNG, atau WEBP.'], 422);
            }
            ensure_writable_directory(__DIR__ . '/uploads');
            $filename = 'product-' . bin2hex(random_bytes(8)) . '.' . $allowed[$extension];
            $destination = __DIR__ . '/uploads/' . $filename;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                json_response(['success' => false, 'message' => 'Folder uploads tidak dapat ditulis.'], 500);
            }
            $imagePath = 'uploads/' . $filename;
        }

        if ($id > 0) {
            $exists = $pdo->prepare('SELECT COUNT(*) FROM products WHERE id = ?');
            $exists->execute([$id]);
            if ((int) $exists->fetchColumn() === 0) {
                json_response(['success' => false, 'message' => 'Produk yang akan diedit tidak ditemukan.'], 404);
            }
            $stmt = $pdo->prepare('UPDATE products SET sku=?, name=?, category=?, brand=?, variant=?, price=?, stock=?, image=? WHERE id=?');
            $stmt->execute([$sku, $name, $category, $brand, $variant, $price, $stock, $imagePath, $id]);
            $message = 'Produk berhasil diperbarui.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (sku, name, category, brand, variant, price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$sku, $name, $category, $brand, $variant, $price, $stock, $imagePath]);
            $message = 'Produk berhasil ditambahkan.';
        }

        json_response(['success' => true, 'message' => $message]);
    }

    if ($action === 'delete_product' && $method === 'POST') {
        $data = input_json();
        $id = (int) ($data['id'] ?? 0);
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Produk tidak valid.'], 422);
        }
        $stmt = db()->prepare('UPDATE products SET active = 0 WHERE id = ? AND active = 1');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            json_response(['success' => false, 'message' => 'Produk tidak ditemukan atau sudah dihapus.'], 404);
        }
        json_response(['success' => true, 'message' => 'Produk dihapus dari katalog.']);
    }

    if ($action === 'save_transaction' && $method === 'POST') {
        $data = input_json();
        $items = $data['items'] ?? [];
        if (!is_array($items) || count($items) === 0) {
            json_response(['success' => false, 'message' => 'Keranjang masih kosong.'], 422);
        }

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $subtotal = 0.0;
            $validated = [];
            $productStmt = $pdo->prepare('SELECT id, name, brand, variant, price, stock FROM products WHERE id = ? AND active = 1 FOR UPDATE');

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                if ($productId <= 0) {
                    throw new RuntimeException('Data produk pada keranjang tidak valid.');
                }
                $productStmt->execute([$productId]);
                $product = $productStmt->fetch();
                if (!$product) {
                    throw new RuntimeException('Salah satu produk sudah tidak tersedia.');
                }
                if ((int) $product['stock'] < $quantity) {
                    throw new RuntimeException('Stok ' . $product['name'] . ' tidak mencukupi.');
                }
                $price = (float) $product['price'];
                $lineTotal = $price * $quantity;
                $subtotal += $lineTotal;
                $validated[] = [
                    'id' => (int) $product['id'],
                    'name' => trim(($product['brand'] ?? '') . ' ' . $product['name'] . (($product['variant'] ?? '') !== '' ? ' - ' . $product['variant'] : '')),
                    'price' => $price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = min($subtotal, max(0, (float) ($data['discount'] ?? 0)));
            $total = max(0, $subtotal - $discount);
            $methodName = (string) ($data['payment_method'] ?? 'Tunai');
            $allowedMethods = ['Tunai', 'QRIS', 'Transfer', 'Kartu'];
            if (!in_array($methodName, $allowedMethods, true)) {
                $methodName = 'Tunai';
            }
            $paid = max(0, (float) ($data['paid'] ?? $total));
            if ($methodName !== 'Tunai') {
                $paid = $total;
            }
            if ($paid < $total) {
                throw new RuntimeException('Nominal pembayaran kurang dari total transaksi.');
            }
            $change = max(0, $paid - $total);
            $code = generate_unique_transaction_code($pdo);
            $token = bin2hex(random_bytes(16));
            $customerName = clean_text((string) ($data['customer_name'] ?? ''), 150);
            $customerPhone = normalize_phone((string) ($data['customer_phone'] ?? ''));
            $notes = clean_text((string) ($data['notes'] ?? ''), 255);

            $transactionStmt = $pdo->prepare(
                'INSERT INTO transactions
                (transaction_code, receipt_token, customer_name, customer_phone, payment_method, subtotal, discount, total, paid, change_amount, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $transactionStmt->execute([
                $code, $token, $customerName ?: null, $customerPhone ?: null, $methodName,
                $subtotal, $discount, $total, $paid, $change, $notes ?: null,
            ]);
            $transactionId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO transaction_items (transaction_id, product_id, product_name, price, quantity, line_total)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stockStmt = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
            foreach ($validated as $item) {
                $itemStmt->execute([
                    $transactionId, $item['id'], $item['name'], $item['price'], $item['quantity'], $item['line_total'],
                ]);
                $stockStmt->execute([$item['quantity'], $item['id']]);
            }

            $pdo->commit();

            $receiptUrl = app_base_url() . '/receipt.php?token=' . urlencode($token);
            $lines = [
                '*' . ($config['app']['store_name'] ?? 'Toko') . '*',
                'Nota: ' . $code,
                'Tanggal: ' . date('d/m/Y H:i'),
                '',
            ];
            foreach ($validated as $item) {
                $lines[] = $item['quantity'] . 'x ' . $item['name'] . ' - ' . rupiah($item['line_total']);
            }
            $lines[] = '';
            $lines[] = '*Total: ' . rupiah($total) . '*';
            $lines[] = 'Pembayaran: ' . $methodName;
            $lines[] = 'Lihat nota digital: ' . $receiptUrl;
            $lines[] = '';
            $lines[] = 'Terima kasih sudah berbelanja.';

            json_response([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'transaction_code' => $code,
                'token' => $token,
                'receipt_url' => $receiptUrl,
                'print_url' => app_base_url() . '/print_receipt.php?token=' . urlencode($token),
                'download_url' => app_base_url() . '/download_receipt.php?token=' . urlencode($token),
                'whatsapp_text' => implode("\n", $lines),
                'phone' => $customerPhone,
                'total' => $total,
                'change' => $change,
            ]);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_response(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    json_response(['success' => false, 'message' => 'Endpoint tidak ditemukan.'], 404);
} catch (PDOException $e) {
    $message = str_contains(strtolower($e->getMessage()), 'duplicate')
        ? 'SKU sudah digunakan. Gunakan SKU lain.'
        : 'Database belum siap atau konfigurasi salah.';
    json_response(['success' => false, 'message' => $message], 500);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
}
