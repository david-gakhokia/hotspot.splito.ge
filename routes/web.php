
<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\HotspotController;
use App\Http\Controllers\HotspotServerController;
use App\Http\Controllers\LoginPageDesignerController;
use App\Http\Controllers\MikrotikTestController;
use App\Http\Controllers\HotspotAuthController;
use App\Http\Controllers\WiFiConfigController;
use App\Http\Controllers\MikrotikFileController;
use App\Http\Controllers\HotspotUserController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;



Route::get('/', function () {
    return view('welcome');
})->name('home');

// Hotspot Test Route
Route::get('/test-hotspot', function () {
    return response()->file(public_path('test-hotspot.html'));
})->name('test.hotspot');

// Hotspot Authentication Routes
// Route::get('/hotspot/login', [HotspotAuthController::class, 'loginPage'])->name('hotspot.login');
Route::post('/hotspot/authenticate', [HotspotAuthController::class, 'authenticate'])->name('hotspot.authenticate');
Route::get('/hotspot/welcome', [HotspotAuthController::class, 'welcome'])->name('hotspot.welcome');
Route::post('/hotspot/logout', [HotspotAuthController::class, 'logout'])->name('hotspot.logout');
Route::get('/hotspot/status', [HotspotAuthController::class, 'status'])->name('hotspot.status');


// Inline Hotspot Login Routes
Route::get('/hotspot/login', function (Request $request) {
    return view('hotspot.login', [
        'params' => $request->all()
    ]);
});

Route::post('/hotspot/login', function (Request $request) {
    $response = Http::asForm()->post($request->input('login_url'), [
        'username' => $request->input('username'),
        'password' => $request->input('password'),
    ]);

    return redirect($request->input('link_orig') ?? 'https://invest.mardi.ge/offer');
});


// Hotspot Users Resource Routes
Route::get('/hotspot/users', [HotspotUserController::class, 'index'])->name('hotspot.users');
Route::get('/hotspot/users/create', [HotspotUserController::class, 'create'])->name('hotspot.users.create');
Route::get('/hotspot/users/{id}/edit', [HotspotUserController::class, 'edit'])->name('hotspot.users.edit');
Route::post('/hotspot/users', [HotspotUserController::class, 'store'])->name('hotspot.users.store');
Route::put('/hotspot/users/{id}', [HotspotUserController::class, 'update'])->name('hotspot.users.update');
Route::delete('/hotspot/users/{id}', [HotspotUserController::class, 'destroy'])->name('hotspot.users.delete');

// Hotspot Management
Route::get('/hotspot/management', function () {
    return view('hotspot.management');
})->name('hotspot.management');

// WiFi Configuration Routes
Route::get('/api/wifi/config', [WiFiConfigController::class, 'getWiFiConfig'])->name('wifi.config');
Route::post('/api/wifi/update-ssid', [WiFiConfigController::class, 'updateSSID'])->name('wifi.update-ssid');
Route::post('/api/wifi/update-password', [WiFiConfigController::class, 'updatePassword'])->name('wifi.update-password');
Route::get('/api/wifi/security-profiles', [WiFiConfigController::class, 'getSecurityProfiles'])->name('wifi.security-profiles');
Route::post('/api/wifi/toggle', [WiFiConfigController::class, 'toggleWiFi'])->name('wifi.toggle');

// Test WiFi endpoint
Route::get('/test-wifi', function () {
    try {
        $mikrotik = app(\App\Services\MikrotikService::class);
        $interfaces = $mikrotik->getWirelessInterfaces();
        return response()->json([
            'success' => true,
            'interfaces' => $interfaces,
            'count' => count($interfaces)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});



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

// MikroTik Connection Test
Route::get('/mikrotik/test', [MikrotikTestController::class, 'index'])->name('mikrotik.test.index');
Route::post('/mikrotik/test-connection', [MikrotikTestController::class, 'testConnection'])->name('mikrotik.test.connection');
Route::get('/mikrotik/system-info', [MikrotikTestController::class, 'getSystemInfo'])->name('mikrotik.test.system-info');
Route::get('/mikrotik/device-info', [MikrotikTestController::class, 'getDeviceInfo'])->name('mikrotik.test.device-info');

// MikroTik File Management Routes
Route::prefix('mikrotik/files')->name('mikrotik.files.')->group(function () {
    Route::get('/', [MikrotikFileController::class, 'index'])->name('index');
    Route::post('/upload', [MikrotikFileController::class, 'upload'])->name('upload');
    Route::post('/download', [MikrotikFileController::class, 'download'])->name('download');
    Route::delete('/delete', [MikrotikFileController::class, 'delete'])->name('delete');
    Route::post('/upload-hotspot', [MikrotikFileController::class, 'uploadHotspotFiles'])->name('upload-hotspot');
    Route::post('/test-connection', [MikrotikFileController::class, 'testConnection'])->name('test-connection');
    Route::post('/file-info', [MikrotikFileController::class, 'fileInfo'])->name('file-info');
});

// MikroTik API Endpoints
Route::get('/mikrotik/api', [MikrotikTestController::class, 'api'])->name('mikrotik.api');
Route::get('/mikrotik/users', [MikrotikTestController::class, 'users'])->name('mikrotik.users');
Route::get('/mikrotik/active', [MikrotikTestController::class, 'active'])->name('mikrotik.active');

// API Test Page
Route::get('/mikrotik/test-api', function () {
    return view('test-api');
})->name('mikrotik.test-api');

// Real-time Dashboard
Route::get('/mikrotik/realtime', function () {
    return view('realtime-dashboard');
})->name('mikrotik.realtime');

// Documentation
Route::get('/docs/mikrotik', function () {
    return view('docs.mikrotik-system');
})->name('docs.mikrotik');

Route::get('/docs/troubleshooting', function () {
    return view('docs.troubleshooting');
})->name('docs.troubleshooting');


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
