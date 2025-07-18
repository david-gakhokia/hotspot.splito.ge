<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class ConfigureCustomHotspot extends Command
{
    protected $signature = 'hotspot:configure-custom 
                          {--timeout=300 : Session timeout in seconds (default: 5 minutes)}
                          {--profile=hsprof1 : Hotspot profile name}
                          {--redirect=https://splito.ge : Redirect URL after login}';

    protected $description = 'Configure MikroTik hotspot with custom HTML templates and redirect';

    public function handle()
    {
        $this->info('🔧 Configuring Custom Hotspot...');
        
        try {
            $service = new MikrotikService();
            $timeout = $this->option('timeout');
            $profile = $this->option('profile');
            $redirectUrl = $this->option('redirect');
            
            $this->info("Profile: {$profile}");
            $this->info("Session Timeout: {$timeout} seconds");
            $this->info("Redirect URL: {$redirectUrl}");
            
            // Step 1: Configure HTML Directory
            $this->info('📁 Setting HTML Directory...');
            $this->configureHtmlDirectory($service, $profile);
            
            // Step 2: Configure Session Timeout
            $this->info('⏰ Setting Session Timeout...');
            $this->configureSessionTimeout($service, $profile, $timeout);
            
            // Step 3: Configure Walled Garden
            $this->info('🌐 Setting up Walled Garden...');
            $this->configureWalledGarden($service, $redirectUrl);
            
            // Step 4: Test configuration
            $this->info('🧪 Testing configuration...');
            $this->testConfiguration($service);
            
            $this->newLine();
            $this->info('✅ Custom Hotspot configuration completed successfully!');
            $this->newLine();
            
            $this->comment('📋 Next Steps:');
            $this->line('1. Upload custom HTML files to MikroTik (if using RouterOS file system)');
            $this->line('2. OR configure HTTP server to serve files from Laravel public/hotspot/');
            $this->line('3. Test hotspot login with a client device');
            $this->line('4. Monitor logs for any issues');
            
            $this->newLine();
            $this->comment('🔗 Important URLs:');
            $this->line("• Hotspot Login: http://192.168.50.1/login");
            $this->line("• Custom Templates: " . url('/hotspot/'));
            $this->line("• Redirect After Login: {$redirectUrl}");
            
        } catch (\Exception $e) {
            $this->error('❌ Configuration failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function configureHtmlDirectory($service, $profile)
    {
        // Option 1: Use Laravel public directory (recommended)
        $htmlDirectory = '/hotspot'; // This will point to Laravel's public/hotspot
        
        // Update hotspot server profile
        $settings = [
            'html-directory' => $htmlDirectory,
            'http-cookie-lifetime' => '00:05:00', // 5 minutes
            'login-by' => 'http-chap,http-pap'
        ];
        
        $response = $service->configureHotspotProfile($profile, $settings);
        
        $this->line("   HTML Directory set to: {$htmlDirectory}");
        
        // Option 2: Upload files to MikroTik (alternative method)
        $this->comment('   Alternative: Upload HTML files directly to MikroTik RouterOS');
    }
    
    private function configureSessionTimeout($service, $profile, $timeout)
    {
        // Convert seconds to HH:MM:SS format
        $hours = floor($timeout / 3600);
        $minutes = floor(($timeout % 3600) / 60);
        $seconds = $timeout % 60;
        $timeFormat = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
        $settings = [
            'session-timeout' => $timeFormat,
            'idle-timeout' => '00:01:00', // 1 minute idle timeout
            'keepalive-timeout' => '00:02:00' // 2 minutes keepalive
        ];
        
        $response = $service->configureHotspotProfile($profile, $settings);
        
        $this->line("   Session timeout set to: {$timeFormat}");
    }
    
    private function configureWalledGarden($service, $redirectUrl)
    {
        // Parse the redirect URL to get domain
        $parsedUrl = parse_url($redirectUrl);
        $domain = $parsedUrl['host'] ?? 'splito.ge';
        
        // Add walled garden rules for the redirect domain
        $rules = [
            [$domain, 'allow', 'Allow redirect domain'],
            ['*.google.com', 'allow', 'Allow Google services'],
            ['fonts.googleapis.com', 'allow', 'Allow Google Fonts'],
            ['*.googleapis.com', 'allow', 'Allow Google APIs'],
            ['*.gstatic.com', 'allow', 'Allow Google static content']
        ];
        
        foreach ($rules as [$host, $action, $comment]) {
            try {
                $response = $service->addWalledGardenRule($host, $action, $comment);
                $this->line("   Added walled garden rule for: {$host}");
            } catch (\Exception $e) {
                $this->warn("   Rule might already exist for {$host}: " . $e->getMessage());
            }
        }
    }
    
    private function testConfiguration($service)
    {
        // Test if we can read hotspot profiles
        $profiles = $service->executeCommand(['/ip/hotspot/profile/print']);
        $this->line('   Found ' . count($profiles) . ' hotspot profile(s)');
        
        // Test walled garden rules
        $gardens = $service->executeCommand(['/ip/hotspot/walled-garden/print']);
        $this->line('   Found ' . count($gardens) . ' walled garden rule(s)');
    }
}
