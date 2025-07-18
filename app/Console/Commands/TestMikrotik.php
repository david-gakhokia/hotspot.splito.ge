<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class TestMikrotik extends Command
{
    protected $signature = 'test:mikrotik';
    protected $description = 'Test MikroTik connection and data retrieval';

    public function handle()
    {
        $this->info('🔧 Testing MikroTik connection...');

        try {
            $service = new MikrotikService();
            $this->info('✅ Connection established');
            
            // Test API permissions
            $this->info('🧪 Testing API permissions...');
            
            // Test 1: Current user info
            $this->info('1️⃣ Testing current user permissions:');
            $service->writePublic(['/system/identity/print']);
            $identity = $service->readPublic();
            $this->info('Identity response: ' . json_encode($identity));
            
            // Test 2: Check hotspot configuration  
            $this->info('2️⃣ Testing hotspot configuration:');
            $service->writePublic(['/ip/hotspot/print']);
            $hotspotConfig = $service->readPublic();
            $this->info('Hotspot config response: ' . json_encode($hotspotConfig));
            
            // Test 3: Check hotspot profiles
            $this->info('3️⃣ Testing hotspot profiles:');
            $service->writePublic(['/ip/hotspot/profile/print']);
            $hotspotProfiles = $service->readPublic();
            $this->info('Hotspot profiles response: ' . json_encode($hotspotProfiles));
            
            // Test 4: Check hotspot user profiles
            $this->info('4️⃣ Testing hotspot user profiles:');
            $service->writePublic(['/ip/hotspot/user/profile/print']);
            $userProfiles = $service->readPublic();
            $this->info('User profiles response: ' . json_encode($userProfiles));
            
            // Test 5: Check interfaces
            $this->info('5️⃣ Testing interfaces:');
            $service->writePublic(['/interface/print']);
            $interfaces = $service->readPublic();
            $this->info('Interfaces response: ' . json_encode($interfaces));
            
            // Test 6: Check system resources
            $this->info('6️⃣ Testing system resources:');
            $service->writePublic(['/system/resource/print']);
            $resources = $service->readPublic();
            $this->info('Resources response: ' . json_encode($resources));
            
            // Test different API calls
            $this->info('📊 Getting hotspot users...');
            $users = $service->getHotspotUsers();
            $this->info('✅ Users retrieved: ' . count($users));
            
            if (count($users) > 0) {
                $this->info('👤 All users:');
                foreach ($users as $index => $user) {
                    $this->line("  User {$index}:");
                    foreach ($user as $key => $value) {
                        $this->line("    {$key}: {$value}");
                    }
                    $this->line('');
                }
            } else {
                $this->warn('⚠️  No hotspot users found');
            }
            
            // Test raw API response
            $this->info('🔍 Testing raw hotspot user API response...');
            $service->writePublic(['/ip/hotspot/user/print']);
            $response = $service->readPublic();
            $this->info('Raw response:');
            foreach ($response as $line) {
                $this->line("  '{$line}'");
            }
            
            // Test manual parsing
            $this->info('🔧 Testing manual parsing...');
            $manualUsers = $this->parseManually($response);
            $this->info('Manual parsing result:');
            foreach ($manualUsers as $index => $user) {
                $this->line("  User {$index}:");
                foreach ($user as $key => $value) {
                    $this->line("    {$key}: {$value}");
                }
                $this->line('');
            }
            
            $this->info('📋 Getting profiles...');
            $profiles = $service->getHotspotProfiles();
            $this->info('✅ Profiles retrieved: ' . count($profiles));
            
            if (count($profiles) > 0) {
                $this->info('📝 Available profiles:');
                foreach ($profiles as $profile) {
                    $this->line('  - ' . ($profile['name'] ?? 'Unknown'));
                }
            }
            
            // Test active users
            $this->info('🔗 Testing active hotspot users:');
            $service->writePublic(['/ip/hotspot/active/print']);
            $activeUsers = $service->readPublic();
            $this->info('Active users response: ' . json_encode($activeUsers));
            
            $service->disconnect();
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            if (isset($service)) {
                $service->disconnect();
            }
        }
    }

    private function parseManually($response)
    {
        $users = [];
        $current = [];

        foreach ($response as $index => $line) {
            $this->line("Processing line {$index}: '{$line}'");
            
            if ($line === '!re') {
                // Start of new record - save previous if exists
                if (!empty($current)) {
                    $users[] = $current;
                    $this->line("  -> Added user with " . count($current) . " fields");
                }
                $current = []; // Reset for new record
            } elseif (str_starts_with($line, '=')) {
                // Find the second = sign
                $equalPos = strpos($line, '=', 1);
                if ($equalPos !== false) {
                    $key = substr($line, 1, $equalPos - 1);
                    $value = substr($line, $equalPos + 1);
                    $current[$key] = $value;
                    $this->line("  -> Set {$key} = {$value}");
                }
            } elseif ($line === '!done') {
                // End of response - add last user if exists
                if (!empty($current)) {
                    $users[] = $current;
                    $this->line("  -> Added final user with " . count($current) . " fields");
                }
                break;
            }
        }

        // If no !done found, add the last user anyway
        if (!empty($current)) {
            $users[] = $current;
            $this->line("  -> Added remaining user with " . count($current) . " fields");
        }

        return $users;
    }
}
