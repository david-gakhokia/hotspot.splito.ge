<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MikrotikTestController extends Controller
{
    public function index()
    {
        try {
            $mikrotik = new MikrotikService();
            
            // Get user count
            $users = $mikrotik->getHotspotUsers();
            $userCount = count($users);
            
            // Get active sessions count
            $activeSessions = $mikrotik->getActiveHotspotUsers();
            $activeCount = count($activeSessions);
            
            // Get device info
            $identity = $mikrotik->getSystemIdentity();
            $deviceName = $identity['name'] ?? 'MikroTik';
            
            return view('mikrotik.dashboard', compact('userCount', 'activeCount', 'deviceName'));
            
        } catch (\Exception $e) {
            return view('mikrotik.dashboard', [
                'userCount' => 0,
                'activeCount' => 0,
                'deviceName' => 'MikroTik (Disconnected)',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function oldIndex()
    {
        $connectionStatus = 'disconnected';
        $deviceInfo = [];
        $systemResources = [];
        $interfaces = [];
        $hotspotStatus = [];
        $userInfo = [];
        $error = null;

        try {
            $mikrotik = new MikrotikService();
            $connectionStatus = 'connected';

            // Get system identity
            $mikrotik->writePublic(['/system/identity/print']);
            $identity = $mikrotik->readPublic();
            
            // Get system resources
            $mikrotik->writePublic(['/system/resource/print']);
            $resources = $mikrotik->readPublic();
            
            // Get interfaces
            $mikrotik->writePublic(['/interface/print']);
            $interfaceData = $mikrotik->readPublic();
            
            // Get hotspot servers
            $mikrotik->writePublic(['/ip/hotspot/print']);
            $hotspotData = $mikrotik->readPublic();
            
            // Get current user permissions
            $mikrotik->writePublic(['/user/print', '?name=' . config('mikrotik.user')]);
            $currentUser = $mikrotik->readPublic();
            
            // Parse responses
            $deviceInfo = $this->parseResponse($identity);
            $systemResources = $this->parseResponse($resources);
            $interfaces = $this->parseResponse($interfaceData);
            $hotspotStatus = $this->parseResponse($hotspotData);
            $userInfo = $this->parseResponse($currentUser);
            
            $mikrotik->disconnect();
            
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('MikroTik Test Error: ' . $e->getMessage());
        }

        return view('mikrotik.test', compact(
            'connectionStatus', 
            'deviceInfo', 
            'systemResources', 
            'interfaces', 
            'hotspotStatus', 
            'userInfo', 
            'error'
        ));
    }

    public function testConnection(Request $request)
    {
        $results = [
            'connection' => false,
            'login' => false,
            'api_access' => false,
            'hotspot_access' => false,
            'user_management' => false,
            'messages' => [],
            'details' => []
        ];

        try {
            // Test 1: Connection
            $mikrotik = new MikrotikService();
            $results['connection'] = true;
            $results['messages'][] = '✅ Connection established successfully';
            
            // Test 2: Identity check
            $mikrotik->writePublic(['/system/identity/print']);
            $identity = $mikrotik->readPublic();
            if (!empty($identity)) {
                $results['login'] = true;
                $results['messages'][] = '✅ Login successful';
                $results['details']['identity'] = $this->parseResponse($identity);
            }
            
            // Test 3: API access
            $mikrotik->writePublic(['/system/routerboard/print']);
            $routerboard = $mikrotik->readPublic();
            if (!empty($routerboard)) {
                $results['api_access'] = true;
                $results['messages'][] = '✅ API access confirmed';
                $results['details']['routerboard'] = $this->parseResponse($routerboard);
            }
            
            // Test 4: Hotspot access
            $mikrotik->writePublic(['/ip/hotspot/print']);
            $hotspot = $mikrotik->readPublic();
            $results['hotspot_access'] = true;
            $results['messages'][] = '✅ Hotspot API access confirmed';
            $results['details']['hotspot'] = $this->parseResponse($hotspot);
            
            // Test 5: User management
            $mikrotik->writePublic(['/ip/hotspot/user/print']);
            $users = $mikrotik->readPublic();
            $results['user_management'] = true;
            $results['messages'][] = '✅ User management access confirmed';
            $results['details']['users'] = $this->parseResponse($users);
            
            // Test 6: System resources
            $mikrotik->writePublic(['/system/resource/print']);
            $resources = $mikrotik->readPublic();
            $results['details']['resources'] = $this->parseResponse($resources);
            
            $mikrotik->disconnect();
            
        } catch (\Exception $e) {
            $results['messages'][] = '❌ Error: ' . $e->getMessage();
            Log::error('MikroTik Connection Test Error: ' . $e->getMessage());
        }

        return response()->json($results);
    }

    public function getSystemInfo()
    {
        try {
            $mikrotik = new MikrotikService();
            
            // Get comprehensive system information
            $systemInfo = [];
            
            // System identity
            $systemInfo['identity'] = $mikrotik->getSystemIdentity();
            
            // System resources
            $systemInfo['resources'] = $mikrotik->getSystemResource();
            
            // System clock
            $systemInfo['clock'] = $mikrotik->getSystemClock();
            
            // RouterBoard info
            $systemInfo['routerboard'] = $mikrotik->getSystemRouterBoard();
            
            // Interfaces
            $systemInfo['interfaces'] = $mikrotik->getInterfaces();
            
            // IP addresses
            $systemInfo['addresses'] = $mikrotik->getIpAddresses();
            
            // Hotspot servers
            $systemInfo['hotspots'] = $mikrotik->getHotspotServers();
            
            // Hotspot profiles
            $systemInfo['hotspot_profiles'] = $mikrotik->getHotspotProfiles();
            
            // Active users
            $systemInfo['active_users'] = $mikrotik->getActiveHotspotUsers();
            
            // System users
            $systemInfo['system_users'] = $mikrotik->getSystemUsers();
            
            $mikrotik->disconnect();
            
            return response()->json($systemInfo);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDeviceInfo()
    {
        try {
            $mikrotik = new MikrotikService();
            
            // Get device specific information
            $deviceInfo = [];
            
            // Basic device info
            $deviceInfo['identity'] = $mikrotik->getSystemIdentity();
            $deviceInfo['resources'] = $mikrotik->getSystemResource();
            $deviceInfo['routerboard'] = $mikrotik->getSystemRouterBoard();
            $deviceInfo['clock'] = $mikrotik->getSystemClock();
            
            $mikrotik->disconnect();
            
            return response()->json($deviceInfo);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function users()
    {
        try {
            $mikrotik = new MikrotikService();
            $users = $mikrotik->getHotspotUsers();
            $mikrotik->disconnect();
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'count' => count($users)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function active()
    {
        try {
            $mikrotik = new MikrotikService();
            $activeUsers = $mikrotik->getActiveHotspotUsers();
            $mikrotik->disconnect();
            
            return response()->json([
                'success' => true,
                'data' => $activeUsers,
                'count' => count($activeUsers)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function parseResponse(array $response): array
    {
        $result = [];
        $current = [];

        foreach ($response as $line) {
            if ($line === '!re') {
                if (!empty($current)) {
                    $result[] = $current;
                }
                $current = [];
            } elseif (str_starts_with($line, '=')) {
                $equalPos = strpos($line, '=', 1);
                if ($equalPos !== false) {
                    $key = substr($line, 1, $equalPos - 1);
                    $value = substr($line, $equalPos + 1);
                    $current[$key] = $value;
                }
            } elseif ($line === '!done') {
                if (!empty($current)) {
                    $result[] = $current;
                }
                break;
            }
        }

        return $result;
    }
}
