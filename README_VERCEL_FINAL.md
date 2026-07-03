# POSREIYA Vercel Final

Paket ini sudah disiapkan untuk deploy PHP di Vercel memakai community runtime `vercel-php` dan database MySQL online.

## File penting

- `vercel.json` = konfigurasi runtime PHP dan route Vercel.
- `api/` = wrapper agar file PHP lama bisa berjalan di Vercel Functions.
- `config.php` = sudah memakai Environment Variables, jadi tidak perlu hard-code password.
- `DATABASE_IMPORT_VERCEL.sql` = SQL final untuk database MySQL online, tanpa `CREATE DATABASE`, tanpa `USE`, dan tanpa foreign key agar kompatibel dengan serverless MySQL.
- `health.php` = tes koneksi database.

## Environment Variables minimal

Isi di Vercel Project → Settings → Environment Variables:

DB_HOST=host_database_mysql
DB_PORT=3306
DB_NAME=nama_database
DB_USER=username_database
DB_PASS=password_database
APP_BASE_URL=https://domain-vercel-kamu.vercel.app

Kalau memakai PlanetScale dari Vercel Marketplace, integration biasanya mengisi:

PLANETSCALE_DB
PLANETSCALE_DB_USERNAME
PLANETSCALE_DB_PASSWORD
PLANETSCALE_DB_HOST

`config.php` sudah bisa membaca variabel PLANETSCALE_* tersebut.

## Catatan Vercel

Vercel tidak cocok untuk upload file lokal permanen. Fitur tambah produk tetap bisa jalan tanpa upload gambar. Gambar bawaan dari `assets/img` tetap tampil. Untuk upload gambar permanen perlu storage eksternal seperti Vercel Blob/Cloudinary.
