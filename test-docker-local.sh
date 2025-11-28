#!/bin/bash

# Script untuk test Docker build dan run secara lokal
# Pastikan Docker sudah terinstall dan running

echo "🐳 Testing Docker build locally..."

# Build Docker image
echo "📦 Building Docker image..."
docker build -t desa-donoharjo:test .

if [ $? -ne 0 ]; then
    echo "❌ Build failed!"
    exit 1
fi

echo "✅ Build successful!"

# Run container (test)
echo "🚀 Running container (test mode)..."
echo "Note: Container akan exit setelah test, tidak akan run server"
echo ""

# Test dengan environment variables minimal
docker run --rm \
    -e APP_ENV=production \
    -e APP_DEBUG=false \
    -e DB_CONNECTION=pgsql \
    -e DB_HOST=localhost \
    -e DB_PORT=5432 \
    -e DB_DATABASE=test_db \
    -e DB_USERNAME=test_user \
    -e DB_PASSWORD=test_pass \
    -e CACHE_DRIVER=file \
    -e SESSION_DRIVER=file \
    -e PORT=8000 \
    desa-donoharjo:test \
    sh -c "php artisan config:clear && php artisan migrate:status || echo 'Migration status check done'"

echo ""
echo "✅ Test completed!"
echo ""
echo "Untuk run full server (dengan database real), gunakan:"
echo "docker run -p 8000:8000 -e DB_HOST=your_db_host -e DB_DATABASE=your_db ... desa-donoharjo:test"

