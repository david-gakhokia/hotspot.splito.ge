<x-layouts.app :title="'MikroTik Connection Test'">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="text-red-500">🔧</span>
                        MikroTik Connection Test
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Test and monitor your MikroTik device connection</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $connectionStatus === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <div class="w-3 h-3 rounded-full {{ $connectionStatus === 'connected' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <span class="font-medium">{{ ucfirst($connectionStatus) }}</span>
                    </div>
                    <button onclick="testConnection()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline animate-spin hidden" id="test-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <svg class="w-4 h-4 mr-2 inline" id="test-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Test Connection
                    </button>
                    <button onclick="getSystemInfo()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline animate-spin hidden" id="info-spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <svg class="w-4 h-4 mr-2 inline" id="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Get System Info
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        @if($error)
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 dark:text-red-200 font-medium">{{ $error }}</span>
                </div>
            </div>
        @endif

        <!-- Test Results -->
        <div id="test-results" class="hidden mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Connection Test Results</h2>
                <div id="test-messages" class="space-y-2"></div>
                <div id="test-details" class="mt-4"></div>
            </div>
        </div>

        <!-- System Information Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Device Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Device Information
                </h3>
                <div class="space-y-3">
                    @if(count($deviceInfo) > 0)
                        @foreach($deviceInfo as $info)
                            @if(isset($info['name']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Identity:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $info['name'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No device information available</p>
                    @endif
                </div>
            </div>

            <!-- System Resources -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    System Resources
                </h3>
                <div class="space-y-3">
                    @if(count($systemResources) > 0)
                        @foreach($systemResources as $resource)
                            @if(isset($resource['platform']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Platform:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource['platform'] }}</span>
                                </div>
                            @endif
                            @if(isset($resource['version']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Version:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource['version'] }}</span>
                                </div>
                            @endif
                            @if(isset($resource['cpu']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">CPU:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource['cpu'] }}</span>
                                </div>
                            @endif
                            @if(isset($resource['uptime']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Uptime:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $resource['uptime'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No system resources available</p>
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Current User
                </h3>
                <div class="space-y-3">
                    @if(count($userInfo) > 0)
                        @foreach($userInfo as $user)
                            @if(isset($user['name']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Username:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['name'] }}</span>
                                </div>
                            @endif
                            @if(isset($user['group']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Group:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['group'] }}</span>
                                </div>
                            @endif
                            @if(isset($user['last-logged-in']))
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Last Login:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['last-logged-in'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No user information available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Interfaces -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                        </svg>
                        Network Interfaces
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    @if(count($interfaces) > 0)
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">MAC Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($interfaces as $interface)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $interface['name'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $interface['type'] ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(isset($interface['running']) && $interface['running'] === 'true')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Running</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Stopped</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $interface['mac-address'] ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No interfaces available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hotspot Status -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Hotspot Servers
                    </h3>
                </div>
                <div class="p-6">
                    @if(count($hotspotStatus) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($hotspotStatus as $hotspot)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $hotspot['name'] ?? 'Unknown' }}</h4>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Active</span>
                                    </div>
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Interface:</span>
                                            <span class="text-gray-900 dark:text-white">{{ $hotspot['interface'] ?? '-' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500 dark:text-gray-400">Profile:</span>
                                            <span class="text-gray-900 dark:text-white">{{ $hotspot['profile'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No hotspot servers configured</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Information Display -->
        <div id="system-info" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detailed System Information</h2>
                <div id="system-info-content" class="space-y-4"></div>
            </div>
        </div>
    </div>

    <script>
        function testConnection() {
            const spinner = document.getElementById('test-spinner');
            const icon = document.getElementById('test-icon');
            const results = document.getElementById('test-results');
            const messages = document.getElementById('test-messages');
            
            spinner.classList.remove('hidden');
            icon.classList.add('hidden');
            
            fetch('/mikrotik/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                messages.innerHTML = '';
                data.messages.forEach(message => {
                    const div = document.createElement('div');
                    div.className = 'p-3 rounded-lg ' + (message.startsWith('✅') ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800');
                    div.textContent = message;
                    messages.appendChild(div);
                });
                
                results.classList.remove('hidden');
                
                if (data.details) {
                    const detailsDiv = document.getElementById('test-details');
                    detailsDiv.innerHTML = '<h3 class="font-semibold mb-2">Technical Details:</h3>';
                    Object.keys(data.details).forEach(key => {
                        const detail = document.createElement('details');
                        detail.className = 'mb-2';
                        detail.innerHTML = `
                            <summary class="cursor-pointer font-medium text-gray-700 dark:text-gray-300">${key}</summary>
                            <pre class="mt-2 text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded overflow-x-auto">${JSON.stringify(data.details[key], null, 2)}</pre>
                        `;
                        detailsDiv.appendChild(detail);
                    });
                }
            })
            .catch(error => {
                messages.innerHTML = '<div class="p-3 rounded-lg bg-red-50 text-red-800">❌ Connection test failed: ' + error.message + '</div>';
                results.classList.remove('hidden');
            })
            .finally(() => {
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');
            });
        }

        function getSystemInfo() {
            const spinner = document.getElementById('info-spinner');
            const icon = document.getElementById('info-icon');
            const systemInfo = document.getElementById('system-info');
            const content = document.getElementById('system-info-content');
            
            spinner.classList.remove('hidden');
            icon.classList.add('hidden');
            
            fetch('/mikrotik/system-info')
            .then(response => response.json())
            .then(data => {
                content.innerHTML = '';
                
                if (data.error) {
                    content.innerHTML = '<div class="p-4 bg-red-50 text-red-800 rounded-lg">Error: ' + data.error + '</div>';
                } else {
                    Object.keys(data).forEach(key => {
                        const section = document.createElement('div');
                        section.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4';
                        section.innerHTML = `
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">${key.replace(/_/g, ' ').toUpperCase()}</h3>
                            <pre class="text-xs bg-white dark:bg-gray-800 p-2 rounded overflow-x-auto">${JSON.stringify(data[key], null, 2)}</pre>
                        `;
                        content.appendChild(section);
                    });
                }
                
                systemInfo.classList.remove('hidden');
            })
            .catch(error => {
                content.innerHTML = '<div class="p-4 bg-red-50 text-red-800 rounded-lg">Failed to fetch system information: ' + error.message + '</div>';
                systemInfo.classList.remove('hidden');
            })
            .finally(() => {
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');
            });
        }

        // Auto-refresh connection status every 30 seconds
        setInterval(() => {
            fetch('/mikrotik/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.querySelector('.flex.items-center.gap-2.px-4.py-2.rounded-lg');
                const statusDot = statusDiv.querySelector('.w-3.h-3.rounded-full');
                const statusText = statusDiv.querySelector('span');
                
                if (data.connection) {
                    statusDiv.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-green-100 text-green-800';
                    statusDot.className = 'w-3 h-3 rounded-full bg-green-500';
                    statusText.textContent = 'Connected';
                } else {
                    statusDiv.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-800';
                    statusDot.className = 'w-3 h-3 rounded-full bg-red-500';
                    statusText.textContent = 'Disconnected';
                }
            })
            .catch(error => {
                console.error('Auto-refresh failed:', error);
            });
        }, 30000);
    </script>
</x-layouts.app>
