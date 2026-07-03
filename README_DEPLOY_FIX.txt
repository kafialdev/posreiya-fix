POSREIYA VERCEL FINAL - FIX DEPLOY

Penyebab error deploy:
Vercel membaca vercel.json yang memakai pattern api/*.php, tetapi di deployment kamu Vercel tidak menemukan file PHP yang cocok. Versi ini dibuat lebih aman: hanya memakai 1 function, yaitu api/index.php.

WAJIB DI GITHUB:
Pastikan file/folder berikut ada di ROOT repository, bukan di dalam folder lain:
- vercel.json
- api/index.php
- index.php
- api.php
- bootstrap.php
- config.php
- assets/
- uploads/
- DATABASE_IMPORT_VERCEL.sql

Kalau di GitHub terlihat seperti POSREIYA_VERCEL_FINAL/api/index.php, berarti root-nya salah. Isi foldernya harus dipindah ke root repository.

Langkah cepat:
1. Extract ZIP ini.
2. Upload SEMUA ISI folder hasil extract ke root GitHub repository.
3. Pastikan di GitHub ada api/index.php.
4. Di Vercel, Project Settings > General > Root Directory kosongkan atau arahkan ke folder yang berisi vercel.json dan api/index.php.
5. Deploy ulang.
6. Buat database MySQL-compatible.
7. Import DATABASE_IMPORT_VERCEL.sql.
8. Isi Environment Variables:
   DB_HOST
   DB_PORT
   DB_NAME
   DB_USER
   DB_PASS
   APP_BASE_URL
9. Redeploy.
10. Cek /health.php dan /api.php?action=products.
