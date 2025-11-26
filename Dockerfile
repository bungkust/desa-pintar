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
RUN composer --version && \
    composer validate --no-check-publish && \
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# Copy rest of application files
COPY . .

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Install Node.js dependencies and build assets
RUN npm ci && npm run build

# Run post-install scripts
RUN php artisan package:discover --ansi

# Cache Laravel config, routes, and views
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port (Render will set PORT env variable)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php artisan route:list || exit 1

# Start command
# Render sets PORT environment variable automatically
CMD php artisan serve --host=0.0.0.0 --port=$PORT

