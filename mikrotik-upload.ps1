# MikroTik Hotspot File Upload Script (PowerShell)
# გაუშვით: powershell -ExecutionPolicy Bypass -File mikrotik-upload.ps1

param(
    [string]$MikrotikIP = "192.168.88.1",
    [string]$Username = "admin", 
    [string]$Password = "Admin1.",
    [switch]$TestOnly
)

Write-Host "🚀 Splito Hotspot File Upload Tool" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
Write-Host ""

# Test connection first
Write-Host "🔍 Testing connection to MikroTik router..." -ForegroundColor Yellow
$testConnection = Test-NetConnection -ComputerName $MikrotikIP -Port 21 -InformationLevel Quiet

if (-not $testConnection) {
    Write-Host "❌ Cannot connect to $MikrotikIP. Please check:" -ForegroundColor Red
    Write-Host "   - Router IP address" -ForegroundColor Red
    Write-Host "   - FTP service is enabled on MikroTik" -ForegroundColor Red
    Write-Host "   - Firewall settings" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Connection successful!" -ForegroundColor Green

if ($TestOnly) {
    Write-Host "✅ Test completed successfully!" -ForegroundColor Green
    exit 0
}

# Files to upload
$files = @{
    "public\hotspot\login.html" = "hotspot/login.html"
    "public\hotspot\css\styles.css" = "hotspot/css/styles.css"
    "public\hotspot\js\app.js" = "hotspot/js/app.js"
    "public\hotspot\images\logo.png" = "hotspot/images/logo.png"
    "public\hotspot\images\tower-group-banner.jpg" = "hotspot/images/tower-group-banner.jpg"
}

Write-Host ""
Write-Host "📤 Uploading files via FTP..." -ForegroundColor Yellow

foreach ($file in $files.GetEnumerator()) {
    $localPath = $file.Key
    $remotePath = $file.Value
    
    if (Test-Path $localPath) {
        Write-Host "   📁 Uploading: $localPath -> $remotePath" -ForegroundColor Cyan
        
        # FTP Upload using WebClient
        try {
            $ftp = [System.Net.WebRequest]::Create("ftp://$MikrotikIP/$remotePath")
            $ftp.Credentials = New-Object System.Net.NetworkCredential($Username, $Password)
            $ftp.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
            
            $fileContent = [System.IO.File]::ReadAllBytes($localPath)
            $ftp.ContentLength = $fileContent.Length
            
            $requestStream = $ftp.GetRequestStream()
            $requestStream.Write($fileContent, 0, $fileContent.Length)
            $requestStream.Close()
            
            $response = $ftp.GetResponse()
            Write-Host "   ✅ Success: $($response.StatusDescription)" -ForegroundColor Green
            $response.Close()
        }
        catch {
            Write-Host "   ❌ Failed: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    else {
        Write-Host "   ⚠️  File not found: $localPath" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "🎉 Upload completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Next steps:" -ForegroundColor Yellow
Write-Host "1. Connect to MikroTik via WinBox or SSH" -ForegroundColor White
Write-Host "2. Run the following commands:" -ForegroundColor White
Write-Host ""
Write-Host "/ip hotspot profile" -ForegroundColor Cyan
Write-Host "set [find name=hsprof1] html-directory=hotspot" -ForegroundColor Cyan
Write-Host ""
Write-Host "/ip hotspot walled-garden" -ForegroundColor Cyan
Write-Host 'add dst-host=towergroup.ge comment="Tower Group Ad"' -ForegroundColor Cyan
Write-Host 'add dst-host=splito.ge comment="Splito Main Site"' -ForegroundColor Cyan
Write-Host 'add dst-host=fonts.googleapis.com comment="Google Fonts"' -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Test hotspot: http://$MikrotikIP/login" -ForegroundColor White
