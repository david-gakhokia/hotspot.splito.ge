<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class QuickSetupHotspot extends Command
{
    protected $signature = 'mikrotik:quick-setup';
    protected $description = 'Quick MikroTik hotspot setup using API calls';

    public function handle()
    {
        $this->info('⚡ Quick MikroTik hotspot setup...');
        
        try {
            $service = new MikrotikService();
            $this->info('✅ Connected to MikroTik');
            
            // Check current configuration
            $this->info('📊 Checking current configuration...');
            
            // Get system info
            $identity = $service->getSystemIdentity();
            $resource = $service->getSystemResource();
            
            $this->info("   📡 Device: " . ($identity['name'] ?? 'MikroTik'));
            $this->info("   💾 Version: " . ($resource['version'] ?? 'Unknown'));
            $this->info("   🏗️ Board: " . ($resource['board-name'] ?? 'Unknown'));
            
            // Check existing users
            $users = $service->getHotspotUsers();
            $this->info("   👥 Current users: " . count($users));
            
            // Check hotspot servers
            $servers = $service->getHotspotServers();
            $this->info("   🔥 Hotspot servers: " . count($servers));
            
            // Add some test users if none exist
            if (count($users) < 3) {
                $this->info('👥 Adding test users...');
                
                $testUsers = [
                    ['name' => 'admin', 'password' => 'admin123'],
                    ['name' => 'test', 'password' => 'test123'],
                    ['name' => 'guest', 'password' => 'guest123'],
                    ['name' => 'demo', 'password' => 'demo2025']
                ];
                
                foreach ($testUsers as $user) {
                    try {
                        $result = $service->addHotspotUser($user['name'], $user['password'], 'default', 'Auto-created user');
                        $this->info("   ✅ Created user: {$user['name']} / {$user['password']}");
                    } catch (\Exception $e) {
                        $this->warn("   ⚠️  User {$user['name']} - " . $e->getMessage());
                    }
                }
            }
            
            // Final status check
            $this->info('📈 Final status check...');
            $finalUsers = $service->getHotspotUsers();
            $this->info("   👥 Total users now: " . count($finalUsers));
            
            // Display all users
            if (!empty($finalUsers)) {
                $this->info('📋 Current users:');
                foreach ($finalUsers as $i => $user) {
                    $name = $user['name'] ?? "User $i";
                    $profile = $user['profile'] ?? 'default';
                    $this->info("   • $name ($profile)");
                }
            }
            
            $this->info('');
            $this->info('🎉 Quick setup completed!');
            $this->info('');
            $this->info('🔑 Test credentials:');
            $this->info('   • admin / admin123');
            $this->info('   • test / test123');
            $this->info('   • guest / guest123');
            $this->info('   • demo / demo2025');
            $this->info('');
            $this->info('🌐 Web Interface: https://hotspot.splito.ge.test/mikrotik');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('   Stack trace: ' . $e->getTraceAsString());
        }
    }
}
