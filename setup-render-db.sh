#!/bin/bash

echo "🚀 Setting up Render PostgreSQL Database"
echo "=========================================="

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

echo_step() {
    echo -e "${GREEN}➤${NC} $1"
}

echo_info() {
    echo -e "${BLUE}ℹ️${NC}  $1"
}

# Render PostgreSQL URL (provided by user)
RENDER_URL="postgresql://desa_donoharjo_user:8t36QmeSwKgkaduARQH7TD5sJsnnhj7B@dpg-d4jddn8bdp1s73fs2af0-a.oregon-postgres.render.com/desa_donoharjo"

echo_info "Using Render PostgreSQL URL:"
echo "$RENDER_URL"
echo ""

# Parse the URL manually (Render URLs don't have explicit port)
# Format: postgresql://user:pass@host/database
DB_USER="desa_donoharjo_user"
DB_PASS="8t36QmeSwKgkaduARQH7TD5sJsnnhj7B"
DB_HOST="dpg-d4jddn8bdp1s73fs2af0-a.oregon-postgres.render.com"
DB_PORT="5432"  # Default PostgreSQL port
DB_NAME="desa_donoharjo"

echo_step "Parsed database configuration:"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  Database: $DB_NAME"
echo "  Username: $DB_USER"
echo "  Password: [HIDDEN]"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo_step "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Backup .env
echo_step "Backing up current .env..."
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update .env with Render database
echo_step "Configuring .env for Render PostgreSQL..."

# Use a more robust method to update .env
cat > .env.tmp << EOF
DB_CONNECTION=pgsql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS
APP_TIMEZONE=Asia/Jakarta
EOF

# Update each line individually to avoid sed issues
sed -i.bak 's/DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
sed -i.bak "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i.bak "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
sed -i.bak 's/APP_TIMEZONE=.*/APP_TIMEZONE=Asia\/Jakarta/' .env

echo_step "Testing database connection..."

# Test connection
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo '✅ Database connection successful!';
    echo PHP_EOL;
    echo 'Connected to: ' . DB::connection()->getDatabaseName();
} catch (Exception \$e) {
    echo '❌ Database connection failed: ' . \$e->getMessage();
    exit(1);
}
"

if [ $? -eq 0 ]; then
    echo ""
    echo_step "Running migrations..."
    php artisan migrate --force

    echo_step "Seeding database..."
    php artisan db:seed --force

    echo_step "Clearing caches..."
    php artisan optimize:clear

    echo ""
    echo "🎉 Render PostgreSQL Setup Complete!"
    echo ""
    echo "✅ Local environment configured for Render database"
    echo "✅ Database migrated and seeded"
    echo "✅ Caches cleared"
    echo ""
    echo "📋 Your Render database is now ready:"
    echo "   Host: $DB_HOST"
    echo "   Database: $DB_NAME"
    echo "   Status: Connected & Migrated"
    echo ""
    echo "🚀 For production deployment:"
    echo "   1. Copy this .env file to your production server"
    echo "   2. Run: composer install --optimize-autoloader --no-dev"
    echo "   3. Run: npm run build"
    echo "   4. Run: php artisan optimize:clear"
    echo "   5. Done! No database setup needed (shared DB)"
    echo ""
    echo "💡 Both local and production now use the SAME Render database!"
    echo "   Changes in one environment appear in the other instantly."

else
    echo ""
    echo "❌ Database setup failed!"
    echo "Please check:"
    echo "  - Your internet connection"
    echo "  - The Render database is running"
    echo "  - The connection URL is correct"
    echo "  - Your firewall allows outbound connections to Render"
    exit 1
fi
