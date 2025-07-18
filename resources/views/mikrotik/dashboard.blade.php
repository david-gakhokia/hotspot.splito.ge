<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroTik Hotspot Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold">🔥 MikroTik Hotspot Dashboard</h1>
                    <div class="text-sm">
                        <span class="bg-green-500 px-3 py-1 rounded-full">✅ Connected</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-2">👥 Hotspot Users</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $userCount ?? 0 }}</p>
                    <a href="/mikrotik/users" class="text-blue-500 hover:underline">View All →</a>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-2">🔥 Active Sessions</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $activeCount ?? 0 }}</p>
                    <a href="/mikrotik/active" class="text-blue-500 hover:underline">View Active →</a>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-2">📊 System Info</h3>
                    <p class="text-sm text-gray-600">{{ $deviceName ?? 'MikroTik' }}</p>
                    <a href="/mikrotik/system-info" class="text-blue-500 hover:underline">Details →</a>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-2">🚪 Login Portal</h3>
                    <p class="text-sm text-gray-600">WiFi Authentication</p>
                    <a href="/hotspot/login" class="text-blue-500 hover:underline">Access →</a>
                </div>
            </div>

            <!-- Test Credentials -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">🔑 Test Credentials</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-blue-600">Admin Account</h4>
                        <p class="text-sm">Username: <code class="bg-gray-200 px-2 py-1 rounded">admin</code></p>
                        <p class="text-sm">Password: <code class="bg-gray-200 px-2 py-1 rounded">admin123</code></p>
                        <span class="text-xs text-green-600">1 Day Access</span>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-purple-600">Test User</h4>
                        <p class="text-sm">Username: <code class="bg-gray-200 px-2 py-1 rounded">test</code></p>
                        <p class="text-sm">Password: <code class="bg-gray-200 px-2 py-1 rounded">test123</code></p>
                        <span class="text-xs text-blue-600">1 Hour Access</span>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-orange-600">Guest User</h4>
                        <p class="text-sm">Username: <code class="bg-gray-200 px-2 py-1 rounded">guest</code></p>
                        <p class="text-sm">Password: <code class="bg-gray-200 px-2 py-1 rounded">guest123</code></p>
                        <span class="text-xs text-blue-600">1 Hour Access</span>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-semibold text-red-600">Demo Account</h4>
                        <p class="text-sm">Username: <code class="bg-gray-200 px-2 py-1 rounded">demo</code></p>
                        <p class="text-sm">Password: <code class="bg-gray-200 px-2 py-1 rounded">demo2025</code></p>
                        <span class="text-xs text-green-600">1 Day Access</span>
                    </div>
                </div>
            </div>

            <!-- API Endpoints -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">🔗 API Endpoints</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold mb-2">User Management</h4>
                        <ul class="space-y-1 text-sm">
                            <li><a href="/mikrotik/users" class="text-blue-500 hover:underline">GET /mikrotik/users</a> - All users</li>
                            <li><a href="/mikrotik/active" class="text-blue-500 hover:underline">GET /mikrotik/active</a> - Active sessions</li>
                            <li class="text-gray-500">POST /mikrotik/users - Create user</li>
                            <li class="text-gray-500">DELETE /mikrotik/users/{id} - Delete user</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-2">System Information</h4>
                        <ul class="space-y-1 text-sm">
                            <li><a href="/mikrotik/api" class="text-blue-500 hover:underline">GET /mikrotik/api</a> - Complete system data</li>
                            <li><a href="/mikrotik/system-info" class="text-blue-500 hover:underline">GET /mikrotik/system-info</a> - Device info</li>
                            <li><a href="/mikrotik/device-info" class="text-blue-500 hover:underline">GET /mikrotik/device-info</a> - Hardware info</li>
                            <li><a href="/mikrotik/test-connection" class="text-blue-500 hover:underline">GET /mikrotik/test-connection</a> - Connection test</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Interactive API Tester -->
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="/mikrotik/test-api" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg inline-block text-center">
                            🧪 Interactive API Tester
                        </a>
                        <a href="/mikrotik/realtime" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg inline-block text-center">
                            📊 Real-time Dashboard
                        </a>
                        <a href="/hotspot/management" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg inline-block text-center">
                            🔥 Hotspot Management
                        </a>
                        <a href="/docs/mikrotik" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg inline-block text-center">
                            📖 დოკუმენტაცია
                        </a>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Test all endpoints with real-time responses, charts, live monitoring, WiFi management და სრული დოკუმენტაცია.</p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white text-center py-4 mt-12">
            <p>&copy; 2025 MikroTik Hotspot Management System | Built with Laravel & MikroTik API</p>
        </footer>
    </div>
</body>
</html>
