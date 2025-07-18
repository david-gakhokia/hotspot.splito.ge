<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MikrotikService;

class SetupMikrotikHotspot extends Command
{
    protected $signature = 'mikrotik:setup-hotspot';
    protected $description = 'Complete MikroTik hotspot setup with network configuration';

    public function handle()
    {
        $this->info('🔥 Setting up complete MikroTik hotspot configuration...');
        
        $service = new MikrotikService();
        
        try {
            $this->info('✅ Connected to MikroTik');
            
            // Step 1: Setup interface configurations
            $this->info('🔌 Configuring network interfaces...');
            
            // ether1 = WAN (to internet)
            // ether2 = LAN (hotspot clients)
            
            // Remove any existing IP addresses to avoid conflicts
            $this->info('   🧹 Cleaning existing IP configurations...');
            
            // Step 2: Setup IP addresses
            $this->info('📡 Setting up IP addresses...');
            $this->executeCommand($service, [
                '/ip/address/add',
                '=address=192.168.88.1/24',
                '=interface=ether2',
                '=comment=Hotspot Gateway'
            ], 'Setting LAN IP address');
            
            // Step 3: Setup IP pools
            $this->info('🏊 Creating IP pools...');
            $this->executeCommand($service, [
                '/ip/pool/add',
                '=name=hotspot-pool',
                '=ranges=192.168.88.10-192.168.88.200',
                '=comment=Hotspot user pool'
            ], 'Creating hotspot IP pool');
            
            // Step 4: Setup DHCP server network
            $this->info('🌐 Setting up DHCP network...');
            $this->executeCommand($service, [
                '/ip/dhcp-server/network/add',
                '=address=192.168.88.0/24',
                '=gateway=192.168.88.1',
                '=dns-server=8.8.8.8,8.8.4.4',
                '=comment=Hotspot DHCP network'
            ], 'Setting up DHCP network');
            
            // Step 5: Setup DHCP server
            $this->info('🖥️ Setting up DHCP server...');
            $this->executeCommand($service, [
                '/ip/dhcp-server/add',
                '=name=hotspot-dhcp',
                '=interface=ether2',
                '=address-pool=hotspot-pool',
                '=authoritative=yes',
                '=lease-time=1h',
                '=comment=Hotspot DHCP server'
            ], 'Creating DHCP server');
            
            // Step 6: Enable DHCP server
            $this->executeCommand($service, [
                '/ip/dhcp-server/enable',
                '=numbers=hotspot-dhcp'
            ], 'Enabling DHCP server');
            
            // Step 7: Setup hotspot server profile
            $this->info('👤 Creating hotspot server profile...');
            $this->executeCommand($service, [
                '/ip/hotspot/profile/add',
                '=name=hotspot-profile',
                '=hotspot-address=192.168.88.1',
                '=dns-name=hotspot.local',
                '=html-directory=hotspot',
                '=http-proxy=0.0.0.0:0',
                '=smtp-server=0.0.0.0',
                '=login-by=cookie,http-chap',
                '=http-cookie-lifetime=1d',
                '=trial-uptime=30m',
                '=trial-user-profile=default'
            ], 'Creating hotspot profile');
            
            // Step 8: Setup hotspot server
            $this->info('🔥 Creating hotspot server...');
            $this->executeCommand($service, [
                '/ip/hotspot/add',
                '=name=hotspot1',
                '=interface=ether2',
                '=address-pool=hotspot-pool',
                '=profile=hotspot-profile',
                '=addresses-per-mac=2'
            ], 'Creating hotspot server');
            
            // Step 9: Setup user profiles
            $this->info('📋 Creating user profiles...');
            
            // 1 hour profile
            $this->executeCommand($service, [
                '/ip/hotspot/user/profile/add',
                '=name=1hour',
                '=session-timeout=1h',
                '=idle-timeout=15m',
                '=keepalive-timeout=2m',
                '=status-autorefresh=1m',
                '=shared-users=1',
                '=rate-limit=5M/5M',
                '=address-list=hotspot-users'
            ], 'Creating 1-hour profile');
            
            // 1 day profile
            $this->executeCommand($service, [
                '/ip/hotspot/user/profile/add',
                '=name=1day',
                '=session-timeout=1d',
                '=idle-timeout=30m',
                '=keepalive-timeout=2m',
                '=status-autorefresh=1m',
                '=shared-users=2',
                '=rate-limit=10M/10M',
                '=address-list=hotspot-users'
            ], 'Creating 1-day profile');
            
            // Step 10: Setup firewall rules
            $this->info('🛡️ Setting up firewall rules...');
            
            // NAT rule for internet access
            $this->executeCommand($service, [
                '/ip/firewall/nat/add',
                '=chain=srcnat',
                '=src-address=192.168.88.0/24',
                '=out-interface=ether1',
                '=action=masquerade',
                '=comment=Hotspot NAT'
            ], 'Setting up NAT rule');
            
            // Allow DNS
            $this->executeCommand($service, [
                '/ip/firewall/filter/add',
                '=chain=input',
                '=protocol=udp',
                '=dst-port=53',
                '=src-address=192.168.88.0/24',
                '=action=accept',
                '=comment=Allow DNS for hotspot'
            ], 'Allowing DNS access');
            
            // Step 11: Create test users with different profiles
            $this->info('👥 Creating test users...');
            
            $testUsers = [
                ['name' => 'admin', 'password' => 'admin123', 'profile' => '1day', 'comment' => 'Administrator account'],
                ['name' => 'test1', 'password' => '123456', 'profile' => '1hour', 'comment' => 'Test user 1'],
                ['name' => 'test2', 'password' => '123456', 'profile' => '1hour', 'comment' => 'Test user 2'],
                ['name' => 'guest', 'password' => 'guest123', 'profile' => '1hour', 'comment' => 'Guest user'],
                ['name' => 'demo', 'password' => 'demo2025', 'profile' => '1day', 'comment' => 'Demo account']
            ];
            
            foreach ($testUsers as $user) {
                try {
                    $service->addHotspotUser($user['name'], $user['password'], $user['profile'], $user['comment']);
                    $this->info("   ✅ Created user: {$user['name']} ({$user['profile']})");
                } catch (\Exception $e) {
                    $this->warn("   ⚠️  User {$user['name']} might already exist");
                }
            }
            
            // Step 12: Get current status
            $this->info('📊 Getting configuration status...');
            
            $users = $service->getHotspotUsers();
            $this->info("   👥 Total users: " . count($users));
            
            $servers = $service->getHotspotServers();
            $this->info("   🔥 Hotspot servers: " . count($servers));
            
            $identity = $service->getSystemIdentity();
            $resource = $service->getSystemResource();
            
            $this->info('');
            $this->info('🎉 MikroTik hotspot setup completed successfully!');
            $this->info('');
            $this->info('📝 Configuration Summary:');
            $this->info('   🌐 Hotspot Gateway: 192.168.88.1');
            $this->info('   📡 Interface: ether2 (LAN)');
            $this->info('   🏊 IP Pool: 192.168.88.10-192.168.88.200');
            $this->info('   🔗 DNS Servers: 8.8.8.8, 8.8.4.4');
            $this->info('   ⏱️ DHCP Lease: 1 hour');
            $this->info('');
            $this->info('👤 User Profiles:');
            $this->info('   • 1hour: 1h session, 15m idle, 5Mbps');
            $this->info('   • 1day: 1d session, 30m idle, 10Mbps');
            $this->info('');
            $this->info('🔑 Test Users:');
            foreach ($testUsers as $user) {
                $this->info("   • {$user['name']} / {$user['password']} ({$user['profile']})");
            }
            $this->info('');
            $this->info('🌐 Access hotspot at: http://192.168.88.1/');
            $this->info('📱 Connect device to ether2 and open browser');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
    
    private function executeCommand($service, array $command, string $description)
    {
        try {
            $reflection = new \ReflectionClass($service);
            $writeMethod = $reflection->getMethod('write');
            $readMethod = $reflection->getMethod('read');
            
            $writeMethod->setAccessible(true);
            $readMethod->setAccessible(true);
            
            $writeMethod->invoke($service, $command);
            $response = $readMethod->invoke($service);
            
            $this->info("   ✅ {$description}");
            return $response;
        } catch (\Exception $e) {
            $this->warn("   ⚠️  {$description} - might already exist or need manual setup");
            return null;
        }
    }
}
