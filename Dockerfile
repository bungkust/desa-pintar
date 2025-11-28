FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    postgresql-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first for better Docker layer caching
COPY composer.json composer.lock* ./

# Create .env file from .env.example if .env doesn't exist (for composer scripts)
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || touch .env; fi

# Install PHP dependencies first (before copying all files for better caching)
# Using --no-scripts to avoid running artisan commands before dependencies are fully installed
# Try multiple strategies if first attempt fails
RUN set -eux; \
    composer --version; \
    echo "Checking composer files..."; \
    ls -la composer.* 2>/dev/null || echo "No composer files found"; \
    if [ -f composer.lock ]; then \
        echo "Installing from composer.lock..."; \
        composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist 2>&1 || \
        (echo "=== Install with lock failed, trying with --ignore-platform-reqs ===" && \
         composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist --ignore-platform-reqs 2>&1 || \
         (echo "=== All install attempts failed ===" && exit 1)); \
    else \
        echo "No composer.lock found, running composer update..."; \
        composer update --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist 2>&1 || \
        (echo "=== Update failed, trying with --ignore-platform-reqs ===" && \
         composer update --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist --ignore-platform-reqs 2>&1 || \
         (echo "=== All update attempts failed ===" && exit 1)); \
    fi

# Copy rest of application files (exclude sqlite files via .dockerignore)
COPY . .

# Remove any sqlite files that might have been copied (safety check)
RUN find . -name "*.sqlite" -o -name "*.sqlite3" | xargs rm -f 2>/dev/null || true

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

# Install Node.js dependencies and build assets
RUN npm ci && npm run build

# Run post-install scripts
RUN php artisan package:discover --ansi

# Cache routes and views (config will be cached at runtime after env vars are set)
RUN php artisan route:cache \
    && php artisan view:cache

# Expose port (Render will set PORT env variable)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php artisan route:list || exit 1

# Start command
# Force remove SQLite files and clear all caches
# Set CACHE_DRIVER to file before caching config (to avoid database cache errors)
# Rebuild config cache with environment variables from Render FIRST
# Then run migrations to ensure all tables exist (after config is loaded)
# Create storage link
# Render sets PORT environment variable automatically
CMD rm -rf database/*.sqlite* bootstrap/cache/config.php storage/framework/cache/data/* 2>/dev/null || true && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan optimize:clear && \
    CACHE_STORE=file CACHE_DRIVER=file php artisan config:cache && \
    sleep 2 && \
    php artisan migrate --force && \
    (rm -f public/storage && php artisan storage:link) || true && \
    php artisan serve --host=0.0.0.0 --port=$PORT

