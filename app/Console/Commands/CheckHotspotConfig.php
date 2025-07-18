<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use Illuminate\Console\Command;

class CheckHotspotConfig extends Command
{
    protected $signature = 'hotspot:check';
    protected $description = 'Check MikroTik Hotspot configuration';

    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        parent::__construct();
        $this->mikrotikService = $mikrotikService;
    }

    public function handle()
    {
        $this->info('🔍 Checking MikroTik Hotspot Configuration...');
        
        try {
            // Check Hotspot servers
            $this->info('📡 Checking Hotspot Servers:');
            $servers = $this->mikrotikService->getHotspotServers();
            
            if (empty($servers)) {
                $this->error('❌ No Hotspot servers found!');
                $this->info('💡 Creating Hotspot server...');
                
                // Enable hotspot on bridge interface
                $result = $this->mikrotikService->enableHotspotServer('bridge', 'default');
                $this->info('✅ Hotspot server created on bridge interface');
            } else {
                $this->info('✅ Found ' . count($servers) . ' Hotspot server(s)');
                foreach ($servers as $server) {
                    $this->line("   - Interface: " . ($server['interface'] ?? 'unknown'));
                    $this->line("   - Profile: " . ($server['profile'] ?? 'default'));
                    $this->line("   - Disabled: " . ($server['disabled'] ?? 'false'));
                }
            }

            // Check Hotspot profiles
            $this->info('📋 Checking Hotspot Profiles:');
            $profiles = $this->mikrotikService->getHotspotServerProfiles();
            
            if (empty($profiles)) {
                $this->error('❌ No Hotspot profiles found!');
            } else {
                $this->info('✅ Found ' . count($profiles) . ' Hotspot profile(s)');
                foreach ($profiles as $profile) {
                    $this->line("   - Name: " . ($profile['name'] ?? 'unknown'));
                    $this->line("   - Login by: " . ($profile['login-by'] ?? 'not set'));
                    $this->line("   - HTML Directory: " . ($profile['html-directory'] ?? 'hotspot'));
                }
            }

            // Check WiFi interfaces
            $this->info('📶 Checking WiFi Interfaces:');
            $interfaces = $this->mikrotikService->getWirelessInterfaces();
            
            if (empty($interfaces)) {
                $this->error('❌ No wireless interfaces found!');
            } else {
                $this->info('✅ Found ' . count($interfaces) . ' wireless interface(s)');
                foreach ($interfaces as $interface) {
                    $this->line("   - Name: " . ($interface['name'] ?? 'unknown'));
                    $this->line("   - SSID: " . ($interface['ssid'] ?? 'not set'));
                    $this->line("   - Disabled: " . ($interface['disabled'] ?? 'false'));
                    $this->line("   - Mode: " . ($interface['mode'] ?? 'unknown'));
                }
            }

            $this->info('');
            $this->info('🔧 Recommended Actions:');
            $this->info('1. Make sure WiFi interface is in AP mode');
            $this->info('2. Ensure Hotspot server is enabled on bridge interface');
            $this->info('3. Configure Hotspot profile with proper login method');
            $this->info('4. Set login page redirect to: https://hotspot.splito.ge.test/hotspot/login');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error checking Hotspot configuration: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
