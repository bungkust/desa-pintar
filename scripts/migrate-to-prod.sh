#!/bin/bash

# Script to run migrations on production database from local environment
# Usage: ./scripts/migrate-to-prod.sh [--force] [--pretend] [--step]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}🔄 Running migrations on production database...${NC}"
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    exit 1
fi

# Load .env file
export $(grep -v '^#' .env | xargs)

# Check if production database config exists
# Support both PROD_DB_URL (preferred) and individual config
if [ -n "$PROD_DB_URL" ]; then
    echo -e "${GREEN}✓ Using PROD_DB_URL${NC}"
    # Extract info from URL for display
    if [[ "$PROD_DB_URL" =~ postgresql://([^:]+):([^@]+)@([^/]+)/(.+) ]]; then
        PROD_DB_USERNAME="${BASH_REMATCH[1]}"
        PROD_DB_HOST="${BASH_REMATCH[3]%:*}"  # Remove port if present
        PROD_DB_DATABASE="${BASH_REMATCH[4]}"
    fi
elif [ -z "$PROD_DB_HOST" ] || [ -z "$PROD_DB_DATABASE" ]; then
    echo -e "${RED}❌ Production database configuration not found!${NC}"
    echo ""
    echo "Please add one of these to your .env file:"
    echo ""
    echo "Option 1 (Recommended - External Database URL):"
    echo "PROD_DB_URL=postgresql://user:pass@host:port/database"
    echo ""
    echo "Option 2 (Individual config):"
    echo "PROD_DB_HOST=your-production-db-host"
    echo "PROD_DB_PORT=5432"
    echo "PROD_DB_DATABASE=your-production-db-name"
    echo "PROD_DB_USERNAME=your-production-db-user"
    echo "PROD_DB_PASSWORD=your-production-db-password"
    exit 1
fi

# Display production database info
echo -e "${YELLOW}📊 Production Database:${NC}"
if [ -n "$PROD_DB_URL" ]; then
    echo "  URL: [configured]"
    echo "  Host: $PROD_DB_HOST"
    echo "  Database: $PROD_DB_DATABASE"
    echo "  Username: $PROD_DB_USERNAME"
else
    echo "  Host: $PROD_DB_HOST"
    echo "  Database: $PROD_DB_DATABASE"
    echo "  Username: $PROD_DB_USERNAME"
fi
echo ""

# Test connection (optional, only if psql is available)
if command -v psql &> /dev/null; then
    echo -e "${YELLOW}🔌 Testing connection to production database...${NC}"
    if PGPASSWORD="$PROD_DB_PASSWORD" psql -h "$PROD_DB_HOST" -p "${PROD_DB_PORT:-5432}" -U "$PROD_DB_USERNAME" -d "$PROD_DB_DATABASE" -c "SELECT 1;" > /dev/null 2>&1; then
        echo -e "${GREEN}  ✓ Connection successful!${NC}"
    else
        echo -e "${YELLOW}  ⚠️  Connection test failed (will try anyway)${NC}"
        echo ""
        echo "Note: Render PostgreSQL usually only allows connections from Render services."
        echo "If migration fails, run it directly in production instead."
    fi
    echo ""
else
    echo -e "${YELLOW}⚠️  psql not found, skipping connection test${NC}"
    echo ""
fi

# Run migration
echo -e "${YELLOW}📤 Running migrations...${NC}"
echo ""

# Build command options
OPTIONS=""
if [[ "$*" == *"--force"* ]]; then
    OPTIONS="--force"
fi
if [[ "$*" == *"--pretend"* ]]; then
    OPTIONS="$OPTIONS --pretend"
fi
if [[ "$*" == *"--step"* ]]; then
    OPTIONS="$OPTIONS --step"
fi

# Run Laravel migration command
php artisan migrate:prod $OPTIONS

EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Migrations completed successfully!${NC}"
else
    echo ""
    echo -e "${RED}❌ Migration failed with exit code: $EXIT_CODE${NC}"
fi

exit $EXIT_CODE

