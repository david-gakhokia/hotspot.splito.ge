#!/bin/bash
# Quick Start Script for Splito Hotspot Deployment

echo "🚀 Splito Hotspot - Quick Start"
echo "==============================="
echo ""

# Check if Laravel Herd is running
echo "🔍 Checking Laravel Herd..."
if curl -s http://hotspot.splito.test > /dev/null; then
    echo "✅ Laravel Herd is running"
else
    echo "❌ Laravel Herd is not accessible"
    echo "   Please start Laravel Herd and ensure hotspot.splito.test is working"
    exit 1
fi

# Test MikroTik connection
echo ""
echo "🔍 Testing MikroTik connection..."
if command -v php > /dev/null; then
    cd "$(dirname "$0")"
    php artisan hotspot:deploy --test
    if [ $? -eq 0 ]; then
        echo "✅ MikroTik connection successful"
    else
        echo "❌ MikroTik connection failed"
        echo "   Please check your .env configuration"
        exit 1
    fi
else
    echo "❌ PHP not found"
    exit 1
fi

# Deployment options
echo ""
echo "🚀 Ready for deployment! Choose an option:"
echo ""
echo "1. 📤 Upload files via PowerShell (Windows)"
echo "   .\mikrotik-upload.ps1"
echo ""
echo "2. 📝 Manual setup via WinBox:"
echo "   - Copy files from public/hotspot/ to RouterOS"
echo "   - Run commands from mikrotik-setup.rsc"
echo ""
echo "3. 🔧 Laravel command (experimental):"
echo "   php artisan hotspot:deploy --force"
echo ""

# Open useful links
echo "📖 Useful links:"
echo "   Local test: http://hotspot.splito.test/test-hotspot"
echo "   Login page: http://hotspot.splito.test/hotspot/login.html"
echo "   Documentation: DEPLOYMENT_GUIDE.md"
echo ""

echo "🎉 Ready to deploy! Choose your preferred method above."
