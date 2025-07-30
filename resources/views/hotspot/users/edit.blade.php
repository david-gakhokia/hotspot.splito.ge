@extends('layouts.app')

@section('content')
<div class="container max-w-lg mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-6">Edit Hotspot User</h2>
    <form method="POST" action="{{ route('hotspot.users.update', $user['id']) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">MAC Address</label>
            <input type="text" name="mac_address" value="{{ $user['mac_address'] }}" class="w-full border rounded px-3 py-2" required maxlength="17">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Username</label>
            <input type="text" name="username" value="{{ $user['username'] }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Password <span class="text-gray-500">(leave empty to keep current)</span></label>
            <input type="text" name="password" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="active" @if($user['status']==='active') selected @endif>Active</option>
                <option value="inactive" @if($user['status']==='inactive') selected @endif>Inactive</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="{{ route('hotspot.users') }}" class="ml-4 text-gray-600">Back</a>
    </form>
</div>
@endsection
