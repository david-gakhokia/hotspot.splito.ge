# MikroTik ფაილების ტესტი - სწრაფი ვერსია
param(
    [string]$MikrotikIP = "192.168.88.1"
)

Write-Host "🚀 MikroTik Files Quick Test" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""

Write-Host "🎯 Target: $MikrotikIP" -ForegroundColor Cyan
Write-Host ""

# Test basic connectivity
Write-Host "1️⃣ Testing PING..." -ForegroundColor Yellow
$ping = Test-Connection -ComputerName $MikrotikIP -Count 2 -Quiet
if ($ping) {
    Write-Host "   ✅ PING successful" -ForegroundColor Green
} else {
    Write-Host "   ❌ PING failed" -ForegroundColor Red
    exit 1
}

# Test ports
$ports = @{
    "SSH" = 22
    "FTP" = 21  
    "HTTP" = 80
    "API" = 8728
}

Write-Host ""
Write-Host "2️⃣ Testing ports..." -ForegroundColor Yellow
foreach ($service in $ports.GetEnumerator()) {
    $portTest = Test-NetConnection -ComputerName $MikrotikIP -Port $service.Value -InformationLevel Quiet
    if ($portTest) {
        Write-Host "   ✅ $($service.Key) (port $($service.Value)) - Open" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $($service.Key) (port $($service.Value)) - Closed" -ForegroundColor Red
    }
}

# Test web access
Write-Host ""
Write-Host "3️⃣ Testing web access..." -ForegroundColor Yellow
try {
    $web = Invoke-WebRequest -Uri "http://$MikrotikIP/login" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "   ✅ Hotspot login page accessible" -ForegroundColor Green
    
    if ($web.Content -match "splito.ge") {
        Write-Host "   🎉 Custom Splito branding detected!" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  Default MikroTik page (custom files not uploaded)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ❌ Web access failed" -ForegroundColor Red
}

# Test API via Laravel
Write-Host ""
Write-Host "4️⃣ Testing Laravel API connection..." -ForegroundColor Yellow
try {
    $apiResult = & php artisan test:mikrotik
    if ($LASTEXITCODE -eq 0) {
        Write-Host "   ✅ Laravel API connection successful" -ForegroundColor Green
    } else {
        Write-Host "   ❌ Laravel API connection failed" -ForegroundColor Red
    }
} catch {
    Write-Host "   ❌ Laravel API test error" -ForegroundColor Red
}

# Show file locations
Write-Host ""
Write-Host "📁 Local hotspot files:" -ForegroundColor Cyan
Get-ChildItem "public\hotspot\" -Recurse | ForEach-Object {
    Write-Host "   📄 $($_.FullName.Replace($PWD, '.'))" -ForegroundColor White
}

Write-Host ""
Write-Host "✅ Test completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Summary & Next Steps:" -ForegroundColor Yellow
Write-Host "1. ✅ Basic connectivity works" -ForegroundColor White
Write-Host "2. 🔧 Enable FTP on MikroTik: /ip service set ftp disabled=no" -ForegroundColor White  
Write-Host "3. 📤 Upload files: php artisan hotspot:upload --force" -ForegroundColor White
Write-Host "4. 🌐 Test at: http://$MikrotikIP/login" -ForegroundColor White
