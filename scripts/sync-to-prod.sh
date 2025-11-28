#!/bin/bash

# Script to sync local database to production (schema + data)
# Usage: ./scripts/sync-to-prod.sh [--force] [--tables=table1,table2] [--skip-data]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔄 Syncing local database to production...${NC}"
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    exit 1
fi

# Load .env file
export $(grep -v '^#' .env | xargs)

# Check if production database config exists
if [ -z "$PROD_DB_URL" ]; then
    echo -e "${RED}❌ PROD_DB_URL not set in .env!${NC}"
    echo ""
    echo "Please add:"
    echo "PROD_DB_URL=postgresql://user:pass@host:port/database"
    exit 1
fi

# Display info
echo -e "${YELLOW}📊 Local Database:${NC}"
echo "  Type: SQLite"
echo "  File: ${DB_DATABASE:-database/database.sqlite}"
echo ""
echo -e "${YELLOW}📊 Production Database:${NC}"
if [[ "$PROD_DB_URL" =~ postgresql://([^:]+):([^@]+)@([^/]+)/(.+) ]]; then
    echo "  Host: ${BASH_REMATCH[3]%:*}"
    echo "  Database: ${BASH_REMATCH[4]}"
    echo "  Username: ${BASH_REMATCH[1]}"
fi
echo ""

# Build command options
OPTIONS=""
if [[ "$*" == *"--force"* ]]; then
    OPTIONS="--force"
fi
if [[ "$*" == *"--skip-data"* ]]; then
    OPTIONS="$OPTIONS --skip-data"
fi
if [[ "$*" == *"--skip-migrations"* ]]; then
    OPTIONS="$OPTIONS --skip-migrations"
fi

# Extract --tables option if present
if [[ "$*" =~ --tables=([^ ]+) ]]; then
    OPTIONS="$OPTIONS --tables=${BASH_REMATCH[1]}"
fi

# Run sync command
echo -e "${GREEN}🚀 Starting sync...${NC}"
echo ""

php artisan db:sync-local-to-prod $OPTIONS

EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Sync completed successfully!${NC}"
    echo ""
    echo -e "${BLUE}💡 Next steps:${NC}"
    echo "  1. Verify data in production"
    echo "  2. Check application is working correctly"
    echo "  3. Test all features"
else
    echo ""
    echo -e "${RED}❌ Sync failed with exit code: $EXIT_CODE${NC}"
fi

exit $EXIT_CODE

