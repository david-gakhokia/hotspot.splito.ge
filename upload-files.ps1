# MikroTik Hotspot File Upload Script (PowerShell) - Fixed Version
param(
    [string]$MikrotikIP = "192.168.88.1",
    [string]$Username = "admin", 
    [string]$Password = "Admin1.",
    [switch]$TestOnly
)

Write-Host "🚀 Splito Hotspot File Upload Tool" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

# Check if MikroTik is reachable
Write-Host "🔍 Testing connection to MikroTik router..." -ForegroundColor Yellow
$pingResult = Test-Connection -ComputerName $MikrotikIP -Count 2 -Quiet

if (-not $pingResult) {
    Write-Host "❌ Cannot ping $MikrotikIP" -ForegroundColor Red
    Write-Host "   Please check:" -ForegroundColor Red
    Write-Host "   - Router IP address" -ForegroundColor Red
    Write-Host "   - Network connectivity" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Router is reachable!" -ForegroundColor Green

# Test SSH connection (port 22)
Write-Host "🔍 Testing SSH connection..." -ForegroundColor Yellow
$sshTest = Test-NetConnection -ComputerName $MikrotikIP -Port 22 -InformationLevel Quiet

if ($sshTest) {
    Write-Host "✅ SSH port is open!" -ForegroundColor Green
} else {
    Write-Host "⚠️  SSH port is not accessible" -ForegroundColor Yellow
}

if ($TestOnly) {
    Write-Host "✅ Test completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📝 Next steps:" -ForegroundColor Yellow
    Write-Host "1. Enable FTP service on MikroTik:" -ForegroundColor White
    Write-Host "   /ip service set ftp disabled=no" -ForegroundColor Cyan
    Write-Host "2. Upload files manually via WinBox" -ForegroundColor White
    Write-Host "3. Or use SCP/SFTP client" -ForegroundColor White
    exit 0
}

# Manual file upload instructions
Write-Host ""
Write-Host "📋 Manual Upload Instructions:" -ForegroundColor Yellow
Write-Host ""
Write-Host "Since FTP might not be available, here's how to upload manually:" -ForegroundColor White
Write-Host ""
Write-Host "Method 1: WinBox File Upload" -ForegroundColor Cyan
Write-Host "1. Open WinBox and connect to $MikrotikIP" -ForegroundColor White
Write-Host "2. Go to Files tab" -ForegroundColor White
Write-Host "3. Create directory structure:" -ForegroundColor White
Write-Host "   - hotspot/" -ForegroundColor Gray
Write-Host "   - hotspot/css/" -ForegroundColor Gray
Write-Host "   - hotspot/js/" -ForegroundColor Gray
Write-Host "   - hotspot/images/" -ForegroundColor Gray
Write-Host "4. Upload these files:" -ForegroundColor White
Write-Host ""

# List files to upload
$files = @{
    "public\hotspot\login.html" = "hotspot/login.html"
    "public\hotspot\css\styles.css" = "hotspot/css/styles.css"
    "public\hotspot\js\app.js" = "hotspot/js/app.js"
    "public\hotspot\images\logo.png" = "hotspot/images/logo.png"
    "public\hotspot\images\tower-group-banner.jpg" = "hotspot/images/tower-group-banner.jpg"
}

foreach ($file in $files.GetEnumerator()) {
    $localPath = $file.Key
    $remotePath = $file.Value
    
    if (Test-Path $localPath) {
        Write-Host "   ✅ $localPath → $remotePath" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $localPath (NOT FOUND)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Method 2: SCP Upload (if SSH is enabled)" -ForegroundColor Cyan
Write-Host "Use WinSCP or similar tool to upload files" -ForegroundColor White
Write-Host ""

Write-Host "Method 3: Enable FTP and retry" -ForegroundColor Cyan
Write-Host "1. Connect to MikroTik terminal" -ForegroundColor White
Write-Host "2. Run: /ip service set ftp disabled=no" -ForegroundColor Cyan
Write-Host "3. Run this script again" -ForegroundColor White
Write-Host ""

Write-Host "🔧 After uploading files, configure MikroTik:" -ForegroundColor Yellow
Write-Host ""
Write-Host "/ip hotspot profile" -ForegroundColor Cyan
Write-Host "set [find name=hsprof1] html-directory=hotspot" -ForegroundColor Cyan
Write-Host ""
Write-Host "/ip hotspot walled-garden" -ForegroundColor Cyan
Write-Host 'add dst-host=towergroup.ge comment="Tower Group"' -ForegroundColor Cyan
Write-Host 'add dst-host=splito.ge comment="Splito"' -ForegroundColor Cyan
Write-Host ""
Write-Host "📱 Then test: http://$MikrotikIP/login" -ForegroundColor Green
