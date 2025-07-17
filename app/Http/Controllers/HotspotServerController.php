<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotspotServerController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function index()
    {
        try {
            $servers = $this->mikrotik->getHotspotServers();
            $profiles = $this->mikrotik->getHotspotServerProfiles();
            
            return view('hotspot.server.index', compact('servers', 'profiles'));
        } catch (\Exception $e) {
            Log::error('Hotspot Server Error: ' . $e->getMessage());
            return view('hotspot.server.index', ['servers' => [], 'profiles' => []])
                ->with('error', 'MikroTik connection failed: ' . $e->getMessage());
        }
    }

    public function enable(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'profile' => 'required|string'
        ]);

        try {
            $this->mikrotik->enableHotspotServer(
                $request->interface,
                $request->profile
            );
            
            return redirect()->route('hotspot.server.index')
                ->with('success', 'Hotspot server enabled successfully on ' . $request->interface);
        } catch (\Exception $e) {
            return redirect()->route('hotspot.server.index')
                ->with('error', 'Failed to enable hotspot server: ' . $e->getMessage());
        }
    }

    public function disable(Request $request)
    {
        $request->validate([
            'server_id' => 'required|string'
        ]);

        try {
            $this->mikrotik->disableHotspotServer($request->server_id);
            
            return redirect()->route('hotspot.server.index')
                ->with('success', 'Hotspot server disabled successfully');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.server.index')
                ->with('error', 'Failed to disable hotspot server: ' . $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|string',
            'login_page' => 'nullable|string',
            'html_directory' => 'nullable|string',
            'http_cookie_lifetime' => 'nullable|string',
            'http_proxy' => 'nullable|string',
            'radius_accounting' => 'nullable|string',
            'radius_interim_update' => 'nullable|string',
            'rate_limit' => 'nullable|string',
            'session_timeout' => 'nullable|string',
            'idle_timeout' => 'nullable|string',
            'keepalive_timeout' => 'nullable|string',
            'mac_cookie_timeout' => 'nullable|string',
            'trial_uptime' => 'nullable|string',
            'trial_user_profile' => 'nullable|string'
        ]);

        try {
            $data = array_filter([
                'login-page' => $request->login_page,
                'html-directory' => $request->html_directory,
                'http-cookie-lifetime' => $request->http_cookie_lifetime,
                'http-proxy' => $request->http_proxy,
                'radius-accounting' => $request->radius_accounting,
                'radius-interim-update' => $request->radius_interim_update,
                'rate-limit' => $request->rate_limit,
                'session-timeout' => $request->session_timeout,
                'idle-timeout' => $request->idle_timeout,
                'keepalive-timeout' => $request->keepalive_timeout,
                'mac-cookie-timeout' => $request->mac_cookie_timeout,
                'trial-uptime' => $request->trial_uptime,
                'trial-user-profile' => $request->trial_user_profile
            ], function($value) {
                return !is_null($value) && $value !== '';
            });

            $this->mikrotik->updateHotspotServerProfile($request->profile_id, $data);
            
            return redirect()->route('hotspot.server.index')
                ->with('success', 'Hotspot server profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.server.index')
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function setLoginPage(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|string',
            'login_page' => 'required|string'
        ]);

        try {
            $this->mikrotik->setHotspotLoginPage($request->profile_id, $request->login_page);
            
            return redirect()->route('hotspot.server.index')
                ->with('success', 'Login page updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.server.index')
                ->with('error', 'Failed to update login page: ' . $e->getMessage());
        }
    }
}
