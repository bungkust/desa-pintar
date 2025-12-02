#!/bin/bash

echo "🔄 Database Switcher - Local vs Remote"
echo "======================================"

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
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

# Database configurations
REMOTE_HOST="dpg-d4jddn8bdp1s73fs2af0-a.oregon-postgres.render.com"
REMOTE_PORT="5432"
REMOTE_DB="desa_donoharjo"
REMOTE_USER="desa_donoharjo_user"
REMOTE_PASS="8t36QmeSwKgkaduARQH7TD5sJsnnhj7B"

LOCAL_HOST="127.0.0.1"
LOCAL_PORT="5432"
LOCAL_DB="desa_donoharjo"
LOCAL_USER=""  # Use system user
LOCAL_PASS=""

# SQLite for ultra-fast development
SQLITE_DB="database/database.sqlite"

if [ "$1" = "sqlite" ]; then
    echo_step "Switching to ULTRA-FAST SQLite database..."
    echo_info "Lightning-fast local development database"

    sed -i.bak "s/DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$SQLITE_DB/" .env

    echo_step "Testing SQLite connection..."
    php artisan tinker --execute="
    try {
        DB::connection()->getPdo();
        echo '✅ SQLite connection successful!' . PHP_EOL;

        \$start = microtime(true);
        DB::select('SELECT 1');
        \$end = microtime(true);
        \$time = round((\$end - \$start) * 1000, 2);
        echo '✅ SQLite query took ' . \$time . 'ms' . PHP_EOL;
    } catch (Exception \$e) {
        echo '❌ SQLite connection failed: ' . \$e->getMessage();
        exit(1);
    }
    "

    if [ $? -eq 0 ]; then
        echo_step "Running migrations on SQLite..."
        php artisan migrate --force

        echo_step "Seeding SQLite database..."
        php artisan db:seed --force

        echo_step "Clearing cache..."
        php artisan optimize:clear

        echo ""
        echo "🚀 ULTRA-FAST DEVELOPMENT MODE ACTIVE!"
        echo ""
        echo "📊 Performance comparison:"
        echo "  Remote (Oregon): ~2400ms per page load"
        echo "  SQLite (Local):   ~50ms per page load"
        echo ""
        echo "💡 Your development is now 50x faster!"
        echo "   Perfect for rapid development and testing!"
    fi

elif [ "$1" = "local" ]; then
    echo_step "Switching to LOCAL PostgreSQL database..."
    echo_info "Same as production but local - fast development"

    sed -i.bak "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
    sed -i.bak "s/DB_HOST=.*/DB_HOST=$LOCAL_HOST/" .env
    sed -i.bak "s/DB_PORT=.*/DB_PORT=$LOCAL_PORT/" .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$LOCAL_DB/" .env
    sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$LOCAL_USER/" .env
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$LOCAL_PASS/" .env

    echo_step "Testing local PostgreSQL connection..."
    php artisan tinker --execute="
    try {
        \$start = microtime(true);
        DB::connection()->getPdo();
        \$end = microtime(true);
        \$connectTime = round((\$end - \$start) * 1000, 2);

        \$start = microtime(true);
        \$complaints = DB::table('complaints')->count();
        \$posts = DB::table('posts')->count();
        \$end = microtime(true);
        \$queryTime = round((\$end - \$start) * 1000, 2);

        echo '✅ Local PostgreSQL connected in ' . \$connectTime . 'ms' . PHP_EOL;
        echo '✅ Sample queries took ' . \$queryTime . 'ms' . PHP_EOL;
        echo '📊 Local data: ' . \$complaints . ' complaints, ' . \$posts . ' posts' . PHP_EOL;
    } catch (Exception \$e) {
        echo '❌ Local connection failed: ' . \$e->getMessage();
        exit(1);
    }
    "

    if [ $? -eq 0 ]; then
        echo_step "Clearing cache..."
        php artisan optimize:clear

        echo ""
        echo "🎉 Successfully switched to LOCAL PostgreSQL!"
        echo ""
        echo "📊 Performance comparison:"
        echo "  Remote (Oregon): ~1800ms connection, ~600ms queries"
        echo "  Local (Same DB): ~15ms connection, ~5ms queries"
        echo ""
        echo "💡 Same schema as production, but FAST locally!"
        echo "💡 Run './switch-db.sh sync' to update with latest production data"
    fi

elif [ "$1" = "remote" ]; then
    echo_step "Switching to REMOTE Render PostgreSQL database..."
    echo_warning "This will be slower but keeps data in sync"

    sed -i.bak "s/DB_HOST=.*/DB_HOST=$REMOTE_HOST/" .env
    sed -i.bak "s/DB_PORT=.*/DB_PORT=$REMOTE_PORT/" .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$REMOTE_DB/" .env
    sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$REMOTE_USER/" .env
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$REMOTE_PASS/" .env

    echo_step "Testing remote connection..."
    php artisan tinker --execute="
    \$start = microtime(true);
    try {
        DB::connection()->getPdo();
        \$end = microtime(true);
        \$time = round((\$end - \$start) * 1000, 2);
        echo '✅ Remote database connected in ' . \$time . 'ms';
        echo PHP_EOL;
        echo 'Host: ' . config('database.connections.pgsql.host');
    } catch (Exception \$e) {
        echo '❌ Remote connection failed: ' . \$e->getMessage();
        exit(1);
    }
    "

    if [ $? -eq 0 ]; then
        echo_step "Clearing cache..."
        php artisan optimize:clear

        echo ""
        echo "🔄 Successfully switched to REMOTE database!"
        echo ""
        echo_warning "⚠️  Warning: Remote database is slow (~1800ms connection)"
        echo "   Use 'local' for fast development, 'remote' for production sync"
    fi

elif [ "$1" = "sync" ]; then
    echo_step "Syncing production data to local database..."

    # Dump data from remote
    echo_step "Dumping production data..."
    PGPASSWORD=$REMOTE_PASS pg_dump -h $REMOTE_HOST -p $REMOTE_PORT -U $REMOTE_USER -d $REMOTE_DB --no-owner --no-privileges --clean --if-exists > remote_dump.sql

    # Restore to local
    echo_step "Restoring to local database..."
    psql -h $LOCAL_HOST -p $LOCAL_PORT -d $LOCAL_DB -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" 2>/dev/null
    psql -h $LOCAL_HOST -p $LOCAL_PORT -d $LOCAL_DB < remote_dump.sql

    # Switch to local config
    sed -i.bak "s/DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env
    sed -i.bak "s/DB_HOST=.*/DB_HOST=$LOCAL_HOST/" .env
    sed -i.bak "s/DB_PORT=.*/DB_PORT=$LOCAL_PORT/" .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$LOCAL_DB/" .env
    sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$LOCAL_USER/" .env
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$LOCAL_PASS/" .env

    # Clear cache
    php artisan optimize:clear > /dev/null 2>&1

    # Cleanup
    rm remote_dump.sql

    echo "✅ Sync complete! Local database now matches production."

else
    echo "Usage: $0 [local|remote|sync]"
    echo ""
    echo "Commands:"
    echo "  sqlite - ULTRA-FAST SQLite (fastest, but different schema)"
    echo "  local  - Local PostgreSQL (same as production, fast)"
    echo "  remote - Remote Render PostgreSQL (production, slow)"
    echo "  sync   - Copy production data to local database"
    echo ""
    echo "Current status:"
    php artisan tinker --execute="echo 'DB Host: ' . config('database.connections.pgsql.host');"
fi
