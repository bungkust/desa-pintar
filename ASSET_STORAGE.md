# 📁 Lokasi Penyimpanan Aset

## Lokasi File

Semua file upload (gambar, dokumen, dll) disimpan di:

```
storage/app/public/
├── hero-slides/          # Gambar hero slide homepage
├── posts/thumbnails/     # Thumbnail artikel/berita
├── officials/photos/      # Foto pejabat desa
└── [folder lainnya]      # Folder lain sesuai kebutuhan
```

## Struktur Storage

Laravel menggunakan **symlink** untuk menghubungkan folder `storage/app/public` ke `public/storage` agar file bisa diakses via web.

```
public/storage → storage/app/public
```

## ✅ Memastikan Aman di Production

### 1. Symlink Storage

Symlink **otomatis dibuat** saat deployment via Dockerfile:

```dockerfile
php artisan storage:link
```

**Manual check** (jika perlu):
```bash
php artisan storage:link
ls -la public/storage  # Harus menunjukkan symlink
```

### 2. Konfigurasi APP_URL

Di production, pastikan `APP_URL` di `.env` atau environment variables sudah benar:

```env
APP_URL=https://donoharjo.desamu.web.id
```

**Di Render.com:**
- Sudah dikonfigurasi di `render.yaml`
- Atau set manual di Environment Variables dashboard

### 3. URL Generation

Kode sudah **production-safe**:
- ✅ Di **production**: Menggunakan `APP_URL` dari config (aman)
- ✅ Di **local development**: Auto-fix jika host berbeda (localhost vs 127.0.0.1)

### 4. File Permissions

Dockerfile sudah set permissions yang benar:
```dockerfile
chmod -R 777 /var/www/html/storage
```

### 5. Backup & Persistence

⚠️ **PENTING**: File di `storage/app/public/` **TIDAK** di-backup otomatis oleh Git (ada di `.gitignore`).

**Untuk backup:**
- Gunakan backup service (S3, Google Cloud Storage, dll)
- Atau backup manual via database + file storage

**Untuk persistence di Render:**
- Render menggunakan **ephemeral storage** (file hilang saat redeploy)
- **Solusi**: Gunakan **external storage** (S3, Cloudinary, dll) untuk production

## 🔧 Troubleshooting

### Gambar tidak muncul di homepage

1. **Cek symlink:**
   ```bash
   ls -la public/storage
   ```

2. **Cek file ada:**
   ```bash
   ls -la storage/app/public/hero-slides/
   ```

3. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

4. **Cek APP_URL:**
   ```bash
   php artisan tinker
   >>> config('app.url')
   ```

### URL salah di production

Pastikan `APP_URL` di environment variables sudah benar:
- ✅ `https://donoharjo.desamu.web.id` (dengan https)
- ❌ `http://localhost` (salah untuk production)

## 📝 Catatan Penting

1. **File Upload via Filament Admin:**
   - Otomatis disimpan ke `storage/app/public/[folder]`
   - Otomatis di-convert ke WebP (jika memungkinkan)
   - URL otomatis di-generate dengan benar

2. **Storage Disk:**
   - Menggunakan disk `public` (bukan `local`)
   - File bisa diakses via web browser

3. **Production Recommendation:**
   - Untuk production yang lebih robust, pertimbangkan menggunakan:
     - AWS S3
     - Google Cloud Storage
     - Cloudinary (untuk image optimization)
   - Update `config/filesystems.php` untuk menggunakan cloud storage

## 🚀 Deployment Checklist

- [x] Symlink dibuat otomatis (via Dockerfile)
- [x] APP_URL dikonfigurasi di production
- [x] Permissions sudah benar (777 untuk storage)
- [x] URL generation production-safe
- [ ] (Optional) Setup cloud storage untuk persistence


