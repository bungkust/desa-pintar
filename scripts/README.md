# 📜 Scripts untuk Database Migration

Script-script untuk membantu manage database migration dari local ke production.

---

## 📋 Scripts

### 1. `verify-migration-files.sh`
Verifikasi semua file dan konfigurasi yang diperlukan untuk migration.

**Usage:**
```bash
./scripts/verify-migration-files.sh
```

**Yang di-verify:**
- ✅ .env file exists
- ✅ Migration files ada
- ✅ Production database config (PROD_DB_*)
- ✅ Laravel command `migrate:prod` exists
- ✅ Script executable

---

### 2. `migrate-to-prod.sh`
Jalankan migration ke production database dari local.

**Usage:**
```bash
# Dengan confirmation
./scripts/migrate-to-prod.sh

# Force (tanpa confirmation)
./scripts/migrate-to-prod.sh --force

# Pretend (lihat SQL queries tanpa execute)
./scripts/migrate-to-prod.sh --pretend

# Step by step
./scripts/migrate-to-prod.sh --force --step
```

**Requirements:**
- Production database config di `.env` (PROD_DB_*)
- Network access ke production database
- Laravel command `migrate:prod` tersedia

---

## ⚙️ Setup

### 1. Tambahkan Production DB Config ke `.env`

```env
# Production Database Configuration
PROD_DB_HOST=dpg-d4jddn8bdp1s73fs2af0-a
PROD_DB_PORT=5432
PROD_DB_DATABASE=desa_donoharjo
PROD_DB_USERNAME=desa_donoharjo_user
PROD_DB_PASSWORD=[password dari Render]
```

**Cara mendapatkan:**
1. Render Dashboard → PostgreSQL service
2. Tab "Info" atau "Connections"
3. Copy values ke `.env`

### 2. Make Scripts Executable

```bash
chmod +x scripts/*.sh
```

### 3. Verify Setup

```bash
./scripts/verify-migration-files.sh
```

---

## 🚀 Quick Start

```bash
# 1. Verify setup
./scripts/verify-migration-files.sh

# 2. Run migration
./scripts/migrate-to-prod.sh --force
```

---

## ⚠️ Important Notes

1. **Network Access**: 
   - Render PostgreSQL biasanya hanya bisa diakses dari Render services
   - Jika connection gagal, migration sudah otomatis di-run di Dockerfile saat deploy

2. **Security**: 
   - Jangan commit `.env` dengan production credentials
   - Gunakan environment variables yang aman

3. **Backup**: 
   - Selalu backup production database sebelum migration

---

**Status**: ✅ Ready to Use

