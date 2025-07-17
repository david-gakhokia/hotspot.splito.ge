<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\HotspotServerController;
use App\Http\Controllers\LoginPageDesignerController;


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

// Hotspot Server Management
Route::get('/hotspot/server', [HotspotServerController::class, 'index'])->name('hotspot.server.index');
Route::post('/hotspot/server/enable', [HotspotServerController::class, 'enable'])->name('hotspot.server.enable');
Route::post('/hotspot/server/disable', [HotspotServerController::class, 'disable'])->name('hotspot.server.disable');
Route::post('/hotspot/server/update-profile', [HotspotServerController::class, 'updateProfile'])->name('hotspot.server.update-profile');
Route::post('/hotspot/server/set-login-page', [HotspotServerController::class, 'setLoginPage'])->name('hotspot.server.set-login-page');

// Login Page Designer
Route::get('/hotspot/login-designer', [LoginPageDesignerController::class, 'index'])->name('hotspot.login-designer.index');
Route::post('/hotspot/login-designer/preview', [LoginPageDesignerController::class, 'preview'])->name('hotspot.login-designer.preview');
Route::post('/hotspot/login-designer/customize', [LoginPageDesignerController::class, 'customize'])->name('hotspot.login-designer.customize');


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
