# Quick Start Script for Splito Hotspot Deployment (PowerShell)
# Usage: .\quick-start.ps1

Write-Host "🚀 Splito Hotspot - Quick Start" -ForegroundColor Green
Write-Host "===============================" -ForegroundColor Green
Write-Host ""

# Check if Laravel Herd is running
Write-Host "🔍 Checking Laravel Herd..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://hotspot.splito.test" -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
    Write-Host "✅ Laravel Herd is running" -ForegroundColor Green
}
catch {
    Write-Host "❌ Laravel Herd is not accessible" -ForegroundColor Red
    Write-Host "   Please start Laravel Herd and ensure hotspot.splito.test is working" -ForegroundColor Red
    exit 1
}

# Test MikroTik connection
Write-Host ""
Write-Host "🔍 Testing MikroTik connection..." -ForegroundColor Yellow

try {
    $testResult = & php artisan hotspot:deploy --test 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ MikroTik connection successful" -ForegroundColor Green
    }
    else {
        Write-Host "❌ MikroTik connection failed" -ForegroundColor Red
        Write-Host "   Please check your .env configuration:" -ForegroundColor Red
        Write-Host "   MIKROTIK_HOST, MIKROTIK_USER, MIKROTIK_PASS" -ForegroundColor Red
        exit 1
    }
}
catch {
    Write-Host "❌ PHP or Laravel command failed" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Deployment options
Write-Host ""
Write-Host "🚀 Ready for deployment! Choose an option:" -ForegroundColor Green
Write-Host ""

Write-Host "1. 📤 Upload files via PowerShell (Recommended)" -ForegroundColor Cyan
Write-Host "   .\mikrotik-upload.ps1" -ForegroundColor White
Write-Host ""

Write-Host "2. 📝 Manual setup via WinBox:" -ForegroundColor Cyan
Write-Host "   - Copy files from public/hotspot/ to RouterOS Files" -ForegroundColor White
Write-Host "   - Run commands from mikrotik-setup.rsc in Terminal" -ForegroundColor White
Write-Host ""

Write-Host "3. 🔧 Laravel command (experimental):" -ForegroundColor Cyan
Write-Host "   php artisan hotspot:deploy --force" -ForegroundColor White
Write-Host ""

# Quick deployment option
Write-Host "💡 Quick Deploy:" -ForegroundColor Yellow
$deploy = Read-Host "Do you want to upload files now? (y/N)"

if ($deploy -eq "y" -or $deploy -eq "Y") {
    Write-Host ""
    Write-Host "🚀 Starting file upload..." -ForegroundColor Green
    
    if (Test-Path ".\mikrotik-upload.ps1") {
        & .\mikrotik-upload.ps1
    }
    else {
        Write-Host "❌ mikrotik-upload.ps1 not found" -ForegroundColor Red
        Write-Host "   Please run the upload script manually" -ForegroundColor Red
    }
}

# Open useful links
Write-Host ""
Write-Host "📖 Useful links:" -ForegroundColor Yellow
Write-Host "   Local test: http://hotspot.splito.test/test-hotspot" -ForegroundColor White
Write-Host "   Login page: http://hotspot.splito.test/hotspot/login.html" -ForegroundColor White  
Write-Host "   MikroTik login: http://192.168.88.1/login (after deployment)" -ForegroundColor White
Write-Host "   Documentation: DEPLOYMENT_GUIDE.md" -ForegroundColor White
Write-Host ""

Write-Host "📋 Next steps after file upload:" -ForegroundColor Yellow
Write-Host "1. Connect to MikroTik via WinBox or SSH" -ForegroundColor White
Write-Host "2. Run commands from mikrotik-setup.rsc" -ForegroundColor White
Write-Host "3. Test hotspot login page" -ForegroundColor White
Write-Host ""

Write-Host "🎉 Ready to deploy! Happy coding! 🚀" -ForegroundColor Green
