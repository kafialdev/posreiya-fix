<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$token = preg_replace('/[^a-f0-9]/i', '', (string) ($_GET['token'] ?? ''));
$transaction = $token ? fetch_transaction_by_token($token) : null;
if (!$transaction) {
    http_response_code(404);
    exit('Nota tidak ditemukan.');
}
$app = $config['app'];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Cetak <?= htmlspecialchars($transaction['transaction_code']) ?></title>
<style>
@page{size:80mm auto;margin:3mm}*{box-sizing:border-box}body{font-family:"Courier New",monospace;width:74mm;margin:0 auto;color:#000;font-size:11px}.center{text-align:center}.store{font-size:16px;font-weight:700;margin-bottom:4px}.dash{border-top:1px dashed #000;margin:8px 0}.row{display:flex;justify-content:space-between;gap:8px;margin:4px 0}.item{margin:7px 0}.item-name{font-weight:700}.item-detail{display:flex;justify-content:space-between}.total{font-size:14px;font-weight:700}.screen-tools{display:flex;gap:8px;margin:12px 0}.screen-tools button{flex:1;padding:9px;border:0;border-radius:8px;background:#15362a;color:white;font-weight:700}@media print{.screen-tools{display:none}body{width:auto}}
</style>
</head>
<body>
<div class="screen-tools"><button onclick="window.print()">Cetak Sekarang</button><button onclick="window.close()">Tutup</button></div>
<div class="center"><div class="store"><?= htmlspecialchars($app['store_name']) ?></div><div><?= htmlspecialchars($app['store_address']) ?></div><div><?= htmlspecialchars($app['store_phone']) ?></div></div>
<div class="dash"></div><div>No: <?= htmlspecialchars($transaction['transaction_code']) ?></div><div>Tgl: <?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></div><div>Kasir: Admin</div><div>Pelanggan: <?= htmlspecialchars($transaction['customer_name'] ?: 'Umum') ?></div><div class="dash"></div>
<?php foreach ($transaction['items'] as $item): ?><div class="item"><div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div><div class="item-detail"><span><?= (int)$item['quantity'] ?> x <?= rupiah($item['price']) ?></span><span><?= rupiah($item['line_total']) ?></span></div></div><?php endforeach; ?>
<div class="dash"></div><div class="row"><span>Subtotal</span><span><?= rupiah($transaction['subtotal']) ?></span></div><div class="row"><span>Diskon</span><span>-<?= rupiah($transaction['discount']) ?></span></div><div class="row total"><span>TOTAL</span><span><?= rupiah($transaction['total']) ?></span></div><div class="row"><span>Dibayar</span><span><?= rupiah($transaction['paid']) ?></span></div><div class="row"><span>Kembali</span><span><?= rupiah($transaction['change_amount']) ?></span></div><div class="row"><span>Metode</span><span><?= htmlspecialchars($transaction['payment_method']) ?></span></div>
<div class="dash"></div><div class="center">Terima kasih<br>Simpan nota sebagai bukti transaksi</div>
<script>window.addEventListener('load',()=>setTimeout(()=>window.print(),350));</script>
</body></html>
