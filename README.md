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

### 🚨 CRITICAL: Asset Building Fix for Production
```bash
# IMPORTANT: Asset building often fails in production due to permissions
# Use this workaround:

# 1. Build on local/development machine first
npm run build

# 2. Or use the production build script
chmod +x build-production.sh
./build-production.sh

# 3. If build fails, use this emergency fix:
mkdir -p public/build/assets/css
echo "/* Temporary production CSS */" > public/build/assets/css/app-CRz7xcJV.css

# 4. Update manifest.json if needed
# Copy from local build to production
```

### Pre-Deployment Commands
```bash
# Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# Skip npm build if using emergency fix above
# npm ci && npm run build

# Database setup
php artisan migrate --force
php artisan db:seed

# Clear all caches (IMPORTANT for production)
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

### Asset Loading Issues (404 CSS/JS errors)
```bash
# If you see 404 errors for CSS/JS files like app-CRz7xcJV.css:

# 1. Check if build files exist
ls -la public/build/assets/css/
ls -la public/build/assets/js/

# 2. Clear browser cache completely
# Chrome: Ctrl+Shift+R or Cmd+Shift+R

# 3. Clear Laravel caches
php artisan optimize:clear
php artisan view:clear
php artisan config:clear

# 4. Emergency fix - create missing files
mkdir -p public/build/assets/css
mkdir -p public/build/assets/js
echo "/* Temporary CSS */" > public/build/assets/css/app-CRz7xcJV.css
echo "/* Temporary JS */" > public/build/assets/js/app-CRz7xcJV.js

# 5. Proper fix - rebuild assets (on local machine)
npm run build
# Then upload public/build/ to production server
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
