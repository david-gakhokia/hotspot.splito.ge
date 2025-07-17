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
            
            // Test 2: All users (not just hotspot)
            $this->info('2️⃣ Testing system users:');
            $service->writePublic(['/user/print']);
            $systemUsers = $service->readPublic();
            $this->info('System users response: ' . json_encode($systemUsers));
            
            // Test 3: API user info
            $this->info('3️⃣ Testing API user info:');
            $service->writePublic(['/user/print', '?name=apiuser']);
            $apiUser = $service->readPublic();
            $this->info('API user response: ' . json_encode($apiUser));
            
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
                $this->warn('⚠️  No users found');
            }
            
            // Test raw API response
            $this->info('🔍 Testing raw API response...');
            $response = $service->testRawResponse();
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
            
        } catch (\Exception $e) {
                        $this->error('❌ Error: ' . $e->getMessage());
            $service->disconnect();
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
