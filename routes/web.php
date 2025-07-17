<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\HotspotController;


Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::get('/hotspot', [HotspotController::class, 'index'])->name('hotspot.index');
Route::post('/hotspot/kick', [HotspotController::class, 'kick'])->name('hotspot.kick');

// Hotspot Users Management
Route::get('/hotspot/users', [HotspotController::class, 'users'])->name('hotspot.users');
Route::post('/hotspot/users', [HotspotController::class, 'storeUser'])->name('hotspot.users.store');
Route::put('/hotspot/users/{id}', [HotspotController::class, 'updateUser'])->name('hotspot.users.update');
Route::delete('/hotspot/users/{id}', [HotspotController::class, 'deleteUser'])->name('hotspot.users.delete');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
