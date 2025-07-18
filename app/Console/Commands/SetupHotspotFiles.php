<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class SetupHotspotFiles extends Command
{
    protected $signature = 'hotspot:setup-files 
                          {--profile=hsprof1 : Hotspot profile name}
                          {--method=override : Method: override or upload}';

    protected $description = 'Setup custom hotspot HTML files in MikroTik';

    public function handle()
    {
        $this->info('🔧 Setting up Hotspot HTML Files...');
        
        $profile = $this->option('profile');
        $method = $this->option('method');
        
        try {
            $service = new MikrotikService();
            
            if ($method === 'override') {
                $this->setupHtmlDirectoryOverride($service, $profile);
            } else {
                $this->uploadFilesToMikrotik($service);
            }
            
            $this->info('✅ Hotspot files setup completed!');
            $this->displayTestInstructions();
            
        } catch (\Exception $e) {
            $this->error('❌ Setup failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function setupHtmlDirectoryOverride($service, $profile)
    {
        $this->info('📁 Setting up HTML Directory Override...');
        
        // Method 1: Use html-directory-override to point to Laravel
        $laravelUrl = 'http://192.168.50.251:80/hotspot';
        
        $settings = [
            'html-directory-override' => $laravelUrl
        ];
        
        $response = $service->configureHotspotProfile($profile, $settings);
        
        $this->line("   HTML Directory Override set to: {$laravelUrl}");
        $this->comment('   MikroTik will fetch templates from Laravel server');
    }
    
    private function uploadFilesToMikrotik($service)
    {
        $this->info('📤 Uploading files to MikroTik...');
        
        // This would require file upload functionality
        $this->comment('   File upload method requires additional implementation');
        $this->comment('   For now, use the override method');
        
        // Alternative: Generate RouterOS script
        $this->generateRouterOSScript();
    }
    
    private function generateRouterOSScript()
    {
        $this->info('📜 Generating RouterOS script...');
        
        $script = '/ip hotspot profile set hsprof1 html-directory-override="http://192.168.50.251:80/hotspot"' . "\n";
        $script .= '/ip hotspot profile set hsprof1 http-cookie-lifetime=00:05:00' . "\n";
        $script .= '/ip hotspot profile set hsprof1 session-timeout=00:05:00' . "\n";
        
        file_put_contents(storage_path('mikrotik_hotspot_config.rsc'), $script);
        
        $this->line('   Script saved to: storage/mikrotik_hotspot_config.rsc');
        $this->comment('   You can copy-paste this script directly in MikroTik terminal');
    }
    
    private function displayTestInstructions()
    {
        $this->newLine();
        $this->comment('🧪 Testing Instructions:');
        $this->line('1. Connect a device to your WiFi hotspot');
        $this->line('2. Try to browse any website');
        $this->line('3. You should see the custom Splito login page');
        $this->line('4. Login with test credentials');
        $this->line('5. You should be redirected to https://splito.ge');
        
        $this->newLine();
        $this->comment('🔍 If still seeing default page:');
        $this->line('• Check MikroTik profile configuration');
        $this->line('• Verify Laravel server is accessible from MikroTik');
        $this->line('• Clear browser cache on test device');
        
        $this->newLine();
        $this->comment('📋 Quick MikroTik Commands:');
        $this->line('/ip hotspot profile print');
        $this->line('/ip hotspot profile set hsprof1 html-directory-override="https://hotspot.splito.ge.test/hotspot"');
    }
}
