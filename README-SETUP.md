# Setup Instructions for Desa Donoharjo

## Current Status
✅ PHP 8.5.0 installed via Homebrew
✅ Routes and views created
⚠️ Laravel core files (artisan, composer.json, vendor) are missing

## To Run This Application Locally

### Option 1: If this is part of a larger Laravel project
If this directory is part of a parent Laravel project, navigate to the parent directory and run:
```bash
cd /path/to/parent/laravel/project
php artisan serve
```

### Option 2: Initialize as a new Laravel project
If you need to set up this as a complete Laravel project:

1. **Install Composer** (if not already installed):
   ```bash
   brew install composer
   ```

2. **Create a new Laravel project** (if starting fresh):
   ```bash
   composer create-project laravel/laravel temp-project
   # Then copy your app/, routes/, resources/, database/ folders to the new project
   ```

3. **Or install dependencies** (if composer.json exists):
   ```bash
   composer install
   ```

### Option 3: Quick Test (if Laravel is set up elsewhere)
If you have Laravel installed globally or in a parent directory:

```bash
# Add PHP to your PATH permanently (add to ~/.zshrc)
echo 'export PATH="/opt/homebrew/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Then navigate to your Laravel project root and run:
php artisan serve
```

## Required Laravel Files
A complete Laravel project needs:
- `artisan` - Laravel command-line tool
- `composer.json` - Dependency management
- `vendor/` - Installed dependencies
- `.env` - Environment configuration
- `bootstrap/app.php` - Application bootstrap
- `public/index.php` - Entry point

## Next Steps
1. Check if there's a parent directory with these files
2. Or initialize this as a complete Laravel project
3. Run `php artisan storage:link` to create symlink for images
4. Run `php artisan serve` to start the development server
5. Access at `http://localhost:8000`

## Current PHP Installation
✅ PHP 8.5.0 is installed at: `/opt/homebrew/bin/php`
✅ Add to PATH: `export PATH="/opt/homebrew/bin:$PATH"`

