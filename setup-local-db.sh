#!/bin/bash

echo "🚀 Setting up Local PostgreSQL Database"
echo "========================================"

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

# Check if Homebrew is installed
if ! command -v brew &> /dev/null; then
    echo_error "Homebrew is not installed. Please install it first:"
    echo "  /bin/bash -c \"\$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)\""
    exit 1
fi

# Check if PostgreSQL is installed
if ! command -v psql &> /dev/null; then
    echo_step "Installing PostgreSQL..."
    brew install postgresql

    echo_step "Starting PostgreSQL service..."
    brew services start postgresql

    echo_info "Waiting for PostgreSQL to start..."
    sleep 5
else
    echo_info "PostgreSQL is already installed"
fi

# Check if service is running
if ! brew services list | grep postgresql | grep started &> /dev/null; then
    echo_step "Starting PostgreSQL service..."
    brew services start postgresql
    sleep 3
fi

# Create database
echo_step "Creating local database..."
if createdb desa_donoharjo_local 2>/dev/null; then
    echo_info "Database 'desa_donoharjo_local' created successfully"
else
    echo_warning "Database might already exist, continuing..."
fi

# Test connection
echo_step "Testing local database connection..."
psql -h 127.0.0.1 -p 5432 -U postgres -d desa_donoharjo_local -c "SELECT version();" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Local PostgreSQL connection successful!"
else
    echo_error "Failed to connect to local PostgreSQL"
    echo "Try: brew services restart postgresql"
    exit 1
fi

echo ""
echo_info "🎉 Local Database Setup Complete!"
echo ""
echo "📊 Performance Comparison:"
echo "  Remote (Render/Oregon): ~1800ms connection, ~600ms queries"
echo "  Local (Your machine):   ~50ms connection, ~10ms queries"
echo ""
echo "💡 Next steps:"
echo "  1. Switch to local database: ./switch-db.sh local"
echo "  2. Your app will be 30-50x faster!"
echo "  3. When ready for production: ./switch-db.sh remote"
echo ""
echo "🔄 Database switching commands:"
echo "  ./switch-db.sh local   - Use fast local database"
echo "  ./switch-db.sh remote  - Use slow remote database"
echo "  ./switch-db.sh sync    - Copy local data to remote"
echo ""
echo_warning "⚠️  Remember:"
echo "  - Use LOCAL for development (fast)"
echo "  - Use REMOTE for production sync (slow but shared)"
echo "  - Run './switch-db.sh sync' when ready to deploy"
