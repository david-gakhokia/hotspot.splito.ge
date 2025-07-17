<?php

namespace App\Http\Controllers;

use App\Services\MikrotikService;
use Illuminate\Http\Request;

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
}
