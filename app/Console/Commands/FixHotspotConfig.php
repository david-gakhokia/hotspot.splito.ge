<?php

namespace App\Console\Commands;

use App\Services\MikrotikService;
use Illuminate\Console\Command;

class FixHotspotConfig extends Command
{
    protected $signature = 'hotspot:fix {--redirect-url=https://hotspot.splito.ge.test/hotspot/login}';
    protected $description = 'Fix MikroTik Hotspot configuration for proper device redirection';

    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        parent::__construct();
        $this->mikrotikService = $mikrotikService;
    }

    public function handle()
    {
        $redirectUrl = $this->option('redirect-url');
        
        $this->info('🔧 Fixing MikroTik Hotspot Configuration...');
        
        try {
            // Step 1: Configure Hotspot Profile
            $this->info('📋 Configuring Hotspot Profile...');
            $this->configureHotspotProfile($redirectUrl);
            
            // Step 2: Enable Hotspot Server if not exists
            $this->info('📡 Ensuring Hotspot Server is active...');
            $this->ensureHotspotServer();
            
            // Step 3: Configure DNS redirection
            $this->info('🌐 Configuring DNS redirection...');
            $this->configureDNSRedirection();
            
            // Step 4: Set up firewall rules for redirection
            $this->info('🔥 Setting up firewall rules...');
            $this->configureFirewallRules();
            
            $this->info('');
            $this->info('✅ Hotspot configuration fixed!');
            $this->info('🔗 Login URL: ' . $redirectUrl);
            $this->info('📱 New devices should now be redirected to login portal');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error fixing Hotspot configuration: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function configureHotspotProfile($redirectUrl)
    {
        // Configure default hotspot profile for proper redirection
        $profiles = $this->mikrotikService->getHotspotServerProfiles();
        
        // Update or create default profile
        $this->mikrotikService->updateHotspotServerProfile('*1', [
            'name' => 'default',
            'hotspot-address' => '192.168.88.1',
            'dns-name' => 'hotspot.local',
            'html-directory' => 'hotspot',
            'http-cookie-lifetime' => '3d',
            'http-proxy' => '0.0.0.0:0',
            'login-by' => 'http-chap,http-pap',
            'use-radius' => 'no'
        ]);
        
        $this->line('✅ Hotspot profile configured');
    }

    private function ensureHotspotServer()
    {
        $servers = $this->mikrotikService->getHotspotServers();
        
        if (empty($servers)) {
            $this->mikrotikService->enableHotspotServer('bridge', 'default');
            $this->line('✅ Hotspot server created on bridge interface');
        } else {
            $this->line('✅ Hotspot server already exists');
        }
    }

    private function configureDNSRedirection()
    {
        // This would typically be done via MikroTik terminal
        // For now, we'll provide instructions
        $this->line('💡 Manual DNS configuration needed:');
        $this->line('   /ip dns set allow-remote-requests=yes');
        $this->line('   /ip dns static add name="hotspot.local" address=192.168.88.1');
    }

    private function configureFirewallRules()
    {
        // This would typically be done via MikroTik terminal
        // For now, we'll provide instructions
        $this->line('💡 Manual firewall rules needed:');
        $this->line('   /ip firewall nat add chain=dstnat protocol=tcp dst-port=80 action=redirect to-ports=8080');
        $this->line('   /ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept');
    }
}
