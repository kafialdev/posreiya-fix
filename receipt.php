<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$token = preg_replace('/[^a-f0-9]/i', '', (string) ($_GET['token'] ?? ''));
$transaction = $token ? fetch_transaction_by_token($token) : null;
if (!$transaction) {
    http_response_code(404);
}
$app = $config['app'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $transaction ? 'Nota ' . htmlspecialchars($transaction['transaction_code']) : 'Nota tidak ditemukan' ?></title>
    <style>
        :root{--ink:#17231e;--green:#1e6a4d;--muted:#717b76;--line:#e5e9e7}*{box-sizing:border-box}body{margin:0;background:#eef2f0;font-family:Inter,Arial,sans-serif;color:var(--ink);padding:28px 15px}.wrap{max-width:520px;margin:auto}.receipt{background:#fff;border-radius:24px;padding:28px;box-shadow:0 20px 60px rgba(21,54,42,.12)}.brand{text-align:center;border-bottom:1px dashed var(--line);padding-bottom:18px}.logo{width:52px;height:52px;border-radius:17px;background:#15362a;color:#fff;display:grid;place-items:center;margin:auto;font-size:23px;font-weight:900}.brand h1{font-size:21px;margin:11px 0 4px}.brand p,.muted{margin:0;color:var(--muted);font-size:12px;line-height:1.5}.meta{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:18px 0;border-bottom:1px dashed var(--line)}.meta span,.meta strong{display:block}.meta span{font-size:10px;color:var(--muted);margin-bottom:4px}.meta strong{font-size:12px}.right{text-align:right}.items{padding:9px 0}.item{display:grid;grid-template-columns:1fr auto;gap:10px;padding:10px 0}.item strong,.item small{display:block}.item strong{font-size:12px}.item small{font-size:10px;color:var(--muted);margin-top:4px}.item>span{font-size:12px;font-weight:700}.totals{border-top:1px dashed var(--line);padding-top:12px;display:grid;gap:9px}.row{display:flex;justify-content:space-between;font-size:12px;color:var(--muted)}.row b{color:var(--ink)}.row.total{font-size:18px;color:var(--ink);font-weight:900;padding-top:9px;border-top:1px solid var(--line)}.row.total b{color:var(--green)}.thanks{text-align:center;padding:20px 0 2px}.thanks strong{display:block;font-size:13px}.thanks span{font-size:11px;color:var(--muted)}.actions{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px}.actions a,.actions button{border:0;border-radius:13px;padding:12px;text-decoration:none;text-align:center;font-weight:800;font-size:12px;cursor:pointer}.primary{background:#1e6a4d;color:#fff}.secondary{background:#fff;color:var(--ink);border:1px solid var(--line)!important}.notfound{text-align:center;padding:50px 20px}@media(max-width:480px){body{padding:0}.receipt{border-radius:0;min-height:100vh;padding:23px}.actions{padding:0 15px 20px}}
    </style>
</head>
<body>
<div class="wrap">
<?php if (!$transaction): ?>
    <div class="receipt notfound"><h1>Nota tidak ditemukan</h1><p class="muted">Tautan nota tidak valid atau transaksi sudah tidak tersedia.</p></div>
<?php else: ?>
    <article class="receipt">
        <header class="brand"><div class="logo">R</div><h1><?= htmlspecialchars($app['store_name']) ?></h1><p><?= htmlspecialchars($app['store_address']) ?><br><?= htmlspecialchars($app['store_phone']) ?></p></header>
        <div class="meta"><div><span>NOMOR NOTA</span><strong><?= htmlspecialchars($transaction['transaction_code']) ?></strong></div><div class="right"><span>TANGGAL</span><strong><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></strong></div><div><span>PELANGGAN</span><strong><?= htmlspecialchars($transaction['customer_name'] ?: 'Pelanggan umum') ?></strong></div><div class="right"><span>PEMBAYARAN</span><strong><?= htmlspecialchars($transaction['payment_method']) ?></strong></div></div>
        <div class="items">
            <?php foreach ($transaction['items'] as $item): ?>
                <div class="item"><div><strong><?= htmlspecialchars($item['product_name']) ?></strong><small><?= (int) $item['quantity'] ?> × <?= rupiah($item['price']) ?></small></div><span><?= rupiah($item['line_total']) ?></span></div>
            <?php endforeach; ?>
        </div>
        <div class="totals"><div class="row"><span>Subtotal</span><b><?= rupiah($transaction['subtotal']) ?></b></div><div class="row"><span>Diskon</span><b>-<?= rupiah($transaction['discount']) ?></b></div><div class="row total"><span>Total</span><b><?= rupiah($transaction['total']) ?></b></div><div class="row"><span>Dibayar</span><b><?= rupiah($transaction['paid']) ?></b></div><div class="row"><span>Kembalian</span><b><?= rupiah($transaction['change_amount']) ?></b></div></div>
        <?php if ($transaction['notes']): ?><p class="muted" style="margin-top:16px"><strong>Catatan:</strong> <?= htmlspecialchars($transaction['notes']) ?></p><?php endif; ?>
        <footer class="thanks"><strong>Terima kasih sudah berbelanja!</strong><span>Simpan nota digital ini sebagai bukti transaksi.</span></footer>
    </article>
    <div class="actions"><a class="primary" href="download_receipt.php?token=<?= urlencode($token) ?>">Download PDF</a><button class="secondary" onclick="window.open('print_receipt.php?token=<?= urlencode($token) ?>','_blank')">Cetak Nota</button></div>
<?php endif; ?>
</div>
</body>
</html>
