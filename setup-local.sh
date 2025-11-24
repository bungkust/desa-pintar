#!/bin/bash

# Setup script for running the application locally

echo "🚀 Setting up Desa Donoharjo for local development..."

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not found in your PATH."
    echo "Please install PHP or add it to your PATH."
    echo ""
    echo "On macOS, you can install PHP via Homebrew:"
    echo "  brew install php"
    exit 1
fi

echo "✅ PHP found: $(php --version | head -n 1)"

# Check if storage symlink exists
if [ ! -L "public/storage" ]; then
    echo "📦 Creating storage symlink..."
    php artisan storage:link
    echo "✅ Storage symlink created"
else
    echo "✅ Storage symlink already exists"
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found. Please create one from .env.example"
    echo "   cp .env.example .env"
    echo "   php artisan key:generate"
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "To start the development server, run:"
echo "  php artisan serve"
echo ""
echo "Then open your browser to: http://localhost:8000"

