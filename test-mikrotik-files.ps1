# MikroTik ფაილების ტესტი და ატვირთვა (SCP/SFTP მეთოდით)
# გაუშვით: powershell -ExecutionPolicy Bypass -File test-mikrotik-files.ps1

param(
    [string]$MikrotikIP = "192.168.88.1",
    [string]$Username = "admin", 
    [string]$Password = "Admin1.",
    [switch]$TestOnly,
    [switch]$UploadFiles
)

Write-Host "🚀 MikroTik Files Testing Tool" -ForegroundColor Green
Write-Host "==============================" -ForegroundColor Green
Write-Host ""

# Function to test different connection methods
function Test-MikrotikConnections {
    Write-Host "🔍 Testing MikroTik connections..." -ForegroundColor Yellow
    Write-Host ""
    
    # Test Ping
    Write-Host "1️⃣ Testing PING connection..." -ForegroundColor Cyan
    $pingResult = Test-Connection -ComputerName $MikrotikIP -Count 2 -Quiet
    if ($pingResult) {
        Write-Host "   ✅ PING successful" -ForegroundColor Green
    } else {
        Write-Host "   ❌ PING failed" -ForegroundColor Red
        return $false
    }
    
    # Test SSH (port 22)
    Write-Host "2️⃣ Testing SSH connection (port 22)..." -ForegroundColor Cyan
    $sshTest = Test-NetConnection -ComputerName $MikrotikIP -Port 22 -InformationLevel Quiet
    if ($sshTest) {
        Write-Host "   ✅ SSH port is open" -ForegroundColor Green
    } else {
        Write-Host "   ❌ SSH port is closed" -ForegroundColor Red
    }
    
    # Test FTP (port 21)
    Write-Host "3️⃣ Testing FTP connection (port 21)..." -ForegroundColor Cyan
    $ftpTest = Test-NetConnection -ComputerName $MikrotikIP -Port 21 -InformationLevel Quiet
    if ($ftpTest) {
        Write-Host "   ✅ FTP port is open" -ForegroundColor Green
    } else {
        Write-Host "   ❌ FTP port is closed" -ForegroundColor Red
    }
    
    # Test HTTP (port 80)
    Write-Host "4️⃣ Testing HTTP connection (port 80)..." -ForegroundColor Cyan
    $httpTest = Test-NetConnection -ComputerName $MikrotikIP -Port 80 -InformationLevel Quiet
    if ($httpTest) {
        Write-Host "   ✅ HTTP port is open" -ForegroundColor Green
    } else {
        Write-Host "   ❌ HTTP port is closed" -ForegroundColor Red
    }
    
    # Test API (port 8728)
    Write-Host "5️⃣ Testing API connection (port 8728)..." -ForegroundColor Cyan
    $apiTest = Test-NetConnection -ComputerName $MikrotikIP -Port 8728 -InformationLevel Quiet
    if ($apiTest) {
        Write-Host "   ✅ API port is open" -ForegroundColor Green
    } else {
        Write-Host "   ❌ API port is closed" -ForegroundColor Red
    }
    
    Write-Host ""
    return $true
}

# Function to test web access
function Test-WebAccess {
    Write-Host "🌐 Testing web access..." -ForegroundColor Yellow
    
    try {
        # Test hotspot login page
        $response = Invoke-WebRequest -Uri "http://$MikrotikIP/login" -TimeoutSec 10 -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            Write-Host "   ✅ Hotspot login page accessible" -ForegroundColor Green
            
            # Check if our custom files are loaded
            if ($response.Content -match "splito.ge") {
                Write-Host "   ✅ Custom Splito branding detected!" -ForegroundColor Green
            } else {
                Write-Host "   ⚠️  Default MikroTik login page" -ForegroundColor Yellow
            }
        }
    } catch {
        Write-Host "   ❌ Web access failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Function to check services via API
function Test-MikrotikServices {
    Write-Host "🔧 Testing MikroTik services via API..." -ForegroundColor Yellow
    
    try {
        # Use PHP artisan to test API
        $apiResult = & php artisan test:mikrotik 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "   ✅ API connection successful" -ForegroundColor Green
            Write-Host "   📊 API Test Results:" -ForegroundColor Cyan
            $apiResult | ForEach-Object { Write-Host "      $_" -ForegroundColor White }
        } else {
            Write-Host "   ❌ API connection failed" -ForegroundColor Red
        }
    } catch {
        Write-Host "   ❌ API test error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Function to upload files using different methods
function Upload-HotspotFiles {
    Write-Host "📤 Attempting to upload hotspot files..." -ForegroundColor Yellow
    Write-Host ""
    
    # Method 1: Try FTP upload
    Write-Host "🔄 Method 1: FTP Upload" -ForegroundColor Cyan
    try {
        $ftpResult = & php artisan hotspot:upload --force 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "   ✅ FTP upload successful!" -ForegroundColor Green
            return $true
        } else {
            Write-Host "   ❌ FTP upload failed" -ForegroundColor Red
            $ftpResult | ForEach-Object { Write-Host "      $_" -ForegroundColor Gray }
        }
    } catch {
        Write-Host "   ❌ FTP upload error: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    # Method 2: Manual file copy instructions
    Write-Host ""
    Write-Host "🔄 Method 2: Manual Upload Instructions" -ForegroundColor Cyan
    Write-Host "   1. Open WinBox and connect to $MikrotikIP" -ForegroundColor White
    Write-Host "   2. Go to Files menu" -ForegroundColor White
    Write-Host "   3. Create 'hotspot' folder if not exists" -ForegroundColor White
    Write-Host "   4. Upload files from: $(Get-Location)\public\hotspot\" -ForegroundColor White
    Write-Host ""
    
    # Method 3: Show configuration commands
    Write-Host "🔄 Method 3: Required MikroTik Commands" -ForegroundColor Cyan
    Write-Host "   Run these commands in MikroTik terminal:" -ForegroundColor White
    Write-Host ""
    Write-Host "   /ip service set ftp disabled=no" -ForegroundColor Yellow
    Write-Host "   /ip hotspot profile set hsprof1 html-directory=hotspot" -ForegroundColor Yellow
    Write-Host "   /ip hotspot walled-garden add dst-host=splito.ge" -ForegroundColor Yellow
    Write-Host "   /ip hotspot walled-garden add dst-host=fonts.googleapis.com" -ForegroundColor Yellow
    Write-Host ""
    
    return $false
}

# Main execution
Write-Host "🎯 Target: $MikrotikIP" -ForegroundColor Cyan
Write-Host "👤 User: $Username" -ForegroundColor Cyan
Write-Host ""

# Test connections
$connectionOk = Test-MikrotikConnections

if ($connectionOk) {
    # Test web access
    Test-WebAccess
    Write-Host ""
    
    # Test API services
    Test-MikrotikServices
    Write-Host ""
    
    if ($UploadFiles -and -not $TestOnly) {
        Upload-HotspotFiles
    }
} else {
    Write-Host "❌ Basic connectivity failed. Please check network settings." -ForegroundColor Red
}

if ($TestOnly) {
    Write-Host ""
    Write-Host "✅ Testing completed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📝 Next steps:" -ForegroundColor Yellow
    Write-Host "1. Enable services on MikroTik if needed" -ForegroundColor White
    Write-Host "2. Run with -UploadFiles to attempt file upload" -ForegroundColor White
    Write-Host "3. Or upload files manually via WinBox" -ForegroundColor White
}
}
