# ✅ Deployment Checklist

Gunakan checklist ini untuk memastikan semua langkah deployment sudah dilakukan dengan benar.

## 📦 Pre-Deployment

### Repository
- [ ] Kode sudah di-push ke GitHub/GitLab
- [ ] Repository adalah public atau sudah di-connect ke Render
- [ ] File `.env` **TIDAK** di-commit (ada di `.gitignore`)
- [ ] File `render.yaml` sudah ada di repository
- [ ] File `.env.example` sudah ada (untuk dokumentasi)

### Code Quality
- [ ] Tidak ada syntax error
- [ ] Semua dependency terdaftar di `composer.json`
- [ ] Semua npm packages terdaftar di `package.json`
- [ ] Build command sudah di-test lokal (opsional)

---

## 🗄️ Database Setup

- [ ] PostgreSQL database sudah dibuat di Render
- [ ] Database credentials sudah dicatat (host, port, database, user, password)
- [ ] Database region sudah dipilih (disarankan sama dengan web service)

---

## 🌐 Web Service Setup

### Service Configuration
- [ ] Web service sudah dibuat di Render
- [ ] Repository sudah di-connect
- [ ] Branch yang benar sudah dipilih (biasanya `main`)
- [ ] Build command sudah di-set dengan benar
- [ ] Start command sudah di-set dengan benar
- [ ] Plan sudah dipilih (Free untuk mulai)

### Environment Variables
- [ ] `APP_NAME` = `Desa Donoharjo`
- [ ] `APP_ENV` = `production`
- [ ] `APP_KEY` = *(sudah di-generate)*
- [ ] `APP_DEBUG` = `false`
- [ ] `APP_URL` = `https://donoharjo.desamu.web.id`
- [ ] `APP_TIMEZONE` = `Asia/Jakarta`
- [ ] `APP_LOCALE` = `id`
- [ ] `APP_FALLBACK_LOCALE` = `id`
- [ ] `DB_CONNECTION` = `pgsql`
- [ ] `DB_HOST` = *(dari database)*
- [ ] `DB_PORT` = `5432`
- [ ] `DB_DATABASE` = *(dari database)*
- [ ] `DB_USERNAME` = *(dari database)*
- [ ] `DB_PASSWORD` = *(dari database)*
- [ ] `CACHE_DRIVER` = `file`
- [ ] `SESSION_DRIVER` = `file`
- [ ] `QUEUE_CONNECTION` = `sync`
- [ ] `LOG_CHANNEL` = `stack`
- [ ] `LOG_LEVEL` = `error`

---

## 🔗 Custom Domain Setup

- [ ] Custom domain `donoharjo.desamu.web.id` sudah ditambahkan di Render
- [ ] Hostname untuk CNAME sudah dicatat
- [ ] CNAME record sudah ditambahkan di DNS provider
- [ ] DNS sudah terpropagasi (cek dengan `dig` atau `nslookup`)
- [ ] SSL certificate sudah aktif (status "Active" di Render)

---

## 🚀 Post-Deployment

### Database
- [ ] Migration sudah di-run (`php artisan migrate --force`)
- [ ] Seeder sudah di-run (jika perlu)
- [ ] Database connection berhasil

### Storage
- [ ] Storage symlink sudah dibuat (`php artisan storage:link`)
- [ ] Folder storage memiliki permission yang benar

### Application
- [ ] Website bisa diakses via `https://donoharjo.desamu.web.id`
- [ ] HTTPS aktif (ada icon gembok di browser)
- [ ] Tidak ada error di logs
- [ ] Halaman utama bisa dibuka
- [ ] CSS dan JavaScript assets muncul dengan benar
- [ ] Images dan files bisa diakses

### Functionality Testing
- [ ] Form pengaduan bisa di-submit
- [ ] Admin panel bisa diakses (`/admin`)
- [ ] Login admin berhasil
- [ ] Semua halaman utama bisa dibuka:
  - [ ] Homepage (`/`)
  - [ ] Berita (`/berita`)
  - [ ] Pengaduan (`/pengaduan`)
  - [ ] Agenda (`/agenda`)
  - [ ] APBDes (`/apbdes`)
  - [ ] Layanan Surat (`/layanan-surat`)
  - [ ] Peraturan Desa (`/peraturan-desa`)
  - [ ] Statistik (`/statistik-lengkap`)

### Security
- [ ] `APP_DEBUG` = `false` (tidak menampilkan error detail)
- [ ] `APP_KEY` sudah di-set (tidak kosong)
- [ ] Environment variables sensitive tidak ter-expose
- [ ] HTTPS aktif dan redirect HTTP ke HTTPS

---

## 📊 Monitoring

- [ ] Logs sudah dicek dan tidak ada error
- [ ] Service status "Live" di dashboard Render
- [ ] Health check path (`/`) merespons dengan benar
- [ ] Auto-deploy sudah diaktifkan (jika ingin)

---

## 🎯 Final Verification

- [ ] Semua checklist di atas sudah dicentang
- [ ] Website berfungsi dengan baik
- [ ] Tidak ada error yang muncul
- [ ] Performance sudah acceptable

---

## 📝 Notes

**Tanggal Deploy**: _______________  
**Deployed by**: _______________  
**Render Service URL**: _______________  
**Custom Domain**: `https://donoharjo.desamu.web.id`  
**Database Name**: _______________  

**Issues/Notes**:
- 
- 
- 

---

**Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Completed | ⬜ Blocked

