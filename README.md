# Desa Donoharjo - Production Deployment Guide

## 🚀 Production Deployment Steps

### Environment Setup
```bash
# Set production environment variables
APP_ENV=production
APP_TIMEZONE=Asia/Jakarta
APP_DEBUG=false
DB_CONNECTION=pgsql  # or mysql
```

### Pre-Deployment Commands
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# Database setup
php artisan migrate --force
php artisan db:seed

# Clear all caches
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### File Permissions (Linux/Ubuntu)
```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data /var/www/html
```

### File Permissions (macOS)
```bash
# macOS permissions (no www-data group)
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
# Use current user or _www if using built-in Apache
chown -R $(whoami) storage/
chown -R $(whoami) bootstrap/cache/
```

### File Permissions (Docker)
```bash
# In Dockerfile
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache
```

### User & Permission Setup
```bash
# Create admin users with proper roles
# - super_admin: Full access
# - admin_desa: Most admin functions
# - lurah: Limited admin access
# - petugas: Assigned complaint access
# - viewer: Read-only access
```

### Production Testing Checklist
- [ ] Admin login works with proper roles
- [ ] Complaint CRUD operations functional
- [ ] Status updates via modal work
- [ ] User assignments via modal work
- [ ] Comments page accessible and functional
- [ ] Public blog displays posts correctly
- [ ] Dark mode works in admin
- [ ] Mobile responsiveness verified
- [ ] No console errors in browser
- [ ] Page load times under 2 seconds

## 🔧 Troubleshooting

### macOS Specific Issues
```bash
# If you get "illegal group name" errors:
# Don't use www-data group on macOS
sudo chown -R $(whoami) storage/
sudo chown -R $(whoami) bootstrap/cache/

# Or if using built-in Apache:
sudo chown -R _www storage/
sudo chown -R _www bootstrap/cache/
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# For Laravel logs
touch storage/logs/laravel.log
chmod 664 storage/logs/laravel.log
```

### Cache Issues
```bash
# Clear all Laravel caches
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 📋 Features Status

### ✅ Completed Features
- Complaint management with full CRUD
- Status updates with activity logging
- User assignment system
- Comment system with dedicated UI
- Dark mode support
- Timezone configuration (Asia/Jakarta)
- CSP security headers
- Mobile responsive design
- SEO optimization

### 🔒 Security Features
- Role-based access control
- Content Security Policy
- Input validation
- XSS protection
- CSRF protection

### ⚡ Performance Features
- Database query caching
- View caching
- Asset optimization
- CDN-ready cache headers
