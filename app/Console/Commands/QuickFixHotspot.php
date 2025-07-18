<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use Illuminate\Console\Command;

class QuickFixHotspot extends Command
{
    protected $signature = 'mikrotik:quick-fix';
    protected $description = 'Quick fix for Hotspot login redirection issues';

    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        parent::__construct();
        $this->mikrotikService = $mikrotikService;
    }

    public function handle()
    {
        $this->info('🚀 Quick Fix for Hotspot Login Issues...');
        $this->info('');
        
        try {
            // Step 1: Enable Hotspot Server
            $this->info('1. 📡 Enabling Hotspot Server...');
            $this->enableHotspotServer();
            
            // Step 2: Configure Hotspot Profile
            $this->info('2. 📋 Configuring Hotspot Profile...');
            $this->configureHotspotProfile();
            
            // Step 3: Add test users
            $this->info('3. 👥 Adding test users...');
            $this->addTestUsers();
            
            // Step 4: Show manual commands
            $this->info('4. 🔧 Manual MikroTik Commands Needed:');
            $this->showManualCommands();
            
            $this->info('');
            $this->info('✅ Quick fix completed!');
            $this->info('🔗 Test URL: https://hotspot.splito.ge.test/hotspot/login');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error during quick fix: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function enableHotspotServer()
    {
        try {
            $result = $this->mikrotikService->enableHotspotServer('bridge', 'default');
            $this->line('   ✅ Hotspot server enabled on bridge interface');
        } catch (\Exception $e) {
            $this->error('   ❌ Failed to enable hotspot server: ' . $e->getMessage());
            $this->line('   💡 Try manually: /ip hotspot add name=hotspot interface=bridge profile=default');
        }
    }

    private function configureHotspotProfile()
    {
        try {
            $this->mikrotikService->updateHotspotServerProfile('*1', [
                'name' => 'default',
                'login-by' => 'http-chap,http-pap',
                'html-directory' => 'hotspot',
                'use-radius' => 'no'
            ]);
            $this->line('   ✅ Hotspot profile configured');
        } catch (\Exception $e) {
            $this->error('   ❌ Failed to configure profile: ' . $e->getMessage());
            $this->line('   💡 Try manually: /ip hotspot profile set default login-by=http-chap,http-pap');
        }
    }

    private function addTestUsers()
    {
        $testUsers = [
            ['name' => 'test', 'password' => 'test123'],
            ['name' => 'demo', 'password' => 'demo123'],
            ['name' => 'guest', 'password' => 'guest123']
        ];

        foreach ($testUsers as $user) {
            try {
                $this->mikrotikService->addHotspotUser($user['name'], $user['password'], 'default');
                $this->line("   ✅ Added user: {$user['name']}/{$user['password']}");
            } catch (\Exception $e) {
                $this->line("   💡 User {$user['name']} might already exist");
            }
        }
    }

    private function showManualCommands()
    {
        $this->comment('   Copy these commands to MikroTik Terminal (Winbox/SSH):');
        $this->line('');
        
        $this->line('   # Configure DNS for redirection');
        $this->line('   /ip dns set allow-remote-requests=yes servers=8.8.8.8,8.8.4.4');
        $this->line('   /ip dns static add name="hotspot.local" address=192.168.88.1');
        $this->line('');
        
        $this->line('   # Add NAT rules for HTTP redirection');
        $this->line('   /ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080');
        $this->line('   /ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080');
        $this->line('');
        
        $this->line('   # Allow HTTP traffic to hotspot');
        $this->line('   /ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept');
        $this->line('');
        
        $this->line('   # Optional: Set custom login page (advanced)');
        $this->line('   /ip hotspot walled-garden add dst-host="*.splito.ge" action=allow');
        $this->line('   /ip hotspot walled-garden add dst-host="hotspot.splito.ge.test" action=allow');
        $this->line('');
        
        $this->comment('   After running these commands:');
        $this->line('   1. Connect device to WiFi');
        $this->line('   2. Open browser and go to any website');
        $this->line('   3. Should be redirected to MikroTik login page');
        $this->line('   4. Login with test/test123, demo/demo123, or guest/guest123');
    }
}
