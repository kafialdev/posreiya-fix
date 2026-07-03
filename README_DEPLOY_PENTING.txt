PENTING - CARA DEPLOY AGAR TIDAK ERROR

Error kamu sebelumnya:
"The pattern api/index.php defined in functions doesn't match any Serverless Functions..."

Versi ini sudah diganti dari konfigurasi `functions` ke konfigurasi `builds`, jadi error itu tidak dipakai lagi.

WAJIB cek di GitHub setelah upload:
- file vercel.json harus ada di halaman utama repo
- folder api harus ada di halaman utama repo
- file api/index.php harus ada

Struktur benar:
api/index.php
assets/css/style.css
vercel.json
index.php
api.php
config.php
DATABASE_IMPORT_VERCEL.sql

Struktur salah:
POSREIYA_VERCEL_DEPLOY_FIX_FINAL/api/index.php
POSREIYA_VERCEL_DEPLOY_FIX_FINAL/vercel.json

Kalau struktur salah, di Vercel buka Settings > General > Root Directory dan isi nama folder tersebut, atau upload ulang ISI foldernya ke root repo.

Database gratis yang disarankan: TiDB Cloud Serverless/Starter.
Environment Variables di Vercel:
DB_HOST=host database
DB_PORT=4000
DB_NAME=nama database
DB_USER=username
DB_PASS=password
DB_SSL=true
APP_BASE_URL=https://domain-vercel-kamu.vercel.app

Setelah isi environment variables, wajib Redeploy.
Tes:
/domain-kamu/health.php
/domain-kamu/api.php?action=products
