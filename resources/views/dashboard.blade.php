<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-blue-900">
        <div class="container mx-auto px-6 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">
                    🔥 WiFi Hotspot Dashboard
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    WiFi ინტერფეისების მართვა და კონფიგურაცია
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- WiFi Status -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">WiFi Status</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="wifi-status">
                                <span class="text-green-500">●</span> Active
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Connected Devices -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Connected</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white" id="connected-count">12</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Data Usage -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Data Usage</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">2.4 GB</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Sessions</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">8</p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WiFi Configuration Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- WiFi Settings -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                            📡 WiFi Configuration
                        </h2>
                        <button id="refresh-wifi" class="p-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="wifi-interfaces" class="space-y-4">
                        <!-- WiFi interfaces will be loaded here -->
                        <div class="animate-pulse space-y-4">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
                        ⚡ Quick Actions
                    </h2>

                    <div class="space-y-4">
                        <!-- SSID Change -->
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl">
                            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">📶 Network Name (SSID)</h3>
                            <div class="flex space-x-3">
                                <input type="text" id="new-ssid" placeholder="Enter new SSID" 
                                       class="flex-1 px-4 py-2 bg-white/80 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button onclick="updateSSID()" 
                                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    Update
                                </button>
                            </div>
                        </div>

                        <!-- Password Change -->
                        <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl">
                            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">🔐 WiFi Password</h3>
                            <div class="flex space-x-3">
                                <input type="password" id="new-password" placeholder="Enter new password (min 8 chars)" 
                                       class="flex-1 px-4 py-2 bg-white/80 dark:bg-gray-700/80 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <button onclick="updatePassword()" 
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                    Update
                                </button>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="grid grid-cols-2 gap-4">
                            <a href="/hotspot/login" target="_blank" 
                               class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl text-center hover:scale-105 transition-transform">
                                <div class="text-purple-600 dark:text-purple-400 mb-2">🌐</div>
                                <div class="font-medium text-gray-800 dark:text-white">Login Portal</div>
                            </a>
                            <a href="/hotspot/management" target="_blank" 
                               class="p-4 bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl text-center hover:scale-105 transition-transform">
                                <div class="text-orange-600 dark:text-orange-400 mb-2">⚙️</div>
                                <div class="font-medium text-gray-800 dark:text-white">Management</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Toast -->
            <div id="notification" class="fixed top-4 right-4 z-50 hidden">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 max-w-sm">
                    <div class="flex items-center">
                        <div id="notification-icon" class="flex-shrink-0 mr-3"></div>
                        <div id="notification-message" class="text-sm text-gray-700 dark:text-gray-300"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentInterface = null;

        // Load WiFi configuration on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadWiFiConfig();
        });

        // Load WiFi interfaces
        async function loadWiFiConfig() {
            try {
                const response = await fetch('/api/wifi/config');
                const data = await response.json();
                
                if (data.success) {
                    displayWiFiInterfaces(data.wifi_interfaces);
                } else {
                    showNotification('error', data.message || 'Failed to load WiFi configuration');
                }
            } catch (error) {
                showNotification('error', 'Error loading WiFi configuration: ' + error.message);
            }
        }

        // Display WiFi interfaces
        function displayWiFiInterfaces(interfaces) {
            const container = document.getElementById('wifi-interfaces');
            
            if (interfaces.length === 0) {
                container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">No WiFi interfaces found</p>';
                return;
            }

            container.innerHTML = interfaces.map(iface => `
                <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-600/50 rounded-xl">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-800 dark:text-white">${iface.name}</h4>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" ${iface.disabled === 'false' ? 'checked' : ''} 
                                   onchange="toggleWiFi('${iface.name}', this.checked)" 
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">SSID:</span>
                            <span class="font-medium text-gray-800 dark:text-white">${iface.ssid}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Frequency:</span>
                            <span class="text-gray-800 dark:text-white">${iface.frequency}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Security:</span>
                            <span class="text-gray-800 dark:text-white">${iface.security_profile}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            // Set current interface for updates
            if (interfaces.length > 0) {
                currentInterface = interfaces[0].name;
            }
        }

        // Update SSID
        async function updateSSID() {
            const newSSID = document.getElementById('new-ssid').value.trim();
            
            if (!newSSID) {
                showNotification('error', 'SSID cannot be empty');
                return;
            }

            if (!currentInterface) {
                showNotification('error', 'No WiFi interface selected');
                return;
            }

            try {
                const response = await fetch('/api/wifi/update-ssid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        interface: currentInterface,
                        ssid: newSSID
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('success', data.message);
                    document.getElementById('new-ssid').value = '';
                    setTimeout(() => loadWiFiConfig(), 1000);
                } else {
                    showNotification('error', data.message);
                }
            } catch (error) {
                showNotification('error', 'Error updating SSID: ' + error.message);
            }
        }

        // Update Password
        async function updatePassword() {
            const newPassword = document.getElementById('new-password').value.trim();
            
            if (!newPassword) {
                showNotification('error', 'Password cannot be empty');
                return;
            }

            if (newPassword.length < 8) {
                showNotification('error', 'Password must be at least 8 characters');
                return;
            }

            if (!currentInterface) {
                showNotification('error', 'No WiFi interface selected');
                return;
            }

            try {
                const response = await fetch('/api/wifi/update-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        interface: currentInterface,
                        password: newPassword,
                        security_profile: 'default'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('success', data.message);
                    document.getElementById('new-password').value = '';
                } else {
                    showNotification('error', data.message);
                }
            } catch (error) {
                showNotification('error', 'Error updating password: ' + error.message);
            }
        }

        // Toggle WiFi interface
        async function toggleWiFi(interfaceName, enabled) {
            try {
                const response = await fetch('/api/wifi/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        interface: interfaceName,
                        enabled: enabled
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification('success', data.message);
                } else {
                    showNotification('error', data.message);
                }
            } catch (error) {
                showNotification('error', 'Error toggling WiFi: ' + error.message);
            }
        }

        // Show notification
        function showNotification(type, message) {
            const notification = document.getElementById('notification');
            const icon = document.getElementById('notification-icon');
            const messageElement = document.getElementById('notification-message');

            messageElement.textContent = message;

            if (type === 'success') {
                icon.innerHTML = '<div class="w-5 h-5 text-green-500">✓</div>';
            } else {
                icon.innerHTML = '<div class="w-5 h-5 text-red-500">✗</div>';
            }

            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 5000);
        }

        // Refresh WiFi config
        document.getElementById('refresh-wifi').addEventListener('click', loadWiFiConfig);
    </script>
</x-layouts.app>
