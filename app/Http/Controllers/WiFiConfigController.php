<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WiFiConfigController extends Controller
{
    protected $mikrotikService;

    public function __construct(MikrotikService $mikrotikService)
    {
        $this->mikrotikService = $mikrotikService;
    }

    /**
     * Get current WiFi configuration
     */
    public function getWiFiConfig()
    {
        try {
            $interfaces = $this->mikrotikService->getWirelessInterfaces();
            
            $wifiConfig = [];
            
            foreach ($interfaces as $interface) {
                if (isset($interface['name']) && strpos($interface['name'], 'wlan') !== false) {
                    $wifiConfig[] = [
                        'name' => $interface['name'] ?? 'Unknown',
                        'ssid' => $interface['ssid'] ?? 'Not Set',
                        'frequency' => $interface['frequency'] ?? 'Auto',
                        'band' => $interface['band'] ?? '2ghz-b/g/n',
                        'mode' => $interface['mode'] ?? 'ap-bridge',
                        'disabled' => $interface['disabled'] ?? 'false',
                        'security_profile' => $interface['security-profile'] ?? 'default'
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'wifi_interfaces' => $wifiConfig
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get WiFi configuration: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'WiFi კონფიგურაციის წაკითხვა ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update WiFi SSID
     */
    public function updateSSID(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'ssid' => 'required|string|min:1|max:32'
        ]);

        try {
            $this->mikrotikService->updateWirelessSSID($request->interface, $request->ssid);
            
            Log::info('WiFi SSID updated successfully', [
                'interface' => $request->interface,
                'new_ssid' => $request->ssid
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'WiFi SSID წარმატებით განახლდა!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update WiFi SSID: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'WiFi SSID-ის განახლება ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update WiFi Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'password' => 'required|string|min:8|max:63',
            'security_profile' => 'string'
        ]);

        try {
            $securityProfile = $request->security_profile ?? 'default';
            $this->mikrotikService->updateWirelessPassword($securityProfile, $request->password);
            
            Log::info('WiFi Password updated successfully', [
                'interface' => $request->interface,
                'security_profile' => $securityProfile
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'WiFi პაროლი წარმატებით განახლდა!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update WiFi password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'WiFi პაროლის განახლება ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get security profiles
     */
    public function getSecurityProfiles()
    {
        try {
            $profiles = $this->mikrotikService->getWirelessSecurityProfiles();
            
            $securityProfiles = [];
            
            foreach ($profiles as $profile) {
                $securityProfiles[] = [
                    'name' => $profile['name'] ?? 'Unknown',
                    'mode' => $profile['mode'] ?? 'none',
                    'authentication_types' => $profile['authentication-types'] ?? 'wpa-psk,wpa2-psk',
                    'unicast_ciphers' => $profile['unicast-ciphers'] ?? 'aes-ccm',
                    'group_ciphers' => $profile['group-ciphers'] ?? 'aes-ccm'
                ];
            }
            
            return response()->json([
                'success' => true,
                'security_profiles' => $securityProfiles
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get security profiles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Security profiles-ის წაკითხვა ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enable/Disable WiFi interface
     */
    public function toggleWiFi(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'enabled' => 'required|boolean'
        ]);

        try {
            $this->mikrotikService->toggleWirelessInterface($request->interface, $request->enabled);
            
            $message = $request->enabled ? 'WiFi ჩართულია!' : 'WiFi გამორთულია!';
            
            Log::info('WiFi interface toggled', [
                'interface' => $request->interface,
                'enabled' => $request->enabled
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to toggle WiFi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'WiFi სტატუსის ცვლილება ვერ მოხერხდა: ' . $e->getMessage()
            ], 500);
        }
    }
}
