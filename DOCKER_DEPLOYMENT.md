# 🐳 Docker Deployment Guide untuk Render.com

Panduan deployment menggunakan Docker untuk aplikasi Laravel di Render.com.

---

## 📋 File yang Dibutuhkan

1. **`Dockerfile`** - Konfigurasi Docker image
2. **`.dockerignore`** - File yang di-exclude dari Docker build
3. **`render.yaml`** - Sudah di-update untuk menggunakan Docker runtime

---

## 🚀 Setup di Render Dashboard

### 1. Pilih Runtime Docker

1. Buka Render Dashboard
2. Buat service baru atau edit service yang ada
3. Pilih **Runtime: Docker**
4. Render akan otomatis detect `Dockerfile`

### 2. Konfigurasi Service

**Build Command**: (Kosongkan - Dockerfile handle)
**Start Command**: (Kosongkan - CMD di Dockerfile handle)

### 3. Environment Variables

Set environment variables yang diperlukan:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (generate setelah deploy)
- `APP_URL=https://donoharjo.desamu.web.id`
- Database variables (dari PostgreSQL service)

---

## 🔧 Dockerfile Overview

Dockerfile yang dibuat akan:

1. **Base Image**: PHP 8.2 CLI
2. **Install Dependencies**:
   - System packages (git, curl, zip, dll)
   - PHP extensions (pdo, pdo_pgsql, gd, zip, mbstring, dll)
   - Node.js & npm
   - Composer
3. **Build Application**:
   - Install PHP dependencies (`composer install`)
   - Install Node.js dependencies (`npm ci`)
   - Build assets (`npm run build`)
   - Cache Laravel (config, routes, views)
4. **Start**: `php artisan serve` on port 8000

---

## 📝 Post-Deployment Steps

Setelah deploy berhasil:

### 1. Generate APP_KEY

1. Buka service → Tab "Logs" → Klik "Shell"
2. Run:
   ```bash
   php artisan key:generate --show
   ```
3. Copy output
4. Buka Tab "Environment" → Set `APP_KEY` dengan value yang di-copy

### 2. Run Migration

Di Shell yang sama:
```bash
php artisan migrate --force
```

### 3. Create Storage Link

```bash
php artisan storage:link
```

---

## 🔍 Troubleshooting

### Build Failed

**Cek logs** di Render dashboard untuk detail error:
- Pastikan `Dockerfile` ada di root repository
- Pastikan semua dependencies di `composer.json` dan `package.json` valid

### Port Error

Dockerfile menggunakan `${PORT:-8000}` yang akan:
- Gunakan `PORT` environment variable jika ada (Render otomatis set)
- Fallback ke 8000 jika tidak ada

### Permission Error

Dockerfile sudah set permissions untuk:
- `storage/` directory
- `bootstrap/cache/` directory

Jika masih error, cek di Shell:
```bash
ls -la storage/
ls -la bootstrap/cache/
```

---

## ✅ Advantages Docker Deployment

1. **Konsisten**: Environment sama di semua tempat
2. **Isolated**: Tidak ada konflik dengan system packages
3. **Reproducible**: Build yang sama menghasilkan image yang sama
4. **Flexible**: Bisa customize sesuai kebutuhan

---

## 📚 References

- [Render Docker Documentation](https://render.com/docs/docker)
- [Laravel Docker Documentation](https://laravel.com/docs/sail)
- [Dockerfile Best Practices](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/)

---

**Status**: ✅ Ready for Docker Deployment

