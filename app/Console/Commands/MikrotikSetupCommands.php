<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MikrotikSetupCommands extends Command
{
    protected $signature = 'mikrotik:commands';
    protected $description = 'Display MikroTik terminal commands for proper Hotspot setup';

    public function handle()
    {
        $this->info('🔧 MikroTik RouterOS Commands for Hotspot Setup');
        $this->info('Copy and paste these commands in MikroTik Terminal (Winbox or SSH):');
        $this->info('');
        
        $this->comment('1. 📶 WiFi Interface Configuration:');
        $this->line('/interface wireless set wlan1 mode=ap-bridge ssid="SPLITO-WiFi" band=2ghz-b/g/n frequency=auto disabled=no');
        $this->line('/interface wireless security-profiles set default mode=dynamic-keys authentication-types=wpa-psk,wpa2-psk wpa-pre-shared-key="splito123" wpa2-pre-shared-key="splito123"');
        $this->line('');
        
        $this->comment('2. 🌉 Bridge Configuration:');
        $this->line('/interface bridge add name=bridge protocol-mode=rstp');
        $this->line('/interface bridge port add bridge=bridge interface=ether1');
        $this->line('/interface bridge port add bridge=bridge interface=wlan1');
        $this->line('');
        
        $this->comment('3. 🌐 IP Configuration:');
        $this->line('/ip address add address=192.168.88.1/24 interface=bridge');
        $this->line('/ip pool add name=dhcp-pool ranges=192.168.88.10-192.168.88.254');
        $this->line('');
        
        $this->comment('4. 📡 DHCP Server Configuration:');
        $this->line('/ip dhcp-server add name=dhcp-bridge interface=bridge lease-time=1h address-pool=dhcp-pool disabled=no');
        $this->line('/ip dhcp-server network add address=192.168.88.0/24 gateway=192.168.88.1 dns-server=8.8.8.8,8.8.4.4');
        $this->line('');
        
        $this->comment('5. 🔥 Hotspot Configuration:');
        $this->line('/ip hotspot add name=hotspot interface=bridge address-pool=dhcp-pool profile=default disabled=no');
        $this->line('/ip hotspot profile set default hotspot-address=192.168.88.1 dns-name=hotspot.local html-directory=hotspot login-by=http-chap,http-pap use-radius=no');
        $this->line('');
        
        $this->comment('6. 🌍 DNS Configuration:');
        $this->line('/ip dns set allow-remote-requests=yes servers=8.8.8.8,8.8.4.4');
        $this->line('/ip dns static add name="hotspot.local" address=192.168.88.1');
        $this->line('/ip dns static add name="login.hotspot.local" address=192.168.88.1');
        $this->line('');
        
        $this->comment('7. 🔐 Firewall Rules for Redirection:');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080');
        $this->line('/ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept');
        $this->line('/ip firewall filter add chain=input protocol=tcp dst-port=8291 action=accept comment="Winbox access"');
        $this->line('');
        
        $this->comment('8. 👥 Test Hotspot Users:');
        $this->line('/ip hotspot user add name=test password=test123 profile=default');
        $this->line('/ip hotspot user add name=demo password=demo123 profile=default');
        $this->line('/ip hotspot user add name=guest password=guest123 profile=default');
        $this->line('');
        
        $this->info('🚀 After running these commands:');
        $this->info('✅ WiFi devices will connect to "SPLITO-WiFi" with password "splito123"');
        $this->info('✅ Devices will automatically be redirected to login portal');
        $this->info('✅ Users can login with test/test123, demo/demo123, or guest/guest123');
        $this->info('');
        
        $this->comment('🔗 Login Portal URL: https://hotspot.splito.ge.test/hotspot/login');
        $this->comment('⚙️ Management Panel: https://hotspot.splito.ge.test/hotspot/management');
        
        return Command::SUCCESS;
    }
}
