# 🚀 Deployment ke Render.com

Dokumentasi lengkap untuk deploy aplikasi Desa Donoharjo ke Render.com dengan subdomain `donoharjo.desamu.web.id`.

## 📚 Dokumentasi

1. **[QUICK_DEPLOY.md](./QUICK_DEPLOY.md)** - Panduan cepat (5 menit) untuk yang sudah familiar
2. **[DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)** - Panduan lengkap step-by-step untuk pemula
3. **[DOCKER_DEPLOYMENT.md](./DOCKER_DEPLOYMENT.md)** - 🐳 Panduan khusus Docker deployment
4. **[DEPLOYMENT_CHECKLIST.md](./DEPLOYMENT_CHECKLIST.md)** - Checklist untuk memastikan semua langkah sudah dilakukan
5. **[PRE_DEPLOYMENT_SECURITY_CHECKLIST.md](./PRE_DEPLOYMENT_SECURITY_CHECKLIST.md)** - 🔒 **WAJIB DICEK!** Security checklist sebelum deploy

## 🎯 Quick Start

Jika Anda sudah familiar dengan deployment, ikuti [QUICK_DEPLOY.md](./QUICK_DEPLOY.md).

Jika Anda pemula, ikuti [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md) dari awal sampai akhir.

## 📋 File Konfigurasi

- **`Dockerfile`** - Konfigurasi Docker untuk deployment (⚠️ **WAJIB**)
- **`.dockerignore`** - File yang di-exclude dari Docker build
- **`render.yaml`** - Konfigurasi untuk Render.com (auto-deploy dengan Docker)
- **`.renderignore`** - File yang di-ignore saat deployment
- **`.env.example`** - Template environment variables

## 🐳 Docker Deployment

Project ini menggunakan **Docker** untuk deployment karena Render tidak menyediakan PHP runtime langsung.

**PENTING**: 
- Pastikan `Dockerfile` ada di root repository
- Pilih **Runtime: Docker** di Render dashboard
- Build dan Start commands akan di-handle oleh Dockerfile

Lihat [DOCKER_DEPLOYMENT.md](./DOCKER_DEPLOYMENT.md) untuk detail lengkap.

## 🔑 Environment Variables yang Diperlukan

### Wajib
- `APP_KEY` - Generate dengan `php artisan key:generate --show`
- `APP_URL` - `https://donoharjo.desamu.web.id`
- `DB_*` - Database credentials dari PostgreSQL

### Lengkap
Lihat [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md#langkah-52-tambahkan-variables) untuk daftar lengkap.

## 🌐 Domain Setup

1. Tambahkan custom domain di Render: `donoharjo.desamu.web.id`
2. Setup CNAME record di DNS provider
3. Tunggu SSL certificate aktif (otomatis)

Detail lengkap: [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md#6-setup-custom-domain)

## ✅ Post-Deployment

Setelah deploy, jangan lupa:
1. Run migration: `php artisan migrate --force`
2. Create storage link: `php artisan storage:link`
3. Test semua halaman

## 🆘 Butuh Bantuan?

- Cek [Troubleshooting](./DEPLOYMENT_GUIDE.md#9-troubleshooting)
- Render Docs: https://render.com/docs
- Laravel Docs: https://laravel.com/docs

---

**Status**: ✅ Ready to Deploy

