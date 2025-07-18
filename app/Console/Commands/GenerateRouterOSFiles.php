<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateRouterOSFiles extends Command
{
    protected $signature = 'hotspot:generate-routeros-files';
    protected $description = 'Generate RouterOS compatible HTML files for upload to MikroTik';

    public function handle()
    {
        $this->info('🔧 Generating RouterOS compatible files...');
        
        $outputDir = storage_path('mikrotik_files');
        
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }
        
        // Generate simplified HTML files for RouterOS
        $this->generateLoginHtml($outputDir);
        $this->generateStatusHtml($outputDir);
        $this->generateLogoutHtml($outputDir);
        $this->generateErrorHtml($outputDir);
        $this->generateUploadScript($outputDir);
        
        $this->info('✅ RouterOS files generated successfully!');
        $this->displayInstructions($outputDir);
        
        return 0;
    }
    
    private function generateLoginHtml($dir)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WiFi ავტორიზაცია - Splito</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 400px; width: 100%; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .subtitle { color: #64748b; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 16px; }
        input:focus { border-color: #2563eb; outline: none; }
        .login-btn { width: 100%; padding: 14px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .login-btn:hover { background: #1d4ed8; }
        .info { margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 6px; font-size: 14px; color: #64748b; }
        .error { background: #fef2f2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Splito</div>
            <p class="subtitle">უფასო WiFi ინტერნეტი</p>
        </div>
        
        $(if error)
        <div class="error">$(error)</div>
        $(endif)
        
        <form name="sendin" action="$(link-login-only)" method="post">
            <input type="hidden" name="dst" value="$(link-orig)" />
            <input type="hidden" name="popup" value="true" />
            
            <div class="form-group">
                <label>მომხმარებლის სახელი</label>
                <input type="text" name="username" value="$(username)" required>
            </div>
            
            <div class="form-group">
                <label>პაროლი</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">შესვლა</button>
        </form>
        
        <div class="info">
            ⏱️ სესიის ხანგრძლივობა: 5 წუთი<br>
            🌐 უფასო ინტერნეტი<br>
            🎯 შესვლის შემდეგ გადამისამართება: splito.ge
        </div>
    </div>
    
    <script>
        // Auto redirect after successful login
        if (window.location.search.indexOf("login") === -1) {
            setTimeout(function() {
                window.location.href = "https://splito.ge";
            }, 2000);
        }
    </script>
</body>
</html>';
        
        File::put($dir . '/login.html', $html);
        $this->line('   ✅ login.html generated');
    }
    
    private function generateStatusHtml($dir)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WiFi სტატუსი - Splito</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 400px; width: 100%; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .status-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .timer { font-size: 20px; font-weight: bold; color: #2563eb; text-align: center; padding: 20px; background: #f8fafc; margin: 15px 0; border-radius: 6px; }
        .actions { margin-top: 20px; }
        .btn { display: block; width: 100%; padding: 12px; margin: 10px 0; text-align: center; text-decoration: none; border-radius: 6px; font-weight: 600; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #e2e8f0; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Splito</div>
            <p>კავშირი აქტიურია</p>
        </div>
        
        <div class="timer" id="timer">5:00</div>
        
        <div class="status-item">
            <span>მომხმარებელი:</span>
            <span>$(username)</span>
        </div>
        <div class="status-item">
            <span>IP მისამართი:</span>
            <span>$(ip)</span>
        </div>
        <div class="status-item">
            <span>სესიის დრო:</span>
            <span>$(uptime)</span>
        </div>
        
        <div class="actions">
            <a href="https://splito.ge" class="btn btn-primary">🌐 Splito.ge-ზე გადასვლა</a>
            <a href="$(link-logout)" class="btn btn-secondary">🚪 გამოსვლა</a>
        </div>
    </div>
    
    <script>
        // Countdown timer
        let time = 300;
        function updateTimer() {
            if (time > 0) {
                const min = Math.floor(time / 60);
                const sec = time % 60;
                document.getElementById("timer").textContent = min + ":" + (sec < 10 ? "0" : "") + sec;
                time--;
                setTimeout(updateTimer, 1000);
            }
        }
        updateTimer();
        
        // Auto redirect to splito.ge
        setTimeout(function() {
            window.location.href = "https://splito.ge";
        }, 3000);
    </script>
</body>
</html>';
        
        File::put($dir . '/status.html', $html);
        $this->line('   ✅ status.html generated');
    }
    
    private function generateLogoutHtml($dir)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>გამოსვლა - Splito</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 400px; width: 100%; text-align: center; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .message { color: #059669; margin: 20px 0; padding: 15px; background: #f0f9f5; border-radius: 6px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Splito</div>
        <div class="message">
            ✅ წარმატებით გამოხვედით სისტემიდან<br>
            გმადლობთ გამოყენებისთვის!
        </div>
        <a href="login.html" class="btn">ხელახლა შესვლა</a>
    </div>
</body>
</html>';
        
        File::put($dir . '/logout.html', $html);
        $this->line('   ✅ logout.html generated');
    }
    
    private function generateErrorHtml($dir)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>შეცდომა - Splito</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); max-width: 400px; width: 100%; text-align: center; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; margin-bottom: 10px; }
        .error { color: #dc2626; margin: 20px 0; padding: 15px; background: #fef2f2; border-radius: 6px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Splito</div>
        <div class="error">
            ⚠️ $(error)<br>
            გთხოვთ, სცადოთ ხელახლა
        </div>
        <a href="login.html" class="btn">ხელახლა ცდა</a>
    </div>
</body>
</html>';
        
        File::put($dir . '/error.html', $html);
        $this->line('   ✅ error.html generated');
    }
    
    private function generateUploadScript($dir)
    {
        $script = '# MikroTik RouterOS Script for Custom Hotspot
# Upload these files to MikroTik and run this script

# First upload all HTML files via Files section, then run:
/ip hotspot profile set hsprof1 html-directory=hotspot
/ip hotspot profile set hsprof1 session-timeout=00:05:00
/ip hotspot profile set hsprof1 http-cookie-lifetime=00:05:00
/ip hotspot profile set hsprof1 login-by=http-chap,http-pap

# Add walled garden rules
/ip hotspot walled-garden add dst-host=splito.ge action=allow comment="Allow Splito redirect"
/ip hotspot walled-garden add dst-host=*.splito.ge action=allow comment="Allow Splito subdomains"

# Print configuration to verify
/ip hotspot profile print detail
/ip hotspot walled-garden print
';
        
        File::put($dir . '/upload_script.rsc', $script);
        $this->line('   ✅ upload_script.rsc generated');
    }
    
    private function displayInstructions($dir)
    {
        $this->newLine();
        $this->comment('📁 Files generated in: ' . $dir);
        $this->line('   • login.html');
        $this->line('   • status.html');
        $this->line('   • logout.html');
        $this->line('   • error.html');
        $this->line('   • upload_script.rsc');
        
        $this->newLine();
        $this->comment('📤 Upload Instructions:');
        $this->line('1. Open WinBox → Files');
        $this->line('2. Create folder "hotspot"');
        $this->line('3. Upload all .html files to "hotspot" folder');
        $this->line('4. Open Terminal and paste upload_script.rsc content');
        $this->line('5. Test with mobile device');
        
        $this->newLine();
        $this->comment('🚀 Alternative Quick Method:');
        $this->line('Copy files to your web server and use:');
        $this->line('/ip hotspot profile set hsprof1 html-directory-override="http://your-server.com/hotspot"');
    }
}
