<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotspotConfigController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * Get Hotspot configuration and status
     */
    public function getHotspotStatus()
    {
        try {
            // Get hotspot servers
            $servers = $this->mikrotikService->getHotspotServers();
            
            // Get hotspot profiles
            $profiles = $this->mikrotikService->getHotspotServerProfiles();
            
            // Get wireless interfaces
            $interfaces = $this->mikrotikService->getWirelessInterfaces();
            
            // Get IP pools
            $this->mikrotikService->write(['/ip/pool/print']);
            $poolResponse = $this->mikrotikService->read();
            
            $pools = [];
            $current = [];
            foreach ($poolResponse as $line) {
                if ($line === '!re') {
                    if (!empty($current)) {
                        $pools[] = $current;
                        $current = [];
                    }
                } elseif (str_starts_with($line, '=') && str_contains($line, '=')) {
                    $parts = explode('=', $line);
                    if (count($parts) >= 3) {
                        $key = $parts[1];
                        $value = $parts[2];
                        $current[$key] = $value;
                    }
                }
            }
            if (!empty($current)) {
                $pools[] = $current;
            }
            
            return response()->json([
                'success' => true,
                'hotspot_servers' => $servers,
                'hotspot_profiles' => $profiles,
                'wireless_interfaces' => $interfaces,
                'ip_pools' => $pools
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get Hotspot status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hotspot სტატუსის წაკითხვა ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configure Hotspot properly
     */
    public function configureHotspot(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'ip_pool' => 'string',
            'dns_servers' => 'string'
        ]);

        try {
            $interface = $request->interface;
            $ipPool = $request->ip_pool ?? 'default-dhcp';
            $dnsServers = $request->dns_servers ?? '8.8.8.8,8.8.4.4';

            // Step 1: Create IP pool if not exists
            $this->createIPPool($ipPool);
            
            // Step 2: Configure DHCP server
            $this->configureDHCP($interface, $ipPool);
            
            // Step 3: Enable Hotspot server
            $this->mikrotikService->enableHotspotServer($interface, 'default');
            
            // Step 4: Configure Hotspot profile
            $this->configureHotspotProfile($dnsServers);
            
            Log::info('Hotspot configured successfully', [
                'interface' => $interface,
                'ip_pool' => $ipPool,
                'dns_servers' => $dnsServers
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hotspot წარმატებით კონფიგურირდა!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to configure Hotspot: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hotspot კონფიგურაცია ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create IP pool for hotspot
     */
    private function createIPPool($poolName)
    {
        try {
            // Check if pool exists
            $this->mikrotikService->write(['/ip/pool/print', '?name=' . $poolName]);
            $response = $this->mikrotikService->read();
            
            $poolExists = false;
            foreach ($response as $line) {
                if (str_contains($line, $poolName)) {
                    $poolExists = true;
                    break;
                }
            }
            
            if (!$poolExists) {
                // Create new pool
                $this->mikrotikService->write([
                    '/ip/pool/add',
                    '=name=' . $poolName,
                    '=ranges=192.168.88.10-192.168.88.254'
                ]);
                $this->mikrotikService->read();
                Log::info('IP Pool created: ' . $poolName);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to create IP pool: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Configure DHCP server
     */
    private function configureDHCP($interface, $ipPool)
    {
        try {
            // Add DHCP server
            $this->mikrotikService->write([
                '/ip/dhcp-server/add',
                '=name=dhcp-' . $interface,
                '=interface=' . $interface,
                '=lease-time=1h',
                '=address-pool=' . $ipPool,
                '=disabled=no'
            ]);
            $this->mikrotikService->read();
            
            // Add DHCP network
            $this->mikrotikService->write([
                '/ip/dhcp-server/network/add',
                '=address=192.168.88.0/24',
                '=gateway=192.168.88.1',
                '=dns-server=8.8.8.8,8.8.4.4',
                '=domain=hotspot.local'
            ]);
            $this->mikrotikService->read();
            
            Log::info('DHCP configured for interface: ' . $interface);
            
        } catch (\Exception $e) {
            Log::warning('DHCP configuration warning: ' . $e->getMessage());
            // Continue even if DHCP already exists
        }
    }

    /**
     * Configure Hotspot profile
     */
    private function configureHotspotProfile($dnsServers)
    {
        try {
            // Update default hotspot profile
            $this->mikrotikService->write(['/ip/hotspot/profile/print', '?name=default']);
            $response = $this->mikrotikService->read();
            
            $profileId = null;
            foreach ($response as $line) {
                if (str_starts_with($line, '=.id=')) {
                    $profileId = substr($line, 5);
                    break;
                }
            }
            
            if ($profileId) {
                $this->mikrotikService->write([
                    '/ip/hotspot/profile/set',
                    '=.id=' . $profileId,
                    '=dns-name=hotspot.local',
                    '=html-directory=hotspot',
                    '=http-proxy=0.0.0.0:0',
                    '=login-by=http-chap,http-pap',
                    '=use-radius=no'
                ]);
                $this->mikrotikService->read();
                
                Log::info('Hotspot profile configured');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to configure Hotspot profile: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Set custom login page redirect
     */
    public function setLoginRedirect(Request $request)
    {
        $request->validate([
            'login_url' => 'required|url'
        ]);

        try {
            $loginUrl = $request->login_url;
            
            // Update hotspot profile with custom login page
            $this->mikrotikService->write(['/ip/hotspot/profile/print', '?name=default']);
            $response = $this->mikrotikService->read();
            
            $profileId = null;
            foreach ($response as $line) {
                if (str_starts_with($line, '=.id=')) {
                    $profileId = substr($line, 5);
                    break;
                }
            }
            
            if ($profileId) {
                $this->mikrotikService->write([
                    '/ip/hotspot/profile/set',
                    '=.id=' . $profileId,
                    '=http-proxy=' . parse_url($loginUrl, PHP_URL_HOST) . ':' . (parse_url($loginUrl, PHP_URL_PORT) ?? 80)
                ]);
                $this->mikrotikService->read();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Login redirect წარმატებით დაყენდა!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to set login redirect: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login redirect-ის დაყენება ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }
}
