<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class HotspotController extends Controller
{
    protected MikrotikService $mikrotik;

    public function __construct(MikrotikService $mikrotik)
    {
        $this->mikrotik = $mikrotik;
    }

    public function index()
    {
        try {
            $users = $this->mikrotik->getActiveHotspotUsers();
            return view('hotspot.index', compact('users'));
        } catch (\Exception $e) {
            return view('hotspot.index', ['users' => []])
                ->with('error', 'MikroTik connection failed: ' . $e->getMessage());
        }
    }

    public function kick(Request $request)
    {
        try {
            $id = $request->input('id');
            $this->mikrotik->kickUserById($id);
            return redirect()->route('hotspot.index')->with('success', 'User kicked successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.index')->with('error', 'Failed to kick user: ' . $e->getMessage());
        }
    }

    public function users()
    {
        try {
            $users = $this->mikrotik->getHotspotUsers();
            $profiles = $this->mikrotik->getHotspotProfiles();
            
            Log::info('Controller Users Count: ' . count($users));
            Log::info('Controller Profiles Count: ' . count($profiles));
            
            return view('hotspot.users', compact('users', 'profiles'));
        } catch (\Exception $e) {
            Log::error('Hotspot Users Error: ' . $e->getMessage());
            return view('hotspot.users', ['users' => [], 'profiles' => []])
                ->with('error', 'MikroTik connection failed: ' . $e->getMessage());
        }
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:4|max:255',
            'profile' => 'required|string|max:255',
            'comment' => 'nullable|string|max:255'
        ]);

        try {
            $this->mikrotik->addHotspotUser(
                $request->name,
                $request->password,
                $request->profile,
                $request->comment
            );
            return redirect()->route('hotspot.users')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.users')->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:4|max:255',
            'profile' => 'required|string|max:255',
            'comment' => 'nullable|string|max:255'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'profile' => $request->profile,
                'comment' => $request->comment ?? ''
            ];

            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            $this->mikrotik->updateHotspotUser($id, $data);
            return redirect()->route('hotspot.users')->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.users')->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function deleteUser(string $id)
    {
        try {
            $this->mikrotik->deleteHotspotUser($id);
            return redirect()->route('hotspot.users')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('hotspot.users')->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
