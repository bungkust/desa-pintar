# 🐳 Docker Deployment Guide untuk Render.com

Panduan deployment menggunakan Docker untuk aplikasi Laravel di Render.com.

---

## 📋 File yang Dibutuhkan

1. **`Dockerfile`** - Konfigurasi Docker image
2. **`.dockerignore`** - File yang di-exclude dari Docker build
3. **`render.yaml`** - Sudah di-update untuk menggunakan Docker runtime

---

## 🚀 Setup di Render Dashboard (Web Service) — Step by Step

### 1. Buat Web Service Baru

1. Login ke **Render Dashboard**
2. Klik tombol **“New +”** di sidebar kiri
3. Pilih **“Web Service”**
4. Pilih **“Build and deploy from a Git repository”**
5. Connect ke GitHub:
   - Jika belum, klik **“Connect account”**, authorize GitHub
   - Pilih repository: **`bungkust/desa-pintar`** (atau nama repo kamu)
   - Klik **“Connect”**

### 2. Pilih Runtime Docker

Pada halaman konfigurasi service:

1. **Name**: isi misalnya `desa-donoharjo-web`
2. **Region**: pilih region terdekat (misal: Singapore)
3. **Branch**: `main` (atau branch yang mau di-deploy)
4. **Root Directory**: kosongkan (karena `Dockerfile` di root)
5. **Runtime**:
   - Klik dropdown Runtime
   - Pilih **`Docker`** (bukan Node / Python / Go / dsb)
6. Render akan otomatis mendeteksi file **`Dockerfile`** di root repo.

### 3. Build & Start Command

Karena kita pakai Docker:

- **Build Command**: **kosongkan**  
  Docker akan build image berdasarkan instruksi di `Dockerfile`.

- **Start Command**: **kosongkan**  
  Render akan menjalankan perintah `CMD` dari `Dockerfile`:

```dockerfile
CMD php artisan serve --host=0.0.0.0 --port=$PORT
```

### 4. Plan & Auto Deploy

1. **Instance Type / Plan**: pilih **Free** dulu (bisa upgrade nanti)
2. Centang **Auto-deploy** jika ingin setiap push ke `main` otomatis deploy
3. Klik **“Create Web Service”**

Render sekarang akan:
- Clone repo
- Build Docker image mengikuti `Dockerfile`
- Menjalankan container dan mengekspos port `$PORT`

---

## 🔐 Environment Variables (Step by Step)

Setelah service dibuat:

1. Buka halaman service → tab **“Environment”**
2. Klik **“Add Environment Variable”** untuk setiap key di bawah:

**Wajib:**

- `APP_ENV` = `production`
- `APP_DEBUG` = `false`
- `APP_URL` = `https://donoharjo.desamu.web.id`
- `APP_KEY` = *(kosong dulu, akan diisi setelah deploy pertama)*

**Database (dari PostgreSQL service di Render):**

- `DB_CONNECTION` = `pgsql`
- `DB_HOST` = *(host dari halaman database, misalnya `dpg-xxx.render.com`)*  
- `DB_PORT` = `5432`
- `DB_DATABASE` = *(nama database dari Render)*
- `DB_USERNAME` = *(user database dari Render)*
- `DB_PASSWORD` = *(password database dari Render)*

**Lainnya (recommended):**

- `CACHE_DRIVER` = `file`
- `SESSION_DRIVER` = `file`
- `QUEUE_CONNECTION` = `sync`

Klik **“Save Changes”** setelah selesai.

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
4. **Start**: `php artisan serve` pada port `$PORT` (diset otomatis oleh Render)

---

## 📝 Post-Deployment Steps (Step by Step)

Setelah deploy pertama **berhasil** (status deploy = **Live**):

### 1. Generate APP_KEY

1. Di halaman service, buka tab **“Logs”**
2. Klik tombol **“Shell”** (biasanya di kanan atas)
3. Jalankan:

```bash
php artisan key:generate --show
```

4. Copy output (string `base64:...`)
5. Kembali ke tab **“Environment”**
6. Edit / tambah variable:
   - `APP_KEY` = *(paste nilai yang tadi di-copy)*
7. Klik **“Save Changes”** → Render akan restart container

### 2. Jalankan Migration

Masih di **Shell**:

```bash
php artisan migrate --force
```

- Pastikan tidak ada error
- Jika ada error, cek lagi konfigurasi database

### 3. Buat Storage Symlink

Masih di **Shell**:

```bash
php artisan storage:link
```

- Ini penting agar file upload (gambar, dll) bisa diakses dari `public/storage`

---

## 🌐 Setup Domain & HTTPS (Ringkas)

1. Di tab **“Settings”** → bagian **“Custom Domains”**
2. Klik **“Add Custom Domain”**
3. Masukkan: `donoharjo.desamu.web.id`
4. Render akan menampilkan **CNAME** target (misal: `desa-donoharjo-web.onrender.com`)
5. Di provider domain, buat **CNAME record**:
   - **Name**: `donoharjo`
   - **Value**: `desa-donoharjo-web.onrender.com` (dari Render)
6. Tunggu 5–30 menit hingga DNS propagate
7. Render akan otomatis mengaktifkan SSL (HTTPS)

---

## 🔍 Troubleshooting

### Build Failed

**Cek logs** di Render dashboard untuk detail error:
- Pastikan `Dockerfile` ada di root repository
- Pastikan semua dependencies di `composer.json` dan `package.json` valid

### Container Tidak Jalan / Port Error

- Dockerfile menjalankan:

```dockerfile
CMD php artisan serve --host=0.0.0.0 --port=$PORT
```

- Pastikan **JANGAN** override Start Command di Render (biarkan kosong)

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

