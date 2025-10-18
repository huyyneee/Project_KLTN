#!/bin/bash

# Optimized PHP Server Startup Script
echo "🚀 Starting Optimized PHP API Server..."

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed or not in PATH"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "📋 PHP Version: $PHP_VERSION"

# Check if OPcache is available
if php -m | grep -q "Zend OPcache"; then
    echo "✅ OPcache is available"
else
    echo "⚠️  OPcache is not available (consider installing for better performance)"
fi

# Create logs directory if it doesn't exist
mkdir -p logs

# Start server with optimized settings
echo "🌐 Starting server on http://localhost:8000"
echo "📁 Document root: $(pwd)/public"
echo "⚙️  Using optimized php.ini"
echo ""
echo "Press Ctrl+C to stop the server"
echo "================================"

# Start the server with custom php.ini
php -S localhost:8000 -t public -c php.ini

