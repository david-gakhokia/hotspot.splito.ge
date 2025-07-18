<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HotspotAuthController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    /**
     * Show hotspot login page
     */
    public function loginPage(Request $request)
    {
        // Get parameters from MikroTik redirect
        $username = $request->get('username', '');
        $dst = $request->get('dst', '');
        $popup = $request->get('popup', '');
        
        Log::info('Hotspot login page accessed', [
            'username' => $username,
            'dst' => $dst,
            'popup' => $popup,
            'ip' => $request->ip()
        ]);

        return view('hotspot.login', compact('username', 'dst', 'popup'));
    }

    /**
     * Handle hotspot authentication
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data'
            ], 400);
        }

        $username = $request->input('username');
        $password = $request->input('password');
        $dst = $request->input('dst', '/hotspot/welcome');

        Log::info('Hotspot authentication attempt', [
            'username' => $username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // Verify user credentials against MikroTik
            $authenticated = $this->verifyUserCredentials($username, $password);
            
            if ($authenticated) {
                Log::info('Hotspot authentication successful', ['username' => $username]);
                
                // In a real MikroTik setup, this would be handled by RouterOS
                // For now, we simulate the authentication process
                
                return response()->json([
                    'success' => true,
                    'message' => 'Authentication successful',
                    'redirect' => $dst,
                    'username' => $username
                ]);
            } else {
                Log::warning('Hotspot authentication failed', ['username' => $username]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid username or password'
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error('Hotspot authentication error', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication service error'
            ], 500);
        }
    }

    /**
     * Verify user credentials against MikroTik
     */
    private function verifyUserCredentials(string $username, string $password): bool
    {
        try {
            // Get all hotspot users from MikroTik
            $users = $this->mikrotik->getHotspotUsers();
            
            foreach ($users as $user) {
                if (isset($user['name']) && $user['name'] === $username) {
                    // Check if user exists and is enabled
                    if (!isset($user['disabled']) || $user['disabled'] !== 'true') {
                        // In real implementation, password verification would be done by MikroTik
                        // For demonstration, we check against known test users
                        return $this->checkTestUserPassword($username, $password);
                    }
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error verifying user credentials', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check test user passwords (for demonstration)
     */
    private function checkTestUserPassword(string $username, string $password): bool
    {
        $testUsers = [
            'admin' => 'admin123',
            'test' => 'test123',
            'guest' => 'guest123',
            'demo' => 'demo2025'
        ];

        return isset($testUsers[$username]) && $testUsers[$username] === $password;
    }

    /**
     * Welcome page after successful login
     */
    public function welcome(Request $request)
    {
        $username = $request->get('username', 'Guest');
        
        return view('hotspot.welcome', compact('username'));
    }

    /**
     * Logout from hotspot
     */
    public function logout(Request $request)
    {
        $username = $request->get('username', '');
        
        Log::info('Hotspot logout', ['username' => $username, 'ip' => $request->ip()]);
        
        try {
            // In real MikroTik setup, this would terminate the user session
            // For now, we just log the logout event
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => '/hotspot/login'
            ]);
        } catch (\Exception $e) {
            Log::error('Hotspot logout error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Logout error'
            ], 500);
        }
    }

    /**
     * Get hotspot status
     */
    public function status(Request $request)
    {
        try {
            $activeUsers = $this->mikrotik->getActiveHotspotUsers();
            $allUsers = $this->mikrotik->getHotspotUsers();
            $servers = $this->mikrotik->getHotspotServers();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'active_users' => count($activeUsers),
                    'total_users' => count($allUsers),
                    'servers' => count($servers),
                    'status' => 'online'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to get hotspot status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
