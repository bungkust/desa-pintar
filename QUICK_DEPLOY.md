# ⚡ Quick Deploy Guide - Render.com

Panduan cepat untuk deploy ke Render.com. Untuk panduan lengkap, lihat [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md).

## 🚀 Langkah Cepat (5 Menit)

### 1. Buat Database PostgreSQL
- Render Dashboard → New + → PostgreSQL
- Name: `desa-donoharjo-db`
- Plan: Free
- **Catat credentials!**

### 2. Buat Web Service
- Render Dashboard → New + → Web Service
- Connect GitHub repository
- Settings:
  - **Runtime**: **Docker** ⚠️ (Bukan PHP atau Node.js)
  - **Build Command**: (Kosongkan - Dockerfile handle)
  - **Start Command**: (Kosongkan - CMD di Dockerfile handle)
  - Render akan otomatis detect `Dockerfile`

### 3. Set Environment Variables

**Wajib:**
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://donoharjo.desamu.web.id
APP_KEY=[generate dengan: php artisan key:generate --show]
```

**Database:**
```
DB_CONNECTION=pgsql
DB_HOST=[dari database info]
DB_PORT=5432
DB_DATABASE=[dari database info]
DB_USERNAME=[dari database info]
DB_PASSWORD=[dari database info]
```

### 4. Setup Domain
- Settings → Custom Domains → Add
- Domain: `donoharjo.desamu.web.id`
- **Catat hostname untuk DNS**

### 5. Setup DNS
- Di provider domain, tambahkan CNAME:
  - Name: `donoharjo`
  - Value: `[hostname dari Render]`

### 6. Run Migration
- Logs → Shell → Run:
  ```bash
  php artisan migrate --force
  php artisan storage:link
  ```

## ✅ Done!

Akses: `https://donoharjo.desamu.web.id`

---

## 🔧 Troubleshooting

**Build Failed?** → Cek Logs tab untuk error detail

**500 Error?** → Pastikan APP_KEY sudah di-set

**Domain tidak bisa?** → Tunggu 15-30 menit untuk DNS propagation

**Database Error?** → Cek semua DB_* environment variables

---

📖 **Panduan Lengkap**: [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)

