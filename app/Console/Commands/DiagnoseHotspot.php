<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use Illuminate\Console\Command;

class DiagnoseHotspot extends Command
{
    protected $signature = 'hotspot:diagnose';
    protected $description = 'Diagnose why Hotspot login page is not showing';

    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        parent::__construct();
        $this->mikrotikService = $mikrotikService;
    }

    public function handle()
    {
        $this->info('🔍 Diagnosing Hotspot Login Redirection...');
        $this->info('');
        
        try {
            // Check 1: Hotspot Servers
            $this->checkHotspotServers();
            $this->info('');
            
            // Check 2: Hotspot Profiles
            $this->checkHotspotProfiles();
            $this->info('');
            
            // Check 3: Active Hotspot Users
            $this->checkActiveUsers();
            $this->info('');
            
            // Check 4: Bridge Interface
            $this->checkBridgeInterface();
            $this->info('');
            
            // Provide recommendations
            $this->provideRecommendations();
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error during diagnosis: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function checkHotspotServers()
    {
        $this->comment('1. 📡 Checking Hotspot Servers:');
        
        $servers = $this->mikrotikService->getHotspotServers();
        
        if (empty($servers)) {
            $this->error('   ❌ NO HOTSPOT SERVERS FOUND!');
            $this->warn('   → This is the main issue - no hotspot server is running');
        } else {
            $this->info('   ✅ Found ' . count($servers) . ' hotspot server(s):');
            foreach ($servers as $server) {
                $interface = $server['interface'] ?? 'unknown';
                $disabled = $server['disabled'] ?? 'false';
                $profile = $server['profile'] ?? 'default';
                
                if ($disabled === 'true') {
                    $this->error("   ❌ Server on {$interface} is DISABLED");
                } else {
                    $this->info("   ✅ Server on {$interface} is ACTIVE (profile: {$profile})");
                }
            }
        }
    }

    private function checkHotspotProfiles()
    {
        $this->comment('2. 📋 Checking Hotspot Profiles:');
        
        $profiles = $this->mikrotikService->getHotspotServerProfiles();
        
        if (empty($profiles)) {
            $this->error('   ❌ NO HOTSPOT PROFILES FOUND!');
        } else {
            $this->info('   ✅ Found ' . count($profiles) . ' profile(s):');
            foreach ($profiles as $profile) {
                $name = $profile['name'] ?? 'unknown';
                $loginBy = $profile['login-by'] ?? 'not set';
                $htmlDir = $profile['html-directory'] ?? 'not set';
                
                $this->line("   - Profile: {$name}");
                $this->line("     Login by: {$loginBy}");
                $this->line("     HTML dir: {$htmlDir}");
                
                if (strpos($loginBy, 'http') === false) {
                    $this->warn("     ⚠️  Login method should include 'http-chap' or 'http-pap'");
                }
            }
        }
    }

    private function checkActiveUsers()
    {
        $this->comment('3. 👥 Checking Active Hotspot Users:');
        
        $activeUsers = $this->mikrotikService->getActiveHotspotUsers();
        
        if (empty($activeUsers)) {
            $this->warn('   ⚠️  No active hotspot users found');
            $this->line('   → This might be normal if no one is connected');
        } else {
            $this->info('   ✅ Found ' . count($activeUsers) . ' active user(s):');
            foreach ($activeUsers as $user) {
                $username = $user['user'] ?? 'unknown';
                $address = $user['address'] ?? 'unknown';
                $this->line("   - User: {$username} (IP: {$address})");
            }
        }
    }

    private function checkBridgeInterface()
    {
        $this->comment('4. 🌉 Checking Bridge Interface:');
        
        // This would require additional methods in MikrotikService
        $this->line('   💡 Manually check in MikroTik:');
        $this->line('   /interface bridge print');
        $this->line('   /interface bridge port print');
        $this->line('   /ip address print');
    }

    private function provideRecommendations()
    {
        $this->info('🔧 TROUBLESHOOTING STEPS:');
        $this->info('');
        
        $this->comment('Step 1: Enable Hotspot Server');
        $this->line('Run: php artisan hotspot:enable bridge');
        $this->line('Or manually: /ip hotspot add name=hotspot interface=bridge profile=default');
        $this->info('');
        
        $this->comment('Step 2: Configure Hotspot Profile');
        $this->line('/ip hotspot profile set default login-by=http-chap,http-pap html-directory=hotspot');
        $this->info('');
        
        $this->comment('Step 3: Configure DNS Redirection');
        $this->line('/ip dns set allow-remote-requests=yes');
        $this->line('/ip dns static add name="hotspot.local" address=192.168.88.1');
        $this->info('');
        
        $this->comment('Step 4: Add Firewall NAT Rules');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080');
        $this->info('');
        
        $this->comment('Step 5: Test with Mobile Device');
        $this->line('1. Connect to WiFi');
        $this->line('2. Open browser and go to any website (e.g., google.com)');
        $this->line('3. Should be redirected to login page');
        $this->info('');
        
        $this->comment('🚀 Quick Fix Command:');
        $this->line('php artisan mikrotik:quick-fix');
    }
}
