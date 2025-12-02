#!/bin/bash

echo "🔄 Database Sync Script - Local ↔ Production"
echo "=============================================="

# Set environment
if [ "$1" = "prod" ]; then
    echo "📤 Syncing FROM production TO local..."
    DIRECTION="prod-to-local"
elif [ "$1" = "local" ]; then
    echo "📤 Syncing FROM local TO production..."
    DIRECTION="local-to-prod"
else
    echo "❌ Usage: $0 [prod|local]"
    echo "   prod  - Sync FROM production TO local"
    echo "   local - Sync FROM local TO production"
    exit 1
fi

# Database credentials (set these in your environment)
PROD_DB_HOST=${PROD_DB_HOST:-"your-production-host"}
PROD_DB_PORT=${PROD_DB_PORT:-"5432"}
PROD_DB_NAME=${PROD_DB_NAME:-"desa_donoharjo_prod"}
PROD_DB_USER=${PROD_DB_USER:-"your-prod-user"}
PROD_DB_PASS=${PROD_DB_PASS:-"your-prod-password"}

LOCAL_DB_HOST=${LOCAL_DB_HOST:-"127.0.0.1"}
LOCAL_DB_PORT=${LOCAL_DB_PORT:-"5432"}
LOCAL_DB_NAME=${LOCAL_DB_NAME:-"desa_donoharjo_local"}
LOCAL_DB_USER=${LOCAL_DB_USER:-"postgres"}
LOCAL_DB_PASS=${LOCAL_DB_PASS:-"password"}

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo_step() {
    echo -e "${GREEN}➤${NC} $1"
}

echo_warning() {
    echo -e "${YELLOW}⚠️${NC}  $1"
}

echo_error() {
    echo -e "${RED}❌${NC} $1"
}

# Function to run migrations
run_migrations() {
    echo_step "Running database migrations..."
    php artisan migrate --force
    if [ $? -ne 0 ]; then
        echo_error "Migration failed!"
        exit 1
    fi
}

# Function to seed database
seed_database() {
    echo_step "Seeding database..."
    php artisan db:seed --force
    if [ $? -ne 0 ]; then
        echo_warning "Seeding failed, but continuing..."
    fi
}

# Function to backup database
backup_db() {
    local host=$1
    local port=$2
    local dbname=$3
    local user=$4
    local pass=$5
    local backup_file=$6

    echo_step "Backing up database: $dbname"

    PGPASSWORD=$pass pg_dump \
        -h $host \
        -p $port \
        -U $user \
        -d $dbname \
        --no-owner \
        --no-privileges \
        --clean \
        --if-exists \
        --verbose \
        > $backup_file

    if [ $? -ne 0 ]; then
        echo_error "Backup failed!"
        exit 1
    fi

    echo_step "Backup saved to: $backup_file"
}

# Function to restore database
restore_db() {
    local host=$1
    local port=$2
    local dbname=$3
    local user=$4
    local pass=$5
    local backup_file=$6

    echo_step "Restoring database: $dbname from $backup_file"

    PGPASSWORD=$pass psql \
        -h $host \
        -p $port \
        -U $user \
        -d $dbname \
        -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;" \
        2>/dev/null

    PGPASSWORD=$pass psql \
        -h $host \
        -p $port \
        -U $user \
        -d $dbname \
        < $backup_file

    if [ $? -ne 0 ]; then
        echo_error "Restore failed!"
        exit 1
    fi
}

# Main sync logic
case $DIRECTION in
    "prod-to-local")
        echo_step "Starting production → local sync..."

        # Create backup from production
        PROD_BACKUP="/tmp/prod_backup_$(date +%Y%m%d_%H%M%S).sql"
        backup_db $PROD_DB_HOST $PROD_DB_PORT $PROD_DB_NAME $PROD_DB_USER $PROD_DB_PASS $PROD_BACKUP

        # Restore to local
        restore_db $LOCAL_DB_HOST $LOCAL_DB_PORT $LOCAL_DB_NAME $LOCAL_DB_USER $LOCAL_DB_PASS $PROD_BACKUP

        # Run migrations and seed on local
        export DB_HOST=$LOCAL_DB_HOST
        export DB_PORT=$LOCAL_DB_PORT
        export DB_DATABASE=$LOCAL_DB_NAME
        export DB_USERNAME=$LOCAL_DB_USER
        export DB_PASSWORD=$LOCAL_DB_PASS

        run_migrations
        seed_database

        # Cleanup
        rm -f $PROD_BACKUP

        echo_step "✅ Production → Local sync completed!"
        ;;

    "local-to-prod")
        echo_warning "⚠️  WARNING: This will OVERWRITE production database!"
        read -p "Are you sure you want to continue? (yes/no): " confirm
        if [ "$confirm" != "yes" ]; then
            echo "Operation cancelled."
            exit 0
        fi

        echo_step "Starting local → production sync..."

        # Create backup from local
        LOCAL_BACKUP="/tmp/local_backup_$(date +%Y%m%d_%H%M%S).sql"
        backup_db $LOCAL_DB_HOST $LOCAL_DB_PORT $LOCAL_DB_NAME $LOCAL_DB_USER $LOCAL_DB_PASS $LOCAL_BACKUP

        # Backup production first (safety)
        PROD_SAFETY_BACKUP="/tmp/prod_safety_backup_$(date +%Y%m%d_%H%M%S).sql"
        echo_step "Creating safety backup of production..."
        backup_db $PROD_DB_HOST $PROD_DB_PORT $PROD_DB_NAME $PROD_DB_USER $PROD_DB_PASS $PROD_SAFETY_BACKUP

        # Restore to production
        restore_db $PROD_DB_HOST $PROD_DB_PORT $PROD_DB_NAME $PROD_DB_USER $PROD_DB_PASS $LOCAL_BACKUP

        # Cleanup
        rm -f $LOCAL_BACKUP

        echo_step "✅ Local → Production sync completed!"
        echo_warning "Safety backup saved: $PROD_SAFETY_BACKUP"
        ;;
esac

echo_step "🎯 Sync complete! Both databases are now identical."
