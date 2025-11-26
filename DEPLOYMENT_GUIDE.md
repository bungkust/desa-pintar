# 🚀 Panduan Deploy ke Render.com untuk Pemula

Panduan lengkap step-by-step untuk deploy aplikasi Desa Donoharjo ke Render.com dengan subdomain `donoharjo.desamu.web.id`.

---

## 📋 Daftar Isi

1. [Persiapan](#1-persiapan)
2. [Setup Akun Render.com](#2-setup-akun-rendercom)
3. [Setup Database PostgreSQL](#3-setup-database-postgresql)
4. [Setup Web Service](#4-setup-web-service)
5. [Konfigurasi Environment Variables](#5-konfigurasi-environment-variables)
6. [Setup Custom Domain](#6-setup-custom-domain)
7. [Konfigurasi DNS](#7-konfigurasi-dns)
8. [Testing & Verifikasi](#8-testing--verifikasi)
9. [Troubleshooting](#9-troubleshooting)

---

## 1. Persiapan

### ✅ Checklist Sebelum Deploy

- [ ] Kode sudah di-push ke GitHub/GitLab
- [ ] Repository adalah **public** atau Anda punya akses untuk connect ke Render
- [ ] File `.env` **TIDAK** di-commit ke repository (harus di `.gitignore`)
- [ ] Semua dependency sudah terdaftar di `composer.json` dan `package.json`
- [ ] **Security checklist sudah dicek** - Lihat [PRE_DEPLOYMENT_SECURITY_CHECKLIST.md](./PRE_DEPLOYMENT_SECURITY_CHECKLIST.md)

### 🔒 Security Check (PENTING!)

**SEBELUM deploy, WAJIB cek security checklist:**
- Buka file: [PRE_DEPLOYMENT_SECURITY_CHECKLIST.md](./PRE_DEPLOYMENT_SECURITY_CHECKLIST.md)
- Pastikan semua item sudah dicentang, terutama:
  - `APP_DEBUG=false`
  - `APP_ENV=production`
  - `APP_KEY` sudah di-generate
  - Tidak ada vulnerability dari `composer audit`

### 📝 File yang Perlu Dicek

Pastikan file-file berikut ada di repository:
- ✅ `composer.json`
- ✅ `package.json`
- ✅ `render.yaml` (sudah dibuat)
- ✅ `vite.config.js`
- ✅ `tailwind.config.js`
- ✅ `.gitignore` (pastikan `.env` ada di dalamnya)

---

## 2. Setup Akun Render.com

### Langkah 2.1: Buat Akun

1. Buka [https://render.com](https://render.com)
2. Klik **"Get Started for Free"** atau **"Sign Up"**
3. Pilih salah satu:
   - **Sign up with GitHub** (disarankan jika kode di GitHub)
   - **Sign up with Email**

### Langkah 2.2: Verifikasi Email

1. Cek email Anda
2. Klik link verifikasi
3. Login ke dashboard Render

---

## 3. Setup Database PostgreSQL

### Langkah 3.1: Buat PostgreSQL Database

1. Di dashboard Render, klik **"New +"** di sidebar kiri
2. Pilih **"PostgreSQL"**
3. Isi form:
   - **Name**: `desa-donoharjo-db` (atau nama lain)
   - **Database**: `desa_donoharjo` (atau biarkan default)
   - **User**: `desa_donoharjo_user` (atau biarkan default)
   - **Region**: Pilih yang terdekat (misal: Singapore)
   - **PostgreSQL Version**: Pilih versi terbaru
   - **Plan**: **Free** (untuk mulai)
4. Klik **"Create Database"**

### Langkah 3.2: Catat Database Credentials

Setelah database dibuat, Render akan menampilkan **Connection String**. **JANGAN TUTUP** halaman ini dulu!

Anda akan melihat informasi seperti:
```
Host: dpg-xxxxx-a.singapore-postgres.render.com
Port: 5432
Database: desa_donoharjo
User: desa_donoharjo_user
Password: [password yang ditampilkan]
```

**⚠️ PENTING**: Salin password dan simpan di tempat aman! Password hanya ditampilkan sekali.

---

## 4. Setup Web Service

### Langkah 4.1: Connect Repository

1. Di dashboard Render, klik **"New +"**
2. Pilih **"Web Service"**
3. Pilih **"Build and deploy from a Git repository"**
4. Connect ke GitHub/GitLab:
   - Jika belum connect, klik **"Connect account"**
   - Pilih repository **desa-donoharjo**
   - Klik **"Connect"**

### Langkah 4.2: Konfigurasi Service

Isi form dengan detail berikut:

**Basic Settings:**
- **Name**: `desa-donoharjo-web`
- **Region**: Pilih yang sama dengan database (misal: Singapore)
- **Branch**: `main` (atau branch yang ingin di-deploy)
- **Root Directory**: (kosongkan, biarkan default)
- **Runtime**: **PHP**
- **Build Command**: 
  ```bash
  if ! command -v composer &> /dev/null; then curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; fi && /usr/local/bin/composer install --no-dev --optimize-autoloader && npm ci && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
  ```
  
  **Catatan**: Build command ini akan install composer jika belum tersedia, lalu install dependencies dan build assets.
- **Start Command**: 
  ```bash
  php artisan serve --host=0.0.0.0 --port=${PORT}
  ```
- **Plan**: **Free** (untuk mulai)

**Advanced Settings:**
- **Health Check Path**: `/`
- **Auto-Deploy**: ✅ **Yes** (otomatis deploy saat push ke branch)

Klik **"Create Web Service"**

### Langkah 4.3: Tunggu Build Pertama

Render akan mulai build aplikasi. Proses ini bisa memakan waktu 5-10 menit.

**Jangan tutup halaman ini!** Kita akan setup environment variables selanjutnya.

---

## 5. Konfigurasi Environment Variables

### Langkah 5.1: Buka Environment Variables

1. Di halaman service yang baru dibuat, klik tab **"Environment"**
2. Scroll ke bagian **"Environment Variables"**

### Langkah 5.2: Tambahkan Variables

Klik **"Add Environment Variable"** dan tambahkan satu per satu:

#### A. Application Settings

| Key | Value | Keterangan |
|-----|-------|------------|
| `APP_NAME` | `Desa Donoharjo` | Nama aplikasi |
| `APP_ENV` | `production` | Environment |
| `APP_KEY` | *(akan di-generate)* | Lihat langkah 5.3 |
| `APP_DEBUG` | `false` | Jangan set `true` di production! |
| `APP_URL` | `https://donoharjo.desamu.web.id` | URL aplikasi |
| `APP_TIMEZONE` | `Asia/Jakarta` | Timezone |
| `APP_LOCALE` | `id` | Bahasa default |
| `APP_FALLBACK_LOCALE` | `id` | Bahasa fallback |

#### B. Database Settings

| Key | Value | Contoh |
|-----|-------|--------|
| `DB_CONNECTION` | `pgsql` | PostgreSQL |
| `DB_HOST` | *(dari database)* | `dpg-xxxxx-a.singapore-postgres.render.com` |
| `DB_PORT` | `5432` | Port PostgreSQL |
| `DB_DATABASE` | *(dari database)* | `desa_donoharjo` |
| `DB_USERNAME` | *(dari database)* | `desa_donoharjo_user` |
| `DB_PASSWORD` | *(dari database)* | *(password yang dicatat tadi)* |

**Cara mendapatkan database credentials:**
1. Buka halaman database PostgreSQL yang dibuat tadi
2. Di tab **"Info"**, ada **"Internal Database URL"**
3. Format: `postgresql://user:password@host:port/database`
4. Ambil bagian-bagiannya untuk diisi di environment variables

#### C. Cache & Session Settings

| Key | Value | Keterangan |
|-----|-------|------------|
| `CACHE_DRIVER` | `file` | Untuk free plan, gunakan file |
| `SESSION_DRIVER` | `file` | Untuk free plan, gunakan file |
| `QUEUE_CONNECTION` | `sync` | Untuk free plan, gunakan sync |

#### D. Logging

| Key | Value |
|-----|-------|
| `LOG_CHANNEL` | `stack` |
| `LOG_LEVEL` | `error` |

### Langkah 5.3: Generate APP_KEY

1. Setelah semua environment variables di-set, klik **"Manual Deploy"** → **"Deploy latest commit"**
2. Tunggu build selesai
3. Klik tab **"Logs"** di service
4. Klik **"Shell"** (di pojok kanan atas)
5. Ketik command:
   ```bash
   php artisan key:generate --show
   ```
6. Copy output yang muncul (format: `base64:...`)
7. Kembali ke tab **"Environment"**
8. Update `APP_KEY` dengan value yang baru di-copy
9. Klik **"Save Changes"**
10. Render akan otomatis restart service

---

## 6. Setup Custom Domain

### Langkah 6.1: Tambahkan Custom Domain

1. Di halaman service, klik tab **"Settings"**
2. Scroll ke bagian **"Custom Domains"**
3. Klik **"Add"**
4. Masukkan: `donoharjo.desamu.web.id`
5. Klik **"Save"**

### Langkah 6.2: Catat Hostname untuk DNS

Setelah menambahkan domain, Render akan menampilkan:
- **Hostname untuk CNAME**: Contoh `desa-donoharjo-web.onrender.com`
- **Atau IP Address**: Jika menggunakan A record

**⚠️ PENTING**: Catat hostname ini! Kita akan pakai untuk setup DNS.

---

## 7. Konfigurasi DNS

### Langkah 7.1: Login ke Provider Domain

1. Login ke panel DNS provider domain Anda (misal: cPanel, Cloudflare, Namecheap, dll)
2. Buka bagian **"DNS Management"** atau **"DNS Records"**

### Langkah 7.2: Tambahkan CNAME Record

Tambahkan record baru:

**Type**: `CNAME`  
**Name**: `donoharjo`  
**Value**: `[hostname dari Render, contoh: desa-donoharjo-web.onrender.com]`  
**TTL**: `3600` (atau biarkan default)

**⚠️ CATATAN PENTING:**
- Di field **Name**, hanya masukkan `donoharjo`, **BUKAN** `donoharjo.desamu.web.id`
- Di field **Value**, masukkan hostname lengkap dari Render (dengan `.onrender.com`)

### Langkah 7.3: Verifikasi DNS Propagation

Tunggu 5-15 menit, lalu cek dengan command di terminal:

```bash
# Di Mac/Linux
dig donoharjo.desamu.web.id

# Atau
nslookup donoharjo.desamu.web.id
```

Atau gunakan tool online: [https://dnschecker.org](https://dnschecker.org)

Pastikan hasilnya mengarah ke hostname Render.

---

## 8. Testing & Verifikasi

### Langkah 8.1: Tunggu SSL Certificate

Setelah DNS terpropagasi (biasanya 5-30 menit), Render akan otomatis:
1. Mendeteksi domain
2. Meminta SSL certificate dari Let's Encrypt
3. Mengaktifkan HTTPS

Proses ini bisa dilihat di tab **"Settings"** → **"Custom Domains"**. Status akan berubah dari "Pending" menjadi "Active".

### Langkah 8.2: Test Website

1. Buka browser
2. Akses: `https://donoharjo.desamu.web.id`
3. Pastikan:
   - ✅ Website bisa diakses
   - ✅ Ada icon gembok (HTTPS aktif)
   - ✅ Tidak ada error
   - ✅ Halaman utama muncul

### Langkah 8.3: Run Migration Database

1. Di Render dashboard, buka service
2. Klik tab **"Logs"**
3. Klik **"Shell"**
4. Run command:
   ```bash
   php artisan migrate --force
   ```
5. Tunggu sampai selesai

### Langkah 8.4: Setup Storage Link

1. Di Shell yang sama, run:
   ```bash
   php artisan storage:link
   ```
2. Pastikan tidak ada error

### Langkah 8.5: Test Admin Panel

1. Akses: `https://donoharjo.desamu.web.id/admin`
2. Buat user admin pertama (jika belum ada)
3. Login dan test fitur-fitur

---

## 9. Troubleshooting

### ❌ Problem: Build Failed

**Penyebab**: Dependency error atau command salah

**Solusi**:
1. Cek tab **"Logs"** untuk melihat error detail
2. Pastikan `composer.json` dan `package.json` sudah benar
3. Pastikan build command sudah sesuai

### ❌ Problem: Database Connection Error

**Penyebab**: Environment variables database salah

**Solusi**:
1. Cek semua database environment variables
2. Pastikan password sudah benar (copy-paste, jangan ketik manual)
3. Pastikan host, port, database name, dan username sudah benar

### ❌ Problem: 500 Internal Server Error

**Penyebab**: 
- APP_KEY belum di-set
- Environment variables kurang
- Permission error

**Solusi**:
1. Pastikan `APP_KEY` sudah di-generate dan di-set
2. Cek semua environment variables sudah lengkap
3. Cek logs di tab **"Logs"** untuk detail error

### ❌ Problem: Domain Tidak Bisa Diakses

**Penyebab**: 
- DNS belum terpropagasi
- CNAME record salah
- SSL certificate belum aktif

**Solusi**:
1. Tunggu 15-30 menit untuk DNS propagation
2. Verifikasi CNAME record sudah benar
3. Cek status SSL di dashboard Render

### ❌ Problem: Assets (CSS/JS) Tidak Muncul

**Penyebab**: Vite build belum jalan atau path salah

**Solusi**:
1. Pastikan `npm run build` berhasil di build command
2. Cek `vite.config.js` sudah benar
3. Pastikan `APP_URL` sudah di-set dengan benar

### ❌ Problem: Storage Files Tidak Muncul

**Penyebab**: Storage symlink belum dibuat

**Solusi**:
1. Run `php artisan storage:link` di Shell
2. Pastikan permission folder storage sudah benar

---

## 📝 Checklist Final

Sebelum menandai deployment selesai, pastikan:

- [ ] Website bisa diakses via `https://donoharjo.desamu.web.id`
- [ ] HTTPS aktif (ada icon gembok)
- [ ] Database migration sudah di-run
- [ ] Storage link sudah dibuat
- [ ] Admin panel bisa diakses
- [ ] Semua halaman utama bisa dibuka
- [ ] Form pengaduan bisa di-submit
- [ ] Tidak ada error di logs

---

## 🎉 Selamat!

Website Desa Donoharjo sudah berhasil di-deploy ke production!

### Tips Maintenance:

1. **Auto-Deploy**: Setiap push ke branch `main` akan otomatis deploy
2. **Monitor Logs**: Cek tab **"Logs"** secara berkala
3. **Backup Database**: Render free plan tidak include auto-backup, pertimbangkan backup manual
4. **Update Dependencies**: Update `composer.json` dan `package.json` secara berkala

### Butuh Bantuan?

- Render Documentation: [https://render.com/docs](https://render.com/docs)
- Laravel Documentation: [https://laravel.com/docs](https://laravel.com/docs)

---

**Terakhir di-update**: 2024

