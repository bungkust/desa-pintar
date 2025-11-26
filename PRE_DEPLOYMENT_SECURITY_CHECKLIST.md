# 🔒 Pre-Deployment Security Checklist

Checklist keamanan yang **WAJIB** dicek sebelum deploy ke production di Render.com.

---

## ⚠️ CRITICAL - Harus Dicek Sebelum Deploy

### 1. Environment Variables (PENTING!)

- [ ] **`APP_ENV=production`** - JANGAN set ke `local` atau `development`
- [ ] **`APP_DEBUG=false`** - JANGAN set ke `true` di production!
- [ ] **`APP_KEY`** sudah di-generate dan di-set (tidak kosong)
- [ ] **`APP_URL`** = `https://donoharjo.desamu.web.id` (dengan HTTPS)
- [ ] **`LOG_LEVEL`** = `error` (bukan `debug`)

**Cara cek di Render:**
1. Buka service → Tab "Environment"
2. Pastikan semua variable di atas sudah benar

---

### 2. Database Security

- [ ] Database credentials **TIDAK** di-commit ke repository
- [ ] Database password **kuat** (minimal 16 karakter, campuran huruf/angka/simbol)
- [ ] Database connection menggunakan **SSL/TLS** (jika tersedia)
- [ ] Database user memiliki **privileges minimal** (hanya akses ke database yang diperlukan)

**Cek di Render:**
- Database → Tab "Info" → Pastikan password kuat
- Database → Tab "Connections" → Cek SSL settings

---

### 3. File Permissions & Storage

- [ ] Folder `storage/` dan `bootstrap/cache/` memiliki permission yang benar
- [ ] Storage symlink sudah dibuat (`php artisan storage:link`)
- [ ] File upload validation sudah aktif (MIME type, size limit)
- [ ] Uploaded files **TIDAK** executable

**Cek di Render Shell:**
```bash
php artisan storage:link
ls -la storage/
```

---

### 4. Security Headers

- [ ] Security headers middleware sudah aktif
- [ ] CSP (Content Security Policy) sudah di-set
- [ ] HSTS (Strict-Transport-Security) aktif untuk HTTPS
- [ ] X-Frame-Options = `SAMEORIGIN`
- [ ] X-Content-Type-Options = `nosniff`

**Cek:**
- File: `app/Http/Middleware/SecurityHeaders.php` sudah ada
- Middleware sudah terdaftar di `bootstrap/app.php`

---

### 5. Authentication & Authorization

- [ ] Admin panel hanya bisa diakses oleh user yang authorized
- [ ] Password hashing menggunakan bcrypt (default Laravel)
- [ ] Session security configured (httponly, secure cookies)
- [ ] CSRF protection aktif (Laravel default)

**Cek:**
- File: `app/Models/User.php` - implements `FilamentUser`
- File: `app/Policies/*.php` - semua resources punya policy

---

### 6. Input Validation

- [ ] Semua route parameters di-validate
- [ ] Form requests menggunakan validation rules
- [ ] Mass assignment protection (`$fillable` arrays, bukan `$guarded = []`)
- [ ] SQL injection prevention (menggunakan Laravel ORM)

**Cek:**
- Semua model punya `$fillable` array
- Semua controller menggunakan Form Requests atau validation
- Route parameters di-validate dengan regex

---

### 7. File Upload Security

- [ ] MIME type validation (hanya image: jpeg, png, webp, gif)
- [ ] File size limits (2-5MB)
- [ ] File names di-sanitize
- [ ] Uploaded files disimpan di public disk dengan permission yang benar

**Cek di Filament Resources:**
- File upload fields punya `acceptedFileTypes()`
- File upload fields punya `maxSize()`

---

### 8. URL Redirect Security (SSRF Protection)

- [ ] URL redirects di-validate sebelum redirect
- [ ] Private/local IP addresses di-block
- [ ] Domain whitelisting (jika menggunakan `ALLOWED_REDIRECT_DOMAINS`)

**Cek:**
- File: `app/Http/Requests/ValidateQuickLinkRedirect.php`
- Environment variable: `ALLOWED_REDIRECT_DOMAINS` (opsional)

---

### 9. Logging & Monitoring

- [ ] Audit logging aktif untuk admin actions
- [ ] Log injection prevention (data di-sanitize sebelum logging)
- [ ] Exception handler menyembunyikan detail error di production
- [ ] Logs tidak mengandung sensitive data (password, tokens, dll)

**Cek:**
- File: `app/Observers/AuditLogObserver.php`
- File: `bootstrap/app.php` - Exception handler untuk production

---

### 10. Dependencies & Vulnerabilities

- [ ] Run `composer audit` - tidak ada vulnerabilities
- [ ] Semua dependencies up-to-date
- [ ] `composer.lock` di-commit ke repository

**Cek di lokal:**
```bash
composer audit
composer outdated
```

---

### 11. Rate Limiting

- [ ] Rate limiting aktif di public routes
- [ ] Admin routes protected (tidak bisa diakses tanpa auth)
- [ ] Rate limits sesuai dengan kebutuhan (tidak terlalu ketat/lemah)

**Cek:**
- File: `routes/web.php` - ada `throttle:60,1` atau `throttle:120,1`

---

### 12. HTTPS & SSL

- [ ] Custom domain sudah di-setup
- [ ] SSL certificate aktif (Let's Encrypt otomatis di Render)
- [ ] HTTP redirect ke HTTPS (Render otomatis)
- [ ] HSTS header aktif

**Cek di Render:**
- Settings → Custom Domains → Status = "Active"
- Test: `https://donoharjo.desamu.web.id` → harus ada icon gembok

---

### 13. Git & Repository Security

- [ ] File `.env` **TIDAK** di-commit (ada di `.gitignore`)
- [ ] File `database/database.sqlite` **TIDAK** di-commit
- [ ] File `storage/logs/*.log` **TIDAK** di-commit
- [ ] Sensitive data tidak ada di commit history

**Cek:**
```bash
git check-ignore .env
git check-ignore database/database.sqlite
```

---

### 14. Error Handling

- [ ] Error pages tidak menampilkan stack trace di production
- [ ] Error messages generic (tidak expose system info)
- [ ] 404, 500, dll di-handle dengan baik

**Cek:**
- File: `bootstrap/app.php` - Exception handler untuk production
- Test: Coba akses URL yang tidak ada → harus tampil error page generic

---

### 15. CORS & API Security (jika ada API)

- [ ] CORS policy di-set dengan benar
- [ ] API endpoints protected dengan authentication
- [ ] API rate limiting (jika ada)

**Cek:**
- Jika tidak ada API, skip ini

---

## 📋 Quick Verification Commands

Jalankan command berikut di Render Shell untuk verifikasi cepat:

```bash
# 1. Cek environment
php artisan tinker --execute="echo config('app.env');"
php artisan tinker --execute="echo config('app.debug') ? 'true' : 'false';"

# 2. Cek database connection
php artisan migrate:status

# 3. Cek storage link
ls -la public/storage

# 4. Cek cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Test application
php artisan route:list
```

---

## 🚨 Red Flags - JANGAN Deploy Jika:

- ❌ `APP_DEBUG=true` di production
- ❌ `APP_ENV=local` atau `development`
- ❌ `APP_KEY` kosong atau belum di-generate
- ❌ Database password lemah atau di-commit
- ❌ File `.env` di-commit ke repository
- ❌ Ada vulnerability dari `composer audit`
- ❌ Security headers tidak aktif
- ❌ SSL certificate belum aktif
- ❌ Error pages menampilkan stack trace

---

## ✅ Final Checklist

Sebelum menandai deployment selesai:

- [ ] Semua checklist di atas sudah dicentang
- [ ] Website bisa diakses via HTTPS
- [ ] Admin panel bisa diakses dan login berhasil
- [ ] Tidak ada error di logs
- [ ] Test semua fitur utama:
  - [ ] Form pengaduan bisa submit
  - [ ] File upload berfungsi
  - [ ] Halaman-halaman utama bisa dibuka
  - [ ] Tidak ada error 500

---

## 📝 Notes

**Tanggal Security Check**: _______________  
**Checked by**: _______________  
**Issues Found**: _______________  
**Actions Taken**: _______________  

---

## 🔗 References

- [OWASP Top 10 (2021)](https://owasp.org/Top10/)
- [Laravel Security Documentation](https://laravel.com/docs/security)
- [Render.com Security Best Practices](https://render.com/docs/security)

---

**Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Completed | ⬜ Blocked

