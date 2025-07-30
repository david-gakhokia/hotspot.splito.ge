Write-Host "🚀 Splito Hotspot Setup Guide" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""

$MikrotikIP = "192.168.88.1"

Write-Host "🔍 Testing connection to MikroTik..." -ForegroundColor Yellow
$pingResult = Test-Connection -ComputerName $MikrotikIP -Count 2 -Quiet

if ($pingResult) {
    Write-Host "✅ MikroTik router is reachable!" -ForegroundColor Green
} else {
    Write-Host "❌ Cannot reach MikroTik router at $MikrotikIP" -ForegroundColor Red
    Write-Host "   Please check network connection" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "📋 Manual File Upload Steps:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Open WinBox and connect to $MikrotikIP" -ForegroundColor Cyan
Write-Host "2. Go to Files tab" -ForegroundColor Cyan
Write-Host "3. Create folder structure:" -ForegroundColor Cyan
Write-Host "   hotspot/" -ForegroundColor White
Write-Host "   hotspot/css/" -ForegroundColor White
Write-Host "   hotspot/js/" -ForegroundColor White
Write-Host "   hotspot/images/" -ForegroundColor White
Write-Host ""
Write-Host "4. Upload these files from your project:" -ForegroundColor Cyan

$files = @(
    "public\hotspot\login.html → hotspot/login.html",
    "public\hotspot\css\styles.css → hotspot/css/styles.css", 
    "public\hotspot\js\app.js → hotspot/js/app.js",
    "public\hotspot\images\logo.png → hotspot/images/logo.png",
    "public\hotspot\images\tower-group-banner.jpg → hotspot/images/tower-group-banner.jpg"
)

foreach ($file in $files) {
    Write-Host "   📁 $file" -ForegroundColor White
}

Write-Host ""
Write-Host "5. Configure MikroTik (Terminal commands):" -ForegroundColor Cyan
Write-Host ""
Write-Host "/ip hotspot profile" -ForegroundColor Yellow
Write-Host "set [find name=hsprof1] html-directory=hotspot" -ForegroundColor Yellow
Write-Host ""
Write-Host "/ip hotspot walled-garden" -ForegroundColor Yellow
Write-Host "add dst-host=towergroup.ge comment=""Tower Group""" -ForegroundColor Yellow
Write-Host "add dst-host=splito.ge comment=""Splito""" -ForegroundColor Yellow
Write-Host ""
Write-Host "6. Test the result:" -ForegroundColor Cyan
Write-Host "   http://$MikrotikIP/login" -ForegroundColor Green
Write-Host ""
Write-Host "🎉 That's it! Your custom hotspot page should work!" -ForegroundColor Green
