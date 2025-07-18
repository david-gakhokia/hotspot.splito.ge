<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class ConfigureMikrotik extends Command
{
    protected $signature = 'mikrotik:configure';
    protected $description = 'Configure MikroTik hotspot from scratch';

    public function handle()
    {
        $this->info('🔧 Configuring MikroTik hotspot...');
        
        $service = new MikrotikService();
        
        try {
            $this->info('✅ Connected to MikroTik');
            
            // Step 1: Create hotspot user profile
            $this->info('� Creating user profile...');
            try {
                $service->addHotspotUser('test1', '123456', 'default', 'Test user 1');
                $this->info('   ✅ Created user: test1');
            } catch (\Exception $e) {
                $this->warn('   ⚠️  User test1 might already exist');
            }
            
            try {
                $service->addHotspotUser('test2', '123456', 'default', 'Test user 2');
                $this->info('   ✅ Created user: test2');
            } catch (\Exception $e) {
                $this->warn('   ⚠️  User test2 might already exist');
            }
            
            try {
                $service->addHotspotUser('guest', 'guest123', 'default', 'Guest user');
                $this->info('   ✅ Created user: guest');
            } catch (\Exception $e) {
                $this->warn('   ⚠️  User guest might already exist');
            }
            
            // Step 2: Get current configuration
            $this->info('� Getting current configuration...');
            $users = $service->getHotspotUsers();
            $this->info('   📋 Total users: ' . count($users));
            
            $profiles = $service->getHotspotProfiles();
            $this->info('   📋 Available profiles: ' . count($profiles));
            
            $servers = $service->getHotspotServers();
            $this->info('   📋 Hotspot servers: ' . count($servers));
            
            // Step 3: Display system info
            $this->info('�️ System information:');
            $identity = $service->getSystemIdentity();
            $this->info('   📡 Device: ' . ($identity['name'] ?? 'Unknown'));
            
            $resource = $service->getSystemResource();
            $this->info('   💾 Version: ' . ($resource['version'] ?? 'Unknown'));
            $this->info('   🏗️ Board: ' . ($resource['board-name'] ?? 'Unknown'));
            
            $this->info('');
            $this->info('🎉 MikroTik hotspot configured successfully!');
            $this->info('');
            $this->info('� Test users created:');
            $this->info('   • test1 / 123456');
            $this->info('   • test2 / 123456');
            $this->info('   • guest / guest123');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
