#!/bin/bash

echo "🚀 Setting up Shared Database for Local & Production"
echo "====================================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo_step() {
    echo -e "${GREEN}➤${NC} $1"
}

echo_info() {
    echo -e "${BLUE}ℹ️${NC}  $1"
}

echo_warning() {
    echo -e "${YELLOW}⚠️${NC}  $1"
}

echo_error() {
    echo -e "${RED}❌${NC} $1"
}

# Check if .env exists
if [ ! -f .env ]; then
    echo_error ".env file not found!"
    echo "Please copy .env.example to .env first"
    exit 1
fi

echo_step "Configuring shared PostgreSQL database..."

# Ask for database details
echo ""
echo_info "Choose your database setup method:"

echo "1. Render PostgreSQL URL (recommended)"
echo "2. Manual database configuration"
read -p "Enter choice (1 or 2): " choice

if [ "$choice" = "1" ]; then
    echo ""
    echo_info "Enter your Render PostgreSQL URL:"
    echo "Example: postgresql://user:pass@host/database"
    read -p "PostgreSQL URL: " DB_URL

    # Parse the URL
    # Format: postgresql://user:pass@host:port/database
    DB_USER=$(echo $DB_URL | sed 's|postgresql://\([^:]*\):.*|\1|')
    DB_PASS=$(echo $DB_URL | sed 's|postgresql://[^:]*:\([^@]*\)@.*|\1|')
    DB_HOST=$(echo $DB_URL | sed 's|postgresql://[^@]*@\([^:]*\):.*|\1|')
    DB_PORT=$(echo $DB_URL | sed 's|postgresql://[^@]*@[^:]*:\([^/]*\)/.*|\1|')
    DB_NAME=$(echo $DB_URL | sed 's|.*/\([^/]*\)$|\1|')

    echo_step "Parsed database details:"
    echo "  Host: $DB_HOST"
    echo "  Port: $DB_PORT"
    echo "  Database: $DB_NAME"
    echo "  Username: $DB_USER"
    echo "  Password: [HIDDEN]"

elif [ "$choice" = "2" ]; then
    echo ""
    echo_info "Enter your SHARED PostgreSQL database details:"
    echo "(This will be used by both local and production)"

    read -p "Database Host: " DB_HOST
    read -p "Database Port (default: 5432): " DB_PORT
    DB_PORT=${DB_PORT:-5432}
    read -p "Database Name: " DB_NAME
    read -p "Database Username: " DB_USER
    read -s -p "Database Password: " DB_PASS
    echo ""
else
    echo_error "Invalid choice!"
    exit 1
fi

# Update .env file
echo_step "Updating .env configuration..."

# Backup original .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update database settings
sed -i.bak "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
sed -i.bak "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i.bak "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# Set timezone
sed -i.bak "s/APP_TIMEZONE=.*/APP_TIMEZONE=Asia\/Jakarta/" .env

echo_step "Testing database connection..."

# Test connection
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo '✅ Database connection successful!';
} catch (Exception \$e) {
    echo '❌ Database connection failed: ' . \$e->getMessage();
    exit(1);
}
"

if [ $? -eq 0 ]; then
    echo_step "Running migrations..."
    php artisan migrate --force

    echo_step "Seeding database..."
    php artisan db:seed --force

    echo_step "Clearing caches..."
    php artisan optimize:clear

    echo ""
    echo_info "🎉 Setup Complete!"
    echo ""
    echo "Your local and production environments now use the SAME database:"
    echo "  Host: $DB_HOST"
    echo "  Database: $DB_NAME"
    echo "  User: $DB_USER"
    echo ""
    echo_warning "⚠️  IMPORTANT:"
    echo "  - This database is now SHARED between local and production"
    echo "  - Changes in one environment will affect the other"
    echo "  - Make sure to coordinate deployments"
    echo ""
    echo "To sync data between environments:"
    echo "  ./sync-db.sh prod   # Copy production data to local"
    echo "  ./sync-db.sh local  # Copy local data to production"
    echo ""
    echo "For production deployment:"
    echo "  1. Copy this .env to production server"
    echo "  2. Run: php artisan migrate --force"
    echo "  3. Run: php artisan db:seed --force"
    echo "  4. Clear caches: php artisan optimize:clear"

else
    echo_error "Database setup failed!"
    echo "Please check your database credentials and try again."
    exit 1
fi
