<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HotspotStepByStep extends Command
{
    protected $signature = 'hotspot:step-by-step';
    protected $description = 'Step by step MikroTik configuration for Hotspot';

    public function handle()
    {
        $this->info('📋 MikroTik Hotspot Configuration - Step by Step');
        $this->info('Follow these exact steps in MikroTik Terminal (Winbox or SSH)');
        $this->info('');
        
        $this->comment('🔧 STEP 1: Check Current Configuration');
        $this->line('First, let\'s see what we have:');
        $this->line('/interface print');
        $this->line('/interface bridge print');
        $this->line('/ip hotspot print');
        $this->line('');
        $this->line('Press ENTER when you\'ve checked these...');
        $this->line('');
        
        $this->comment('🌉 STEP 2: Create Bridge (if not exists)');
        $this->line('Run these commands ONE BY ONE:');
        $this->line('');
        $this->line('/interface bridge add name=bridge');
        $this->line('/interface bridge port add bridge=bridge interface=ether1');
        $this->line('/interface bridge port add bridge=bridge interface=wlan1');
        $this->line('/ip address add address=192.168.88.1/24 interface=bridge');
        $this->line('');
        $this->line('If you get errors like "already exists", that\'s OK - continue!');
        $this->line('');
        
        $this->comment('📡 STEP 3: Configure WiFi Interface');
        $this->line('/interface wireless set wlan1 mode=ap-bridge');
        $this->line('/interface wireless set wlan1 ssid="SPLITO-WiFi"');
        $this->line('/interface wireless set wlan1 disabled=no');
        $this->line('/interface wireless security-profiles set default wpa2-pre-shared-key="splito123"');
        $this->line('');
        
        $this->comment('🌐 STEP 4: DHCP Server (Critical!)');
        $this->line('/ip pool add name=dhcp-pool ranges=192.168.88.10-192.168.88.254');
        $this->line('/ip dhcp-server add name=dhcp-bridge interface=bridge address-pool=dhcp-pool disabled=no');
        $this->line('/ip dhcp-server network add address=192.168.88.0/24 gateway=192.168.88.1 dns-server=8.8.8.8');
        $this->line('');
        
        $this->comment('🔥 STEP 5: Create Hotspot Server (MOST IMPORTANT!)');
        $this->line('/ip hotspot add name=hotspot interface=bridge profile=default');
        $this->line('/ip hotspot profile set default login-by=http-chap,http-pap');
        $this->line('');
        
        $this->comment('🌍 STEP 6: DNS Configuration');
        $this->line('/ip dns set allow-remote-requests=yes');
        $this->line('/ip dns static add name="hotspot.local" address=192.168.88.1');
        $this->line('');
        
        $this->comment('🔐 STEP 7: Firewall NAT Rules (CRITICAL!)');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept');
        $this->line('');
        
        $this->comment('👥 STEP 8: Add Test Users');
        $this->line('/ip hotspot user add name=test password=test123 profile=default');
        $this->line('/ip hotspot user add name=demo password=demo123 profile=default');
        $this->line('');
        
        $this->comment('✅ STEP 9: Verification Commands');
        $this->line('Run these to verify everything is working:');
        $this->line('/ip hotspot print');
        $this->line('/ip hotspot active print');
        $this->line('/ip firewall nat print chain=dstnat');
        $this->line('');
        
        $this->info('🧪 TESTING:');
        $this->line('1. Connect phone/laptop to "SPLITO-WiFi" (password: splito123)');
        $this->line('2. Open browser and go to google.com');
        $this->line('3. Should redirect to MikroTik login page');
        $this->line('4. Login with: test/test123 or demo/demo123');
        $this->line('');
        
        $this->comment('🚨 If still not working:');
        $this->line('1. Reboot MikroTik router: /system reboot');
        $this->line('2. Or manually visit: http://192.168.88.1:8080');
        $this->line('3. Check if you can ping 192.168.88.1 from connected device');
        
        return Command::SUCCESS;
    }
}
