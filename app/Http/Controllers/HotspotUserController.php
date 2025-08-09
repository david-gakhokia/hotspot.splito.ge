<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HotspotUserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = DB::table('hotspot_users')->get()->map(function($item) {
            return (array) $item;
        });
        return view('hotspot.users', compact('users'));
    }

    public function offer()
    {
    
        return view('hotspot.offer');
    }

    public function home()
    {
    
        return view('hotspot.home');
    }


    // Show create form
    public function create()
    {
        return view('hotspot.users.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $data = $request->validate([
            'mac_address' => 'required|string|max:17|unique:hotspot_users,mac_address',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);
        DB::table('hotspot_users')->insert($data);
        return redirect()->route('hotspot.users')->with('success', 'User added successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $user = (array) DB::table('hotspot_users')->where('id', $id)->first();
        if (!$user) {
            return redirect()->route('hotspot.users')->with('error', 'User not found!');
        }
        return view('hotspot.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'mac_address' => 'required|string|max:17|unique:hotspot_users,mac_address,' . $id,
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);
        DB::table('hotspot_users')->where('id', $id)->update($data);
        return redirect()->route('hotspot.users')->with('success', 'User updated successfully!');
    }

    // Delete user
    public function destroy($id)
    {
        DB::table('hotspot_users')->where('id', $id)->delete();
        return redirect()->route('hotspot.users')->with('success', 'User deleted successfully!');
    }
}
