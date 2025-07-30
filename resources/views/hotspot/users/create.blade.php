@extends('layouts.app')

@section('content')
<div class="container max-w-lg mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">Add Hotspot User</h2>
    <form method="POST" action="{{ route('hotspot.users.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">MAC Address</label>
            <input type="text" name="mac_address" class="w-full border rounded px-3 py-2" required maxlength="17">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Username</label>
            <input type="text" name="username" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Password</label>
            <input type="text" name="password" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
        <a href="{{ route('hotspot.users') }}" class="ml-4 text-gray-600">Back</a>
    </form>
</div>
@endsection
