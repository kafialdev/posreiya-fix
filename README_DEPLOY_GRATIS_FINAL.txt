POSREIYA - FINAL VERCEL GRATIS

PENTING:
- Hosting di Vercel bisa.
- Database InfinityFree TIDAK bisa dipakai oleh Vercel karena MySQL InfinityFree free tidak bisa diakses dari server luar.
- Kalau mau full Vercel gratis, pakai database MySQL-compatible gratis seperti TiDB Cloud Serverless/Starter.

ISI ZIP INI SUDAH SIAP DEPLOY:
- vercel.json sudah benar: hanya api/index.php sebagai function.
- config.php baca database dari Environment Variables Vercel.
- SQL ada di DATABASE_IMPORT_VERCEL.sql.
- Tampilan tidak diubah.

LANGKAH RINGKAS:
1. Extract ZIP ini.
2. Upload SEMUA ISI folder ke GitHub. Jangan upload folder pembungkusnya.
3. Di Vercel, import repository GitHub.
4. Framework Preset: Other.
5. Build Command: kosong.
6. Output Directory: kosong.
7. Deploy.
8. Buat database gratis di TiDB Cloud.
9. Import DATABASE_IMPORT_VERCEL.sql di SQL Editor TiDB.
10. Masuk Vercel > Project > Settings > Environment Variables.
11. Isi:
    DB_HOST=host_dari_tidb
    DB_PORT=4000
    DB_NAME=nama_database
    DB_USER=username_dari_tidb
    DB_PASS=password_dari_tidb
    DB_SSL=true
    APP_BASE_URL=https://domain-vercel-kamu.vercel.app
12. Redeploy.
13. Tes:
    https://domain-vercel-kamu.vercel.app/health.php
    https://domain-vercel-kamu.vercel.app/api.php?action=products

Kalau health.php success true dan api products keluar JSON, website sudah konek database.

CATATAN UPLOAD GAMBAR:
Vercel tidak cocok untuk menyimpan upload file lokal secara permanen. Produk bawaan tetap tampil, transaksi tetap jalan. Untuk upload gambar permanen perlu Vercel Blob/Cloudinary.
