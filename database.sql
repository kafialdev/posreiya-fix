-- DATABASE_IMPORT_VERCEL.sql
-- Final untuk Vercel + database MySQL online (PlanetScale/Aiven/TiDB/Railway MySQL, dll).
-- Jalankan di database yang sudah dibuat. File ini sengaja TANPA CREATE DATABASE dan TANPA USE.
-- Versi ini juga TANPA FOREIGN KEY agar lebih kompatibel dengan database serverless MySQL seperti PlanetScale/Vitess.

CREATE TABLE IF NOT EXISTS products (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transaction_items (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products (sku, name, category, brand, variant, price, stock, image, active) VALUES
('WRD-BDK-01', 'Colorfit Velvet Powder Foundation', 'Bedak', 'Wardah', '01 Light Beige', 89000, 24, 'assets/img/skincare/wardah-bedak-light-beige.svg', 1),
('WRD-BDK-02', 'Colorfit Velvet Powder Foundation', 'Bedak', 'Wardah', '02 Natural', 89000, 18, 'assets/img/skincare/wardah-bedak-natural.svg', 1),
('MKO-BDK-W22', 'Powerstay Matte Powder Foundation', 'Bedak', 'Make Over', 'W22 Warm Ivory', 159000, 14, 'assets/img/skincare/makeover-bedak-w22.svg', 1),
('MKO-BDK-N30', 'Powerstay Matte Powder Foundation', 'Bedak', 'Make Over', 'N30 Natural Beige', 159000, 12, 'assets/img/skincare/makeover-bedak-n30.svg', 1),
('EMN-BDK-01', 'Bare With Me Mineral Compact Powder', 'Bedak', 'Emina', '01 Fair', 54000, 27, 'assets/img/skincare/emina-bedak-fair.svg', 1),
('WRD-LIP-01', 'Colorfit Velvet Matte Lip Mousse', 'Lipstik', 'Wardah', '01 Brown Dreamer', 79000, 32, 'assets/img/skincare/wardah-lip-brown.svg', 1),
('WRD-LIP-05', 'Colorfit Velvet Matte Lip Mousse', 'Lipstik', 'Wardah', '05 Artisan Mauve', 79000, 21, 'assets/img/skincare/wardah-lip-mauve.svg', 1),
('HNS-LIP-02', 'Mattedorable Lip Cream', 'Lipstik', 'Hanasui', '02 Chic', 35000, 36, 'assets/img/skincare/hanasui-lip-chic.svg', 1),
('HNS-LIP-04', 'Mattedorable Lip Cream', 'Lipstik', 'Hanasui', '04 Pink Latte', 35000, 29, 'assets/img/skincare/hanasui-lip-pink.svg', 1),
('SKN-SRM-5X', '5X Ceramide Barrier Repair Serum', 'Serum', 'Skintific', '20 ml', 129000, 20, 'assets/img/skincare/skintific-serum-ceramide.svg', 1),
('SKN-SRM-NIA', '10% Niacinamide Brightening Serum', 'Serum', 'Skintific', '20 ml', 139000, 16, 'assets/img/skincare/skintific-serum-niacinamide.svg', 1),
('AZR-SUN-HYD', 'Hydrasoothe Sunscreen Gel SPF45', 'Sunscreen', 'Azarine', '50 ml', 69000, 31, 'assets/img/skincare/azarine-sunscreen-hydra.svg', 1),
('AZR-SUN-TUP', 'Tone Up Mineral Sunscreen Serum SPF50', 'Sunscreen', 'Azarine', '40 ml', 75000, 25, 'assets/img/skincare/azarine-sunscreen-toneup.svg', 1),
('EMN-FW-BRT', 'Bright Stuff Face Wash', 'Facial Wash', 'Emina', '50 ml', 32000, 40, 'assets/img/skincare/emina-wash-bright.svg', 1),
('EMN-FW-ACN', 'Ms. Pimple Acne Solution Face Wash', 'Facial Wash', 'Emina', '50 ml', 38000, 22, 'assets/img/skincare/emina-wash-pimple.svg', 1),
('MKO-FND-W22', 'Powerstay Weightless Liquid Foundation', 'Foundation', 'Make Over', 'W22 Warm Ivory', 189000, 13, 'assets/img/skincare/makeover-foundation-w22.svg', 1),
('MKO-FND-N30', 'Powerstay Weightless Liquid Foundation', 'Foundation', 'Make Over', 'N30 Natural Beige', 189000, 10, 'assets/img/skincare/makeover-foundation-n30.svg', 1),
('WRD-FND-21N', 'Colorfit Matte Foundation', 'Foundation', 'Wardah', '21N Shell Ivory', 95000, 19, 'assets/img/skincare/wardah-foundation-shell.svg', 1)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    category = VALUES(category),
    brand = VALUES(brand),
    variant = VALUES(variant),
    price = VALUES(price),
    image = VALUES(image),
    active = 1;
