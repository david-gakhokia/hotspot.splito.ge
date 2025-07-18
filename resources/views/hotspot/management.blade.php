<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotspot Management | WiFi სერვისის მართვა</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <div x-data="hotspotManagement()" x-init="init()" class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-green-600 to-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold">🔥 Hotspot Management</h1>
                    <div class="flex items-center space-x-4">
                        <span x-text="'Last Update: ' + lastUpdate" class="text-sm"></span>
                        <div :class="serviceStatus ? 'bg-green-500' : 'bg-red-500'" class="px-3 py-1 rounded-full text-sm">
                            <span x-text="serviceStatus ? '✅ Service Active' : '❌ Service Inactive'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Service Control Panel -->
        <div class="container mx-auto px-4 py-8">
            <!-- Service Status -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">🎛️ Service Control</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-green-800 mb-2">Service Status</h3>
                        <p x-text="serviceStatus ? 'Active' : 'Inactive'" class="text-2xl font-bold" :class="serviceStatus ? 'text-green-600' : 'text-red-600'"></p>
                        <p class="text-sm text-gray-600">Hotspot Authentication</p>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-800 mb-2">Active Sessions</h3>
                        <p x-text="stats.activeSessions" class="text-2xl font-bold text-blue-600">0</p>
                        <p class="text-sm text-gray-600">Connected Users</p>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-purple-800 mb-2">Total Users</h3>
                        <p x-text="stats.totalUsers" class="text-2xl font-bold text-purple-600">0</p>
                        <p class="text-sm text-gray-600">Registered</p>
                    </div>
                </div>
                
                <div class="mt-6 flex space-x-4">
                    <button @click="enableService()" :disabled="serviceStatus" class="bg-green-500 hover:bg-green-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded">
                        🚀 Enable Service
                    </button>
                    <button @click="disableService()" :disabled="!serviceStatus" class="bg-red-500 hover:bg-red-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded">
                        ⏹️ Disable Service
                    </button>
                    <button @click="restartService()" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        🔄 Restart Service
                    </button>
                </div>
            </div>

            <!-- Quick Access -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <a href="/hotspot/login" target="_blank" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="text-center">
                        <div class="text-4xl mb-3">🚪</div>
                        <h3 class="font-semibold text-gray-800">Login Portal</h3>
                        <p class="text-sm text-gray-600">WiFi Authentication</p>
                    </div>
                </a>
                
                <a href="/mikrotik/realtime" target="_blank" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="text-center">
                        <div class="text-4xl mb-3">📊</div>
                        <h3 class="font-semibold text-gray-800">Real-time Monitor</h3>
                        <p class="text-sm text-gray-600">Live Statistics</p>
                    </div>
                </a>
                
                <a href="/mikrotik/users" target="_blank" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="text-center">
                        <div class="text-4xl mb-3">👥</div>
                        <h3 class="font-semibold text-gray-800">User Management</h3>
                        <p class="text-sm text-gray-600">Manage Users</p>
                    </div>
                </a>
                
                <a href="/docs/mikrotik" target="_blank" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                    <div class="text-center">
                        <div class="text-4xl mb-3">📖</div>
                        <h3 class="font-semibold text-gray-800">Documentation</h3>
                        <p class="text-sm text-gray-600">Setup Guide</p>
                    </div>
                </a>
            </div>

            <!-- Active Users Table -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">🔥 Active WiFi Sessions</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">User</th>
                                <th class="px-4 py-2 text-left">IP Address</th>
                                <th class="px-4 py-2 text-left">MAC Address</th>
                                <th class="px-4 py-2 text-left">Uptime</th>
                                <th class="px-4 py-2 text-left">Traffic</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="session in activeSessions" :key="session.id">
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium" x-text="session.user"></td>
                                    <td class="px-4 py-2" x-text="session.address"></td>
                                    <td class="px-4 py-2 text-xs" x-text="session.mac"></td>
                                    <td class="px-4 py-2" x-text="session.uptime"></td>
                                    <td class="px-4 py-2">
                                        <div class="text-xs">
                                            <div>↓ <span x-text="formatBytes(session.bytesIn)"></span></div>
                                            <div>↑ <span x-text="formatBytes(session.bytesOut)"></span></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button @click="kickUser(session.id)" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                            Kick
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="activeSessions.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2">📡</div>
                                    <p>No active WiFi sessions</p>
                                    <p class="text-sm">Users will appear here when they connect via hotspot</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Configuration Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">⚙️ Hotspot Configuration</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-3">🌐 Network Settings</h3>
                        <div class="bg-gray-50 p-4 rounded text-sm space-y-1">
                            <p><strong>Network:</strong> 192.168.88.0/24</p>
                            <p><strong>Gateway:</strong> 192.168.88.1</p>
                            <p><strong>DHCP Pool:</strong> 192.168.88.10-100</p>
                            <p><strong>Interface:</strong> bridge</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-3">🔗 Access URLs</h3>
                        <div class="bg-gray-50 p-4 rounded text-sm space-y-1">
                            <p><strong>Login Portal:</strong></p>
                            <a href="/hotspot/login" class="text-blue-600 hover:underline block">https://hotspot.splito.ge.test/hotspot/login</a>
                            <p class="mt-2"><strong>Welcome Page:</strong></p>
                            <a href="/hotspot/welcome" class="text-blue-600 hover:underline block">https://hotspot.splito.ge.test/hotspot/welcome</a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="font-semibold mb-3">🔑 Test Accounts</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-3 rounded">
                            <h4 class="font-medium text-blue-800">Admin</h4>
                            <p class="text-xs">admin / admin123</p>
                            <span class="text-xs text-green-600">1 Day Access</span>
                        </div>
                        <div class="bg-purple-50 p-3 rounded">
                            <h4 class="font-medium text-purple-800">Test</h4>
                            <p class="text-xs">test / test123</p>
                            <span class="text-xs text-blue-600">1 Hour Access</span>
                        </div>
                        <div class="bg-orange-50 p-3 rounded">
                            <h4 class="font-medium text-orange-800">Guest</h4>
                            <p class="text-xs">guest / guest123</p>
                            <span class="text-xs text-blue-600">1 Hour Access</span>
                        </div>
                        <div class="bg-red-50 p-3 rounded">
                            <h4 class="font-medium text-red-800">Demo</h4>
                            <p class="text-xs">demo / demo2025</p>
                            <span class="text-xs text-green-600">1 Day Access</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function hotspotManagement() {
            return {
                serviceStatus: true,
                lastUpdate: '',
                stats: {
                    activeSessions: 0,
                    totalUsers: 0
                },
                activeSessions: [],

                async init() {
                    await this.updateData();
                    this.startAutoUpdate();
                },

                async updateData() {
                    try {
                        const [usersResponse, activeResponse, statusResponse] = await Promise.all([
                            fetch('/mikrotik/users'),
                            fetch('/mikrotik/active'),
                            fetch('/hotspot/status')
                        ]);

                        if (usersResponse.ok) {
                            const usersData = await usersResponse.json();
                            this.stats.totalUsers = usersData.count || 0;
                        }

                        if (activeResponse.ok) {
                            const activeData = await activeResponse.json();
                            this.activeSessions = activeData.data || [];
                            this.stats.activeSessions = activeData.count || 0;
                        }

                        if (statusResponse.ok) {
                            const statusData = await statusResponse.json();
                            this.serviceStatus = statusData.success && statusData.data.status === 'online';
                        }

                        this.lastUpdate = new Date().toLocaleTimeString();
                    } catch (error) {
                        console.error('Update failed:', error);
                        this.serviceStatus = false;
                    }
                },

                startAutoUpdate() {
                    setInterval(() => {
                        this.updateData();
                    }, 10000); // Update every 10 seconds
                },

                async enableService() {
                    try {
                        // In real implementation, this would enable the hotspot service
                        alert('🚀 Hotspot service would be enabled via MikroTik API');
                        this.serviceStatus = true;
                    } catch (error) {
                        alert('Error enabling service: ' + error.message);
                    }
                },

                async disableService() {
                    if (confirm('Are you sure you want to disable the hotspot service?')) {
                        try {
                            // In real implementation, this would disable the hotspot service
                            alert('⏹️ Hotspot service would be disabled via MikroTik API');
                            this.serviceStatus = false;
                        } catch (error) {
                            alert('Error disabling service: ' + error.message);
                        }
                    }
                },

                async restartService() {
                    if (confirm('Are you sure you want to restart the hotspot service?')) {
                        try {
                            // In real implementation, this would restart the hotspot service
                            alert('🔄 Hotspot service would be restarted via MikroTik API');
                            await this.updateData();
                        } catch (error) {
                            alert('Error restarting service: ' + error.message);
                        }
                    }
                },

                async kickUser(sessionId) {
                    if (confirm('Are you sure you want to disconnect this user?')) {
                        try {
                            const response = await fetch('/hotspot/kick', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ id: sessionId })
                            });

                            if (response.ok) {
                                alert('User disconnected successfully');
                                await this.updateData();
                            } else {
                                alert('Failed to disconnect user');
                            }
                        } catch (error) {
                            alert('Error: ' + error.message);
                        }
                    }
                },

                formatBytes(bytes) {
                    if (!bytes || bytes === '0') return '0 B';
                    const k = 1024;
                    const sizes = ['B', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }
        }
    </script>
</body>
</html>
