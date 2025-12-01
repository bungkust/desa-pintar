#!/bin/bash

echo "🚀 Building for production..."

# Set production environment
export APP_ENV=production
export NODE_ENV=production

# Clean old build
echo "🧹 Cleaning old build..."
rm -rf public/build

# Install dependencies (production only)
echo "📦 Installing production dependencies..."
npm ci --only=production

# Build assets
echo "🔨 Building assets..."
npm run build

# Check if build succeeded
if [ $? -eq 0 ]; then
    echo "✅ Build completed successfully!"
    echo "📁 Build files created in public/build/"

    # List build files
    ls -la public/build/assets/css/
    ls -la public/build/assets/js/

    # Show manifest
    echo "📋 Manifest contents:"
    cat public/build/manifest.json

    echo ""
    echo "🎯 Production build ready!"
    echo "   - Upload public/build/ folder to your server"
    echo "   - Clear Laravel caches: php artisan optimize:clear"
    echo "   - Hard refresh browser: Ctrl+F5"
else
    echo "❌ Build failed!"
    exit 1
fi
