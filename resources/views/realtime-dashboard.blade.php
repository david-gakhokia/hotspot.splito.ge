<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroTik Real-time Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div x-data="dashboard()" x-init="init()" class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold">🔥 MikroTik Real-time Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span x-text="'Last Update: ' + lastUpdate" class="text-sm"></span>
                        <div :class="connectionStatus ? 'bg-green-500' : 'bg-red-500'" class="px-3 py-1 rounded-full text-sm">
                            <span x-text="connectionStatus ? '✅ Connected' : '❌ Disconnected'"></span>
                        </div>
                        <button @click="toggleAutoUpdate()" :class="autoUpdate ? 'bg-green-500' : 'bg-gray-500'" class="px-3 py-1 rounded text-sm">
                            <span x-text="autoUpdate ? '⏸️ Pause' : '▶️ Start'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white rounded-lg shadow-md p-6 transform transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-600">Total Users</h3>
                            <p x-text="stats.totalUsers" class="text-3xl font-bold text-blue-600">-</p>
                        </div>
                        <div class="text-4xl">👥</div>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="bg-white rounded-lg shadow-md p-6 transform transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-600">Active Sessions</h3>
                            <p x-text="stats.activeSessions" class="text-3xl font-bold text-green-600">-</p>
                        </div>
                        <div class="text-4xl">🔥</div>
                    </div>
                </div>

                <!-- System Uptime -->
                <div class="bg-white rounded-lg shadow-md p-6 transform transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-600">System Uptime</h3>
                            <p x-text="stats.uptime" class="text-xl font-bold text-purple-600">-</p>
                        </div>
                        <div class="text-4xl">⏰</div>
                    </div>
                </div>

                <!-- CPU Usage -->
                <div class="bg-white rounded-lg shadow-md p-6 transform transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-600">CPU Usage</h3>
                            <p x-text="stats.cpuUsage + '%'" class="text-3xl font-bold text-orange-600">-</p>
                        </div>
                        <div class="text-4xl">💻</div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">User Activity (Last 10 Updates)</h3>
                    <canvas id="userChart" width="400" height="200"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Session Status</h3>
                    <canvas id="sessionChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Live Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Active Users Table -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">🔥 Active Sessions</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left">User</th>
                                    <th class="px-4 py-2 text-left">IP Address</th>
                                    <th class="px-4 py-2 text-left">Uptime</th>
                                    <th class="px-4 py-2 text-left">Traffic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="session in activeUsers" :key="session['.id']">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2" x-text="session.user"></td>
                                        <td class="px-4 py-2" x-text="session.address"></td>
                                        <td class="px-4 py-2" x-text="session.uptime || '-'"></td>
                                        <td class="px-4 py-2">
                                            <div class="text-xs">
                                                <div>↓ <span x-text="formatBytes(session['bytes-in'])"></span></div>
                                                <div>↑ <span x-text="formatBytes(session['bytes-out'])"></span></div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="activeUsers.length === 0">
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No active sessions</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- All Users Table -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">👥 All Users</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left">Username</th>
                                    <th class="px-4 py-2 text-left">Profile</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="user in allUsers" :key="user['.id']">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium" x-text="user.name"></td>
                                        <td class="px-4 py-2" x-text="user.profile"></td>
                                        <td class="px-4 py-2">
                                            <span :class="user.disabled === 'true' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'" class="px-2 py-1 rounded-full text-xs">
                                                <span x-text="user.disabled === 'true' ? 'Disabled' : 'Enabled'"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="allUsers.length === 0">
                                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">No users found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                <h3 class="text-lg font-semibold mb-4">🖥️ System Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-medium text-gray-600">Device</h4>
                        <p x-text="systemInfo.identity?.name || '-'" class="font-bold"></p>
                        <p x-text="systemInfo.routerboard?.model || '-'" class="text-sm text-gray-500"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-medium text-gray-600">Version</h4>
                        <p x-text="systemInfo.resources?.version || '-'" class="font-bold"></p>
                        <p x-text="systemInfo.resources?.architecture || '-'" class="text-sm text-gray-500"></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <h4 class="font-medium text-gray-600">Memory</h4>
                        <p x-text="formatBytes(systemInfo.resources?.['total-memory']) || '-'" class="font-bold"></p>
                        <p x-text="formatBytes(systemInfo.resources?.['free-memory']) + ' free' || '-'" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboard() {
            return {
                connectionStatus: false,
                autoUpdate: false,
                lastUpdate: '',
                updateInterval: null,
                
                stats: {
                    totalUsers: 0,
                    activeSessions: 0,
                    uptime: '-',
                    cpuUsage: 0
                },
                
                allUsers: [],
                activeUsers: [],
                systemInfo: {},
                
                userChart: null,
                sessionChart: null,
                chartData: {
                    users: [],
                    active: [],
                    labels: []
                },

                async init() {
                    this.initCharts();
                    await this.updateData();
                    this.startAutoUpdate();
                },

                async updateData() {
                    try {
                        // Fetch all data in parallel
                        const [usersResponse, activeResponse, systemResponse] = await Promise.all([
                            fetch('/mikrotik/users'),
                            fetch('/mikrotik/active'),
                            fetch('/mikrotik/api')
                        ]);

                        if (usersResponse.ok) {
                            const usersData = await usersResponse.json();
                            this.allUsers = usersData.data || [];
                            this.stats.totalUsers = usersData.count || 0;
                        }

                        if (activeResponse.ok) {
                            const activeData = await activeResponse.json();
                            this.activeUsers = activeData.data || [];
                            this.stats.activeSessions = activeData.count || 0;
                        }

                        if (systemResponse.ok) {
                            this.systemInfo = await systemResponse.json();
                            this.stats.uptime = this.systemInfo.resources?.uptime || '-';
                            this.stats.cpuUsage = parseFloat(this.systemInfo.resources?.['cpu-load'] || 0);
                        }

                        this.updateCharts();
                        this.connectionStatus = true;
                        this.lastUpdate = new Date().toLocaleTimeString();

                    } catch (error) {
                        console.error('Update failed:', error);
                        this.connectionStatus = false;
                    }
                },

                initCharts() {
                    // User Activity Chart
                    const userCtx = document.getElementById('userChart').getContext('2d');
                    this.userChart = new Chart(userCtx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Total Users',
                                data: [],
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }, {
                                label: 'Active Sessions',
                                data: [],
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    // Session Status Chart
                    const sessionCtx = document.getElementById('sessionChart').getContext('2d');
                    this.sessionChart = new Chart(sessionCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Active', 'Idle'],
                            datasets: [{
                                data: [0, 0],
                                backgroundColor: ['#22c55e', '#e5e7eb']
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });
                },

                updateCharts() {
                    const now = new Date().toLocaleTimeString();
                    
                    // Update line chart
                    this.chartData.labels.push(now);
                    this.chartData.users.push(this.stats.totalUsers);
                    this.chartData.active.push(this.stats.activeSessions);

                    // Keep only last 10 data points
                    if (this.chartData.labels.length > 10) {
                        this.chartData.labels.shift();
                        this.chartData.users.shift();
                        this.chartData.active.shift();
                    }

                    this.userChart.data.labels = this.chartData.labels;
                    this.userChart.data.datasets[0].data = this.chartData.users;
                    this.userChart.data.datasets[1].data = this.chartData.active;
                    this.userChart.update();

                    // Update doughnut chart
                    const activeCount = this.stats.activeSessions;
                    const totalCount = this.stats.totalUsers;
                    const idleCount = totalCount - activeCount;

                    this.sessionChart.data.datasets[0].data = [activeCount, idleCount];
                    this.sessionChart.update();
                },

                startAutoUpdate() {
                    this.autoUpdate = true;
                    this.updateInterval = setInterval(() => {
                        if (this.autoUpdate) {
                            this.updateData();
                        }
                    }, 5000); // Update every 5 seconds
                },

                toggleAutoUpdate() {
                    this.autoUpdate = !this.autoUpdate;
                    if (!this.autoUpdate && this.updateInterval) {
                        clearInterval(this.updateInterval);
                    } else if (this.autoUpdate) {
                        this.startAutoUpdate();
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
