<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$token = preg_replace('/[^a-f0-9]/i', '', (string) ($_GET['token'] ?? ''));
$trx = $token ? fetch_transaction_by_token($token) : null;
if (!$trx) {
    http_response_code(404);
    exit('Nota tidak ditemukan.');
}
$app = $config['app'];

function pdf_safe(string $text): string
{
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted !== false) $text = $converted;
    } else {
        $text = preg_replace('/[^\x20-\x7E]/', '?', $text) ?? $text;
    }
    return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $text);
}

function text_cmd(float $x, float $y, string $text, int $size = 9, bool $bold = false): string
{
    $font = $bold ? 'F2' : 'F1';
    return sprintf("BT /%s %d Tf 1 0 0 1 %.2F %.2F Tm (%s) Tj ET\n", $font, $size, $x, $y, pdf_safe($text));
}

function right_x(string $text, int $size, float $right = 211): float
{
    $approx = strlen($text) * $size * 0.49;
    return max(16, $right - $approx);
}

$height = max(420, 350 + count($trx['items']) * 27);
$y = $height - 28;
$content = '';
$title = (string) ($app['store_name'] ?? 'Toko');
$content .= text_cmd(max(18, (226 - strlen($title) * 6.2) / 2), $y, $title, 14, true); $y -= 15;
$address = (string) ($app['store_address'] ?? '');
$content .= text_cmd(max(16, (226 - strlen($address) * 4.1) / 2), $y, $address, 7); $y -= 11;
$phone = (string) ($app['store_phone'] ?? '');
$content .= text_cmd(max(16, (226 - strlen($phone) * 4.1) / 2), $y, $phone, 7); $y -= 12;
$content .= "0.5 w 16 $y m 211 $y l S\n"; $y -= 15;
$content .= text_cmd(16, $y, 'NOTA ' . $trx['transaction_code'], 8, true); $y -= 12;
$content .= text_cmd(16, $y, 'Tanggal: ' . date('d/m/Y H:i', strtotime($trx['created_at'])), 8); $y -= 12;
$content .= text_cmd(16, $y, 'Pelanggan: ' . ($trx['customer_name'] ?: 'Pelanggan umum'), 8); $y -= 12;
$content .= text_cmd(16, $y, 'Pembayaran: ' . $trx['payment_method'], 8); $y -= 12;
$content .= "0.5 w 16 $y m 211 $y l S\n"; $y -= 15;
foreach ($trx['items'] as $item) {
    $productName = (string) $item['product_name'];
    $nameLength = function_exists('mb_strlen') ? mb_strlen($productName, 'UTF-8') : strlen($productName);
    $name = $nameLength > 31
        ? (function_exists('mb_substr') ? mb_substr($productName, 0, 28, 'UTF-8') : substr($productName, 0, 28)) . '...'
        : $productName;
    $content .= text_cmd(16, $y, $name, 8, true); $y -= 11;
    $left = (int)$item['quantity'] . ' x ' . rupiah($item['price']);
    $right = rupiah($item['line_total']);
    $content .= text_cmd(18, $y, $left, 8);
    $content .= text_cmd(right_x($right, 8), $y, $right, 8); $y -= 15;
}
$content .= "0.5 w 16 $y m 211 $y l S\n"; $y -= 15;
$rows = [
    ['Subtotal', rupiah($trx['subtotal']), false],
    ['Diskon', '-' . rupiah($trx['discount']), false],
    ['TOTAL', rupiah($trx['total']), true],
    ['Dibayar', rupiah($trx['paid']), false],
    ['Kembalian', rupiah($trx['change_amount']), false],
];
foreach ($rows as [$label, $value, $bold]) {
    $content .= text_cmd(16, $y, $label, $bold ? 10 : 8, $bold);
    $content .= text_cmd(right_x($value, $bold ? 10 : 8), $y, $value, $bold ? 10 : 8, $bold); $y -= $bold ? 16 : 13;
}
$content .= "0.5 w 16 $y m 211 $y l S\n"; $y -= 18;
$thanks = 'Terima kasih sudah berbelanja!';
$content .= text_cmd(max(16, (226 - strlen($thanks) * 4.4) / 2), $y, $thanks, 8, true);

$objects = [];
$objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
$objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
$objects[] = sprintf('<< /Type /Page /Parent 2 0 R /MediaBox [0 0 226.77 %d] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>', $height);
$objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
$objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
$objects[] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";

$pdf = "%PDF-1.4\n";
$offsets = [0];
foreach ($objects as $i => $object) {
    $offsets[] = strlen($pdf);
    $pdf .= ($i + 1) . " 0 obj\n" . $object . "\nendobj\n";
}
$xref = strlen($pdf);
$pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
$pdf .= "0000000000 65535 f \n";
for ($i = 1; $i <= count($objects); $i++) {
    $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
}
$pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="nota-' . preg_replace('/[^A-Za-z0-9_-]/', '-', $trx['transaction_code']) . '.pdf"');
header('Content-Length: ' . strlen($pdf));
echo $pdf;
