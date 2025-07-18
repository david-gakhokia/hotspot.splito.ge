<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class EnableHotspotService extends Command
{
    protected $signature = 'hotspot:enable {interface=bridge}';
    protected $description = 'Enable MikroTik Hotspot service on specified interface';

    public function handle()
    {
        $interface = $this->argument('interface');
        
        $this->info("🔥 Enabling Hotspot service on interface: {$interface}");
        
        try {
            $mikrotik = new MikrotikService();
            
            // Check if hotspot is already enabled
            $this->info('📋 Checking existing hotspot servers...');
            $servers = $mikrotik->getHotspotServers();
            
            foreach ($servers as $server) {
                if (isset($server['interface']) && $server['interface'] === $interface) {
                    $this->warn("⚠️  Hotspot already enabled on interface {$interface}");
                    $this->info("Server ID: {$server['.id']}");
                    if (isset($server['disabled']) && $server['disabled'] === 'true') {
                        $this->info('🔧 Enabling existing disabled server...');
                        // Enable the server
                        $mikrotik->writePublic([
                            '/ip/hotspot/enable',
                            "=.id={$server['.id']}"
                        ]);
                        $result = $mikrotik->readPublic();
                        $this->info('✅ Hotspot server enabled!');
                    } else {
                        $this->info('✅ Hotspot server is already active!');
                    }
                    
                    $this->displayHotspotInfo($mikrotik);
                    return;
                }
            }
            
            // Enable new hotspot server
            $this->info('🚀 Creating new hotspot server...');
            
            // Get profiles
            $profiles = $mikrotik->getHotspotServerProfiles();
            $profileName = 'default';
            
            foreach ($profiles as $profile) {
                if (isset($profile['name']) && $profile['name'] === 'default') {
                    $profileName = 'default';
                    break;
                }
            }
            
            $this->info("📋 Using profile: {$profileName}");
            
            // Enable hotspot
            $result = $mikrotik->enableHotspotServer($interface, $profileName);
            
            if (!empty($result)) {
                $this->info('✅ Hotspot service enabled successfully!');
            } else {
                $this->info('✅ Hotspot command executed (check MikroTik for status)');
            }
            
            // Display current status
            $this->displayHotspotInfo($mikrotik);
            
            // Show login URL
            $this->info('');
            $this->info('🌐 Hotspot Login URLs:');
            $this->info('   • https://hotspot.splito.ge.test/hotspot/login');
            $this->info('   • http://192.168.88.1/hotspot/login (via MikroTik)');
            $this->info('');
            $this->info('👥 Test Users:');
            $this->info('   • admin / admin123 (1 day access)');
            $this->info('   • test / test123 (1 hour access)');
            $this->info('   • guest / guest123 (1 hour access)');
            $this->info('   • demo / demo2025 (1 day access)');
            
        } catch (\Exception $e) {
            $this->error('❌ Error enabling hotspot service: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function displayHotspotInfo(MikrotikService $mikrotik)
    {
        $this->info('');
        $this->info('📊 Current Hotspot Status:');
        
        try {
            // Get servers
            $servers = $mikrotik->getHotspotServers();
            $this->info("🖥️  Active servers: " . count($servers));
            
            foreach ($servers as $server) {
                $interface = $server['interface'] ?? 'unknown';
                $profile = $server['profile'] ?? 'unknown';
                $disabled = isset($server['disabled']) && $server['disabled'] === 'true' ? 'DISABLED' : 'ENABLED';
                
                $this->info("   • Interface: {$interface}, Profile: {$profile}, Status: {$disabled}");
            }
            
            // Get active users
            $activeUsers = $mikrotik->getActiveHotspotUsers();
            $this->info("👥 Active users: " . count($activeUsers));
            
            // Get all users
            $allUsers = $mikrotik->getHotspotUsers();
            $this->info("📝 Total users: " . count($allUsers));
            
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not retrieve hotspot status: ' . $e->getMessage());
        }
    }
}
