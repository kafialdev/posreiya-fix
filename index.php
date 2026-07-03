<?php
require __DIR__ . '/bootstrap.php';
$app = $config['app'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15362a">
    <title><?= htmlspecialchars($app['name']) ?> — <?= htmlspecialchars($app['store_name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=3.1">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">R</div>
            <div>
                <strong><?= htmlspecialchars($app['name']) ?></strong>
                <span><?= htmlspecialchars($app['store_name']) ?></span>
            </div>
        </div>

        <nav class="nav-list">
            <button class="nav-item active" data-page="cashier"><span>▦</span>Kasir</button>
            <button class="nav-item" data-page="dashboard"><span>◫</span>Dashboard</button>
            <button class="nav-item" data-page="products"><span>◈</span>Produk</button>
            <button class="nav-item" data-page="transactions"><span>↺</span>Transaksi</button>
        </nav>

        <div class="sidebar-foot">
            <div class="store-avatar">RB</div>
            <div><strong><?= htmlspecialchars($app['store_name']) ?></strong><span>Kasir aktif</span></div>
            <i class="status-dot"></i>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="mobile-menu" id="mobileMenu">☰</button>
            <div>
                <p class="eyebrow" id="pageEyebrow">POINT OF SALE</p>
                <h1 id="pageTitle">Kasir</h1>
            </div>
            <div class="top-actions">
                <div class="date-chip" id="todayDate"></div>
                <button class="icon-button" id="refreshButton" title="Muat ulang">↻</button>
            </div>
        </header>

        <section class="page active" id="page-cashier">
            <div class="cashier-layout">
                <div class="catalog-panel">
                    <div class="toolbar">
                        <label class="search-box">
                            <span>⌕</span>
                            <input id="productSearch" type="search" placeholder="Cari jenis, merek, shade, atau SKU...">
                        </label>
                        <button class="primary small" id="quickAddProduct">＋ Produk</button>
                    </div>

                    <div class="catalog-navigation">
                        <div class="breadcrumb" id="catalogBreadcrumb"></div>
                        <button class="back-button" id="catalogBack" type="button">← Kembali</button>
                    </div>

                    <div class="catalog-intro">
                        <div>
                            <p class="eyebrow" id="catalogEyebrow">KATALOG SKINCARE & BEAUTY</p>
                            <h2 id="catalogTitle">Pilih Jenis Produk</h2>
                            <p id="catalogDescription">Pilih kategori seperti Bedak, Lipstik, Serum, dan lainnya.</p>
                        </div>
                        <span class="catalog-count" id="catalogCount">0 pilihan</span>
                    </div>

                    <div class="product-grid segment-grid" id="productGrid">
                        <div class="empty-state">Memuat produk...</div>
                    </div>
                </div>

                <aside class="cart-panel">
                    <div class="cart-heading">
                        <div><p class="eyebrow">PESANAN</p><h2>Keranjang</h2></div>
                        <button class="text-button danger" id="clearCart">Kosongkan</button>
                    </div>
                    <div class="cart-items" id="cartItems">
                        <div class="cart-empty"><div class="empty-icon">🛒</div><strong>Belum ada produk</strong><span>Pilih jenis, merek, lalu shade produk.</span></div>
                    </div>
                    <div class="cart-summary">
                        <div><span>Subtotal</span><strong id="subtotalText">Rp0</strong></div>
                        <label class="discount-line"><span>Diskon</span><input id="discountInput" type="number" min="0" value="0"></label>
                        <div class="grand-total"><span>Total</span><strong id="totalText">Rp0</strong></div>
                    </div>
                    <button class="checkout-button" id="checkoutButton" disabled>Simpan Transaksi <span>→</span></button>
                </aside>
            </div>
        </section>

        <section class="page" id="page-dashboard">
            <div class="stat-grid">
                <article class="stat-card featured"><span>Pemasukan Hari Ini</span><strong id="revenueToday">Rp0</strong><small>Otomatis dari transaksi tersimpan</small></article>
                <article class="stat-card"><span>Transaksi Hari Ini</span><strong id="transactionsToday">0</strong><small>Jumlah nota berhasil dibuat</small></article>
                <article class="stat-card"><span>Rata-rata Belanja</span><strong id="averageOrder">Rp0</strong><small>Nilai rata-rata transaksi</small></article>
                <article class="stat-card"><span>Varian Aktif</span><strong id="activeProducts">0</strong><small>Shade dan jenis siap dijual</small></article>
            </div>
            <div class="dashboard-grid">
                <article class="panel chart-panel"><div class="panel-title"><div><p class="eyebrow">7 HARI TERAKHIR</p><h2>Tren Pemasukan</h2></div></div><div class="bar-chart" id="revenueChart"></div></article>
                <article class="panel"><div class="panel-title"><div><p class="eyebrow">TERBARU</p><h2>Transaksi Terkini</h2></div></div><div id="recentTransactions" class="recent-list"></div></article>
            </div>
        </section>

        <section class="page" id="page-products">
            <div class="section-actions"><div><p class="muted">Kelola jenis produk, merek, shade/varian, foto, harga, dan stok.</p></div><button class="primary" id="addProductButton">＋ Tambah Produk</button></div>
            <div class="panel table-wrap"><table><thead><tr><th>Produk</th><th>SKU</th><th>Jenis</th><th>Merek & Varian</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody id="productTableBody"></tbody></table></div>
        </section>

        <section class="page" id="page-transactions">
            <div class="panel table-wrap"><table><thead><tr><th>No. Nota</th><th>Pelanggan</th><th>Pembayaran</th><th>Total</th><th>Waktu</th><th>Nota</th></tr></thead><tbody id="transactionTableBody"></tbody></table></div>
        </section>
    </main>
</div>

<div class="modal" id="productModal" aria-hidden="true">
    <div class="modal-card">
        <div class="modal-header"><div><p class="eyebrow">KATALOG BEAUTY</p><h2 id="productModalTitle">Tambah Produk</h2></div><button class="close-modal" data-close="productModal">×</button></div>
        <form id="productForm" enctype="multipart/form-data">
            <input type="hidden" name="id" id="productId">
            <input type="hidden" name="existing_image" id="existingImage">
            <div class="form-grid">
                <label><span>Nama Produk</span><input name="name" id="productName" required maxlength="150" placeholder="Contoh: Colorfit Velvet Powder"></label>
                <label><span>Kode Produk</span><input name="sku" id="productSku" required maxlength="50" placeholder="Contoh: WRD-BDK-01"></label>
                <label><span>Jenis Produk</span><input name="category" id="productCategory" list="categorySuggestions" required placeholder="Contoh: Bedak"></label>
                <label><span>Merek</span><input name="brand" id="productBrand" list="brandSuggestions" required placeholder="Contoh: Wardah"></label>
                <label class="full"><span>Shade / Jenis / Ukuran</span><input name="variant" id="productVariant" required maxlength="120" placeholder="Contoh: 01 Light Beige"></label>
                <label><span>Harga</span><input name="price" id="productPrice" type="number" min="1" required></label>
                <label><span>Stok</span><input name="stock" id="productStock" type="number" min="0" required></label>
                <label class="full"><span>Foto Produk</span><input name="image" id="productImage" type="file" accept="image/jpeg,image/png,image/webp"><small>JPG/PNG/WEBP, maksimal 3 MB.</small></label>
            </div>
            <datalist id="categorySuggestions"><option>Bedak</option><option>Foundation</option><option>Lipstik</option><option>Serum</option><option>Sunscreen</option><option>Facial Wash</option></datalist>
            <datalist id="brandSuggestions"><option>Wardah</option><option>Make Over</option><option>Emina</option><option>Hanasui</option><option>Skintific</option><option>Azarine</option></datalist>
            <div class="modal-actions"><button type="button" class="secondary" data-close="productModal">Batal</button><button class="primary" type="submit">Simpan Produk</button></div>
        </form>
    </div>
</div>

<div class="modal" id="checkoutModal" aria-hidden="true">
    <div class="modal-card wide">
        <div class="modal-header"><div><p class="eyebrow">PEMBAYARAN</p><h2>Selesaikan Transaksi</h2></div><button class="close-modal" data-close="checkoutModal">×</button></div>
        <div class="checkout-grid">
            <div class="payment-form">
                <label><span>Nama Pelanggan <small>(opsional)</small></span><input id="customerName" placeholder="Contoh: Ibu Rina"></label>
                <label><span>Nomor WhatsApp <small>(opsional)</small></span><input id="customerPhone" inputmode="tel" placeholder="Contoh: 081234567890"></label>
                <label><span>Metode Pembayaran</span><select id="paymentMethod"><option>Tunai</option><option>QRIS</option><option>Transfer</option><option>Kartu</option></select></label>
                <label><span>Nominal Dibayar</span><input id="paidAmount" type="number" min="0"></label>
                <label><span>Catatan <small>(opsional)</small></span><textarea id="transactionNotes" rows="3" placeholder="Catatan produk atau pembayaran"></textarea></label>
            </div>
            <div class="payment-total-card"><span>TOTAL PEMBAYARAN</span><strong id="checkoutTotal">Rp0</strong><div class="change-row"><span>Kembalian</span><b id="changeText">Rp0</b></div><p>Stok shade yang dipilih akan berkurang dan pemasukan tercatat otomatis setelah transaksi disimpan.</p></div>
        </div>
        <div class="modal-actions"><button type="button" class="secondary" data-close="checkoutModal">Kembali</button><button type="button" class="primary" id="confirmTransaction">Simpan Transaksi</button></div>
    </div>
</div>

<div class="modal" id="successModal" aria-hidden="true">
    <div class="modal-card receipt-success">
        <div class="success-check">✓</div><p class="eyebrow">TRANSAKSI BERHASIL</p><h2 id="successCode">TRX-</h2><p id="successMessage">Pemasukan dan stok telah diperbarui.</p>
        <div class="receipt-actions">
            <a class="action-card" id="downloadReceipt" href="#"><span>⇩</span><strong>Download Nota</strong><small>Unduh sebagai PDF</small></a>
            <button class="action-card" id="printReceipt"><span>⌁</span><strong>Cetak Nota</strong><small>Printer biasa/thermal</small></button>
            <button class="action-card whatsapp" id="sendWhatsapp"><span>◉</span><strong>Kirim WhatsApp</strong><small>Nota digital pelanggan</small></button>
        </div>
        <button class="primary full-button" id="newTransaction">Transaksi Baru</button>
    </div>
</div>

<div class="toast" id="toast"></div>
<script>window.POS_CONFIG = <?= json_encode(['storeName' => $app['store_name']], JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="assets/js/app.js?v=3.1"></script>
</body>
</html>
