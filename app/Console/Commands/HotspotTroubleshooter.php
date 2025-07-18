<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HotspotTroubleshooter extends Command
{
    protected $signature = 'hotspot:troubleshoot';
    protected $description = 'Advanced troubleshooting for Hotspot login issues';

    public function handle()
    {
        $this->info('🔧 Advanced Hotspot Troubleshooting Guide');
        $this->info('');
        
        $this->comment('STEP 1: Verify Basic MikroTik Configuration');
        $this->line('Run these commands in MikroTik Terminal to check status:');
        $this->line('');
        $this->line('/ip hotspot print');
        $this->line('/ip hotspot profile print');
        $this->line('/interface bridge print');
        $this->line('/ip address print');
        $this->line('/ip firewall nat print chain=dstnat');
        $this->line('');
        
        $this->comment('STEP 2: Common Issues and Solutions');
        $this->line('');
        
        $this->warn('Issue 1: No Bridge Interface');
        $this->line('Solution:');
        $this->line('/interface bridge add name=bridge');
        $this->line('/interface bridge port add bridge=bridge interface=ether1');
        $this->line('/interface bridge port add bridge=bridge interface=wlan1');
        $this->line('/ip address add address=192.168.88.1/24 interface=bridge');
        $this->line('');
        
        $this->warn('Issue 2: No DHCP Server');
        $this->line('Solution:');
        $this->line('/ip pool add name=dhcp-pool ranges=192.168.88.10-192.168.88.254');
        $this->line('/ip dhcp-server add name=dhcp-bridge interface=bridge address-pool=dhcp-pool disabled=no');
        $this->line('/ip dhcp-server network add address=192.168.88.0/24 gateway=192.168.88.1 dns-server=8.8.8.8');
        $this->line('');
        
        $this->warn('Issue 3: WiFi Interface Not in AP Mode');
        $this->line('Solution:');
        $this->line('/interface wireless set wlan1 mode=ap-bridge disabled=no');
        $this->line('/interface wireless set wlan1 ssid="SPLITO-WiFi"');
        $this->line('');
        
        $this->warn('Issue 4: No NAT Redirection Rules');
        $this->line('Solution:');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080');
        $this->line('');
        
        $this->comment('STEP 3: Complete Configuration Script');
        $this->line('Run this complete script in MikroTik Terminal:');
        $this->line('');
        $this->info($this->getCompleteScript());
        
        $this->comment('STEP 4: Testing Checklist');
        $this->line('□ 1. MikroTik Router accessible via Winbox/SSH');
        $this->line('□ 2. WiFi interface broadcasting and connectable');
        $this->line('□ 3. Bridge interface configured with IP address');
        $this->line('□ 4. DHCP server running and assigning IPs');
        $this->line('□ 5. Hotspot server enabled on bridge interface');
        $this->line('□ 6. NAT rules for HTTP redirection in place');
        $this->line('□ 7. Test device can connect to WiFi and get IP');
        $this->line('□ 8. Opening browser redirects to MikroTik login page');
        $this->line('');
        
        $this->comment('STEP 5: Alternative Testing');
        $this->line('If automatic redirection doesn\'t work, manually visit:');
        $this->line('http://192.168.88.1:8080');
        $this->line('This should show the MikroTik hotspot login page');
        
        return Command::SUCCESS;
    }

    private function getCompleteScript()
    {
        return '
# Complete MikroTik Hotspot Configuration Script
# Run this line by line in MikroTik Terminal

# 1. Basic Interface Configuration
/interface bridge add name=bridge protocol-mode=rstp
/interface bridge port add bridge=bridge interface=ether1
/interface bridge port add bridge=bridge interface=wlan1
/ip address add address=192.168.88.1/24 interface=bridge

# 2. WiFi Configuration
/interface wireless set wlan1 mode=ap-bridge ssid="SPLITO-WiFi" disabled=no
/interface wireless security-profiles set default mode=dynamic-keys authentication-types=wpa-psk,wpa2-psk wpa2-pre-shared-key="splito123"

# 3. DHCP Server
/ip pool add name=dhcp-pool ranges=192.168.88.10-192.168.88.254
/ip dhcp-server add name=dhcp-bridge interface=bridge address-pool=dhcp-pool disabled=no lease-time=1h
/ip dhcp-server network add address=192.168.88.0/24 gateway=192.168.88.1 dns-server=8.8.8.8,8.8.4.4

# 4. DNS Configuration
/ip dns set allow-remote-requests=yes servers=8.8.8.8,8.8.4.4
/ip dns static add name="hotspot.local" address=192.168.88.1

# 5. Hotspot Configuration
/ip hotspot add name=hotspot interface=bridge address-pool=dhcp-pool profile=default disabled=no
/ip hotspot profile set default login-by=http-chap,http-pap html-directory=hotspot use-radius=no

# 6. Firewall Rules
/ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080
/ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080
/ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept

# 7. Test Users
/ip hotspot user add name=test password=test123 profile=default
/ip hotspot user add name=demo password=demo123 profile=default
/ip hotspot user add name=guest password=guest123 profile=default

# Configuration Complete!
        ';
    }
}
