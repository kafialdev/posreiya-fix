# POSREIYA Vercel Final Gratis

Project ini sudah disiapkan untuk deploy ke Vercel memakai `vercel-php` dan database MySQL-compatible online.

Baca file **README_DEPLOY_GRATIS_FINAL.txt** untuk langkah singkat.

File penting:
- `vercel.json` — konfigurasi Vercel final, hanya memakai `api/index.php`.
- `api/index.php` — single PHP entrypoint untuk Vercel.
- `config.php` — membaca database dari Environment Variables.
- `DATABASE_IMPORT_VERCEL.sql` — SQL final untuk import database.
- `health.php` — tes koneksi database.

Environment Variables minimal:

```txt
DB_HOST=
DB_PORT=4000
DB_NAME=
DB_USER=
DB_PASS=
DB_SSL=true
APP_BASE_URL=https://domain-vercel-kamu.vercel.app
```

InfinityFree MySQL free tidak bisa dipakai sebagai database remote untuk Vercel. Pakai TiDB Cloud Serverless/Starter untuk opsi gratis MySQL-compatible.
