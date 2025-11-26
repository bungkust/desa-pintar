# 🔧 Deployment Troubleshooting Guide

Panduan untuk mengatasi masalah saat deploy ke Render.com.

---

## ❌ Error: "composer: No such file or directory"

### Penyebab
Composer tidak ditemukan di PATH saat build command dijalankan.

### Solusi 1: Update render.yaml (Recommended)

File `render.yaml` sudah di-update dengan build command yang install composer terlebih dahulu:

```yaml
buildCommand: |
  if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  fi &&
  /usr/local/bin/composer install --no-dev --optimize-autoloader &&
  npm ci &&
  npm run build &&
  php artisan config:cache &&
  php artisan route:cache &&
  php artisan view:cache
```

**Langkah**:
1. Commit dan push perubahan `render.yaml` ke repository
2. Render akan otomatis re-deploy dengan build command baru

### Solusi 2: Set Build Command di Render Dashboard

Jika `render.yaml` tidak digunakan, set build command langsung di dashboard:

1. Buka service di Render dashboard
2. Tab **"Settings"** → Scroll ke **"Build Command"**
3. Ganti dengan:
   ```bash
   if ! command -v composer &> /dev/null; then curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; fi && /usr/local/bin/composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
4. Klik **"Save Changes"**
5. Manual deploy: **"Manual Deploy"** → **"Deploy latest commit"**

### Solusi 3: Gunakan Buildpack PHP yang Sudah Include Composer

1. Di Render dashboard, buka service
2. Tab **"Settings"** → **"Build & Deploy"**
3. Pastikan **"Buildpack"** = `php` (bukan `node` atau lainnya)
4. Render akan otomatis detect PHP project dan install composer

---

## ❌ Error: "npm: command not found"

### Penyebab
Node.js tidak terdeteksi atau tidak terinstall.

### Solusi

1. Di Render dashboard, buka service
2. Tab **"Settings"** → **"Build & Deploy"**
3. Pastikan **"Node Version"** sudah di-set (misal: `22.16.0`)
4. Atau tambahkan di build command:
   ```bash
   export NVM_DIR="$HOME/.nvm" && [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" && nvm use 22
   ```

---

## ❌ Error: "Database connection failed"

### Penyebab
Environment variables database belum di-set atau salah.

### Solusi

1. Buka service → Tab **"Environment"**
2. Pastikan semua database variables sudah di-set:
   - `DB_CONNECTION=pgsql`
   - `DB_HOST` (dari database info)
   - `DB_PORT=5432`
   - `DB_DATABASE` (dari database info)
   - `DB_USERNAME` (dari database info)
   - `DB_PASSWORD` (dari database info)
3. **PENTING**: Copy-paste password, jangan ketik manual
4. Klik **"Save Changes"**
5. Service akan restart otomatis

---

## ❌ Error: "500 Internal Server Error"

### Penyebab
- APP_KEY belum di-set
- Environment variables kurang
- Permission error

### Solusi

1. **Generate APP_KEY**:
   - Buka service → Tab **"Logs"** → Klik **"Shell"**
   - Run: `php artisan key:generate --show`
   - Copy output
   - Buka Tab **"Environment"** → Set `APP_KEY` dengan value yang di-copy

2. **Cek Environment Variables**:
   - Pastikan semua required variables sudah di-set (lihat [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md))

3. **Cek Logs**:
   - Tab **"Logs"** untuk detail error

---

## ❌ Error: "Storage symlink failed"

### Penyebab
Storage symlink belum dibuat atau permission error.

### Solusi

1. Buka service → Tab **"Logs"** → Klik **"Shell"**
2. Run:
   ```bash
   php artisan storage:link
   ```
3. Pastikan tidak ada error

---

## ❌ Error: "Migration failed"

### Penyebab
Database connection error atau migration file error.

### Solusi

1. **Cek Database Connection**:
   - Pastikan semua DB_* environment variables sudah benar
   - Test connection di Shell: `php artisan migrate:status`

2. **Run Migration**:
   ```bash
   php artisan migrate --force
   ```

3. **Jika masih error**, cek detail di logs

---

## ❌ Error: "Build timeout"

### Penyebab
Build command terlalu lama (melebihi timeout limit).

### Solusi

1. **Optimize Build Command**:
   - Hapus command yang tidak perlu
   - Gunakan cache untuk npm: `npm ci --prefer-offline`

2. **Split Build Steps**:
   - Install dependencies terlebih dahulu
   - Build assets kemudian

---

## ❌ Error: "Domain not found" atau "SSL certificate failed"

### Penyebab
DNS belum terpropagasi atau CNAME record salah.

### Solusi

1. **Cek DNS Propagation**:
   ```bash
   dig donoharjo.desamu.web.id
   # atau
   nslookup donoharjo.desamu.web.id
   ```

2. **Verifikasi CNAME Record**:
   - Name: `donoharjo` (bukan `donoharjo.desamu.web.id`)
   - Value: Hostname dari Render (contoh: `desa-donoharjo-web.onrender.com`)

3. **Tunggu DNS Propagation**:
   - Biasanya 5-30 menit
   - SSL certificate akan otomatis aktif setelah DNS terpropagasi

---

## ✅ Tips untuk Success Deployment

1. **Gunakan render.yaml** untuk konsistensi
2. **Test build command lokal** sebelum push
3. **Cek logs** jika ada error
4. **Set environment variables** dengan benar
5. **Tunggu build selesai** sebelum test

---

## 📞 Butuh Bantuan Lebih?

- Render Documentation: https://render.com/docs
- Render Support: https://render.com/docs/support
- Laravel Documentation: https://laravel.com/docs

---

**Last Updated**: 2025-01-XX

