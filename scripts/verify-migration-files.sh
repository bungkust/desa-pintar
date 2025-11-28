#!/bin/bash

# Script to verify migration files and database setup
# Usage: ./scripts/verify-migration-files.sh

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Verifying migration files and setup...${NC}"
echo ""

ERRORS=0
WARNINGS=0

# Check if .env exists
echo -e "${YELLOW}📄 Checking .env file...${NC}"
if [ -f .env ]; then
    echo -e "${GREEN}  ✓ .env file exists${NC}"
else
    echo -e "${RED}  ❌ .env file not found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check migration files
echo ""
echo -e "${YELLOW}📦 Checking migration files...${NC}"
MIGRATION_COUNT=$(find database/migrations -name "*.php" 2>/dev/null | wc -l | tr -d ' ')
if [ "$MIGRATION_COUNT" -gt 0 ]; then
    echo -e "${GREEN}  ✓ Found $MIGRATION_COUNT migration files${NC}"
    
    # List migration files
    echo ""
    echo -e "${BLUE}  Migration files:${NC}"
    find database/migrations -name "*.php" -type f | sort | while read file; do
        basename "$file" | sed 's/^/    - /'
    done
else
    echo -e "${RED}  ❌ No migration files found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check production database config
echo ""
echo -e "${YELLOW}🔧 Checking production database configuration...${NC}"
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
    
    if [ -z "$PROD_DB_HOST" ]; then
        echo -e "${RED}  ❌ PROD_DB_HOST not set${NC}"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}  ✓ PROD_DB_HOST: $PROD_DB_HOST${NC}"
    fi
    
    if [ -z "$PROD_DB_DATABASE" ]; then
        echo -e "${RED}  ❌ PROD_DB_DATABASE not set${NC}"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}  ✓ PROD_DB_DATABASE: $PROD_DB_DATABASE${NC}"
    fi
    
    if [ -z "$PROD_DB_USERNAME" ]; then
        echo -e "${RED}  ❌ PROD_DB_USERNAME not set${NC}"
        ERRORS=$((ERRORS + 1))
    else
        echo -e "${GREEN}  ✓ PROD_DB_USERNAME: $PROD_DB_USERNAME${NC}"
    fi
    
    if [ -z "$PROD_DB_PASSWORD" ]; then
        echo -e "${YELLOW}  ⚠️  PROD_DB_PASSWORD not set${NC}"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "${GREEN}  ✓ PROD_DB_PASSWORD: [hidden]${NC}"
    fi
    
    if [ -z "$PROD_DB_PORT" ]; then
        echo -e "${YELLOW}  ⚠️  PROD_DB_PORT not set (will use default: 5432)${NC}"
        WARNINGS=$((WARNINGS + 1))
    else
        echo -e "${GREEN}  ✓ PROD_DB_PORT: $PROD_DB_PORT${NC}"
    fi
else
    echo -e "${RED}  ❌ Cannot check config (no .env file)${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check if psql is available (for connection test)
echo ""
echo -e "${YELLOW}🛠️  Checking tools...${NC}"
if command -v psql &> /dev/null; then
    echo -e "${GREEN}  ✓ psql is available${NC}"
else
    echo -e "${YELLOW}  ⚠️  psql not found (connection test will be skipped)${NC}"
    WARNINGS=$((WARNINGS + 1))
fi

# Check if php artisan migrate:prod command exists
echo ""
echo -e "${YELLOW}🔧 Checking Laravel commands...${NC}"
if php artisan list | grep -q "migrate:prod"; then
    echo -e "${GREEN}  ✓ migrate:prod command exists${NC}"
else
    echo -e "${RED}  ❌ migrate:prod command not found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Check migration script
echo ""
echo -e "${YELLOW}📜 Checking migration script...${NC}"
if [ -f scripts/migrate-to-prod.sh ]; then
    if [ -x scripts/migrate-to-prod.sh ]; then
        echo -e "${GREEN}  ✓ scripts/migrate-to-prod.sh exists and is executable${NC}"
    else
        echo -e "${YELLOW}  ⚠️  scripts/migrate-to-prod.sh exists but not executable${NC}"
        echo -e "${BLUE}    Run: chmod +x scripts/migrate-to-prod.sh${NC}"
        WARNINGS=$((WARNINGS + 1))
    fi
else
    echo -e "${RED}  ❌ scripts/migrate-to-prod.sh not found!${NC}"
    ERRORS=$((ERRORS + 1))
fi

# Summary
echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ All checks passed! Ready to migrate.${NC}"
    echo ""
    echo "To run migration:"
    echo "  ./scripts/migrate-to-prod.sh --force"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  $WARNINGS warning(s) found, but ready to proceed${NC}"
    exit 0
else
    echo -e "${RED}❌ $ERRORS error(s) found, please fix before migrating${NC}"
    if [ $WARNINGS -gt 0 ]; then
        echo -e "${YELLOW}   and $WARNINGS warning(s)${NC}"
    fi
    exit 1
fi

