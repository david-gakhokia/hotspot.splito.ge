<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troubleshooting Guide | პრობლემების გადაჭრა</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .glass-dark {
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

<!-- Navigation Header -->
<nav class="bg-white shadow-lg mb-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="/dashboard" class="text-2xl font-bold text-blue-600 hover:text-blue-800">
                    🔥 Hotspot System
                </a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-600">📚 Documentation</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/docs/mikrotik" class="text-blue-600 hover:text-blue-800 px-3 py-2 rounded-lg hover:bg-blue-50">
                    📡 MikroTik Docs
                </a>
                <a href="/docs/troubleshooting" class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700">
                    🔍 Troubleshooting
                </a>
                <a href="/dashboard" class="text-gray-600 hover:text-gray-800 px-3 py-2 rounded-lg hover:bg-gray-50">
                    ← Dashboard
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="min-h-screen py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🔍 Troubleshooting Guide
            </h1>
            <p class="text-xl text-gray-600">
                პრობლემების გადაჭრის სრული გზამკვლევი
            </p>
        </div>

        <!-- Navigation -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="#hotspot-issues" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                    🔥 Hotspot Issues
                </a>
                <a href="#wifi-config" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                    📡 WiFi Config
                </a>
                <a href="#commands" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                    ⚡ Commands
                </a>
                <a href="#faq" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors">
                    ❓ FAQ
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Hotspot Login Redirection Issues -->
                <section id="hotspot-issues" class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-red-600 mb-6 flex items-center">
                        🔥 Hotspot Login Redirection არ მუშაობს
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Diagnostic Commands -->
                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">🧪 დიაგნოსტიკური კომანდები</h3>
                            <div class="space-y-3">
                                <div class="bg-gray-900 rounded-lg p-4">
                                    <code class="text-green-400 text-sm">
                                        # სრული დიაგნოსტიკა<br>
                                        php artisan hotspot:diagnose<br><br>
                                        # სწრაფი აღდგენა<br>
                                        php artisan mikrotik:quick-fix
                                    </code>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Configuration -->
                        <div class="bg-yellow-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">🔧 ხელით კონფიგურაცია MikroTik Terminal-ში</h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <code class="text-green-400 text-sm">
                                    # DNS კონფიგურაცია<br>
                                    /ip dns set allow-remote-requests=yes servers=8.8.8.8,8.8.4.4<br>
                                    /ip dns static add name="hotspot.local" address=192.168.88.1<br><br>
                                    
                                    # NAT Rules HTTP redirect-ისთვის<br>
                                    /ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080<br>
                                    /ip firewall nat add chain=dstnat protocol=tcp dst-port=443 hotspot=auth action=redirect to-ports=8080<br><br>
                                    
                                    # Firewall filter Hotspot traffic-ისთვის<br>
                                    /ip firewall filter add chain=input protocol=tcp dst-port=8080 action=accept<br><br>
                                    
                                    # Walled Garden (თუ სჭირდება)<br>
                                    /ip hotspot walled-garden add dst-host="*.splito.ge" action=allow<br>
                                    /ip hotspot walled-garden add dst-host="hotspot.splito.ge.test" action=allow
                                </code>
                            </div>
                        </div>

                        <!-- Testing Steps -->
                        <div class="bg-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">✅ ტესტირების ნაბიჯები</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li><strong>მოწყობილობის დაკავშირება</strong> - WiFi-ზე დაუკავშირდი</li>
                                <li><strong>Browser-ის გახსნა</strong> - გახსენი ნებისმიერი საიტი (მაგ. google.com)</li>
                                <li><strong>Redirect-ის შემოწმება</strong> - უნდა გადამისამართდე MikroTik login-ზე</li>
                                <li><strong>Login</strong> - შიხვედი test/test123, demo/demo123 ან guest/guest123</li>
                            </ol>
                        </div>
                    </div>
                </section>

                <!-- Common Problems -->
                <section class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-orange-600 mb-6 flex items-center">
                        🚨 ხშირი პრობლემები
                    </h2>

                    <div class="space-y-6">
                        <!-- Problem 1 -->
                        <div class="border-l-4 border-red-500 pl-6">
                            <h3 class="text-lg font-semibold text-red-700 mb-2">
                                პრობლემა: "NO HOTSPOT SERVERS FOUND"
                            </h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <code class="text-green-400 text-sm">
                                    # გამოსავალი<br>
                                    php artisan mikrotik:quick-fix<br>
                                    # ან ხელით<br>
                                    /ip hotspot add name=hotspot interface=bridge profile=default
                                </code>
                            </div>
                        </div>

                        <!-- Problem 2 -->
                        <div class="border-l-4 border-yellow-500 pl-6">
                            <h3 class="text-lg font-semibold text-yellow-700 mb-2">
                                პრობლემა: მიერთებული მომხმარებლები არ ირთდებიან
                            </h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <code class="text-green-400 text-sm">
                                    # შეამოწმე firewall rules<br>
                                    /ip firewall nat print<br>
                                    /ip firewall filter print<br><br>
                                    
                                    # დაამატე NAT rule-ები თუ არ არის<br>
                                    /ip firewall nat add chain=dstnat protocol=tcp dst-port=80 hotspot=auth action=redirect to-ports=8080
                                </code>
                            </div>
                        </div>

                        <!-- Problem 3 -->
                        <div class="border-l-4 border-blue-500 pl-6">
                            <h3 class="text-lg font-semibold text-blue-700 mb-2">
                                პრობლემა: DNS არ მუშაობს
                            </h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <code class="text-green-400 text-sm">
                                    # DNS სერვერების დაყენება<br>
                                    /ip dns set allow-remote-requests=yes servers=8.8.8.8,8.8.4.4
                                </code>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- WiFi Configuration Issues -->
                <section id="wifi-config" class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-green-600 mb-6 flex items-center">
                        📡 WiFi Configuration პრობლემები
                    </h2>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- CSRF Token Error -->
                            <div class="bg-red-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-red-800 mb-3">CSRF Token Error</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                                    <li>დარწმუნდი რომ <code class="bg-red-200 px-1 rounded">head.blade.php</code>-ში არის CSRF meta tag</li>
                                    <li>Browser cache გაასუფთავე</li>
                                </ul>
                            </div>

                            <!-- Connection Timeout -->
                            <div class="bg-orange-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-orange-800 mb-3">Connection Timeout</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-orange-700">
                                    <li>შეამოწმე MikroTik IP მისამართი (default: 192.168.88.1)</li>
                                    <li>დარწმუნდი რომ API გააქტიურებულია <code class="bg-orange-200 px-1 rounded">/ip service set api disabled=no</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Artisan Commands -->
                <section id="commands" class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-purple-600 mb-6 flex items-center">
                        ⚡ მუხლი Artisan Commands
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-purple-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-purple-800 mb-3">დიაგნოსტიკა</h3>
                            <div class="bg-gray-900 rounded-lg p-3">
                                <code class="text-green-400 text-sm">
                                    php artisan hotspot:diagnose<br>
                                    php artisan hotspot:check<br>
                                    php artisan hotspot:step-by-step
                                </code>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">აღდგენა</h3>
                            <div class="bg-gray-900 rounded-lg p-3">
                                <code class="text-green-400 text-sm">
                                    php artisan mikrotik:quick-fix<br>
                                    php artisan hotspot:fix<br>
                                    php artisan hotspot:enable bridge
                                </code>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- FAQ -->
                <section id="faq" class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-indigo-600 mb-6 flex items-center">
                        ❓ ხშირად დასმული შეკითხვები (FAQ)
                    </h2>

                    <div class="space-y-4">
                        <details class="bg-gray-50 rounded-lg p-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer">
                                რატომ არ ირთდება login გვერდი როდესაც WiFi-ზე ვუკავშირდები?
                            </summary>
                            <div class="mt-3 text-gray-600">
                                <p>ყველაზე ხშირი მიზეზია Hotspot Server-ის არ მუშაობა. გაუშვი <code class="bg-gray-200 px-1 rounded">php artisan hotspot:diagnose</code> კომანდა დიაგნოსტიკისთვის.</p>
                            </div>
                        </details>

                        <details class="bg-gray-50 rounded-lg p-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer">
                                როგორ გავასწორო "NO HOTSPOT SERVERS FOUND" შეცდომა?
                            </summary>
                            <div class="mt-3 text-gray-600">
                                <p>გაუშვი <code class="bg-gray-200 px-1 rounded">php artisan mikrotik:quick-fix</code> კომანდა ან ხელით MikroTik Terminal-ში: <code class="bg-gray-200 px-1 rounded">/ip hotspot add name=hotspot interface=bridge profile=default</code></p>
                            </div>
                        </details>

                        <details class="bg-gray-50 rounded-lg p-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer">
                                რა ვქნა თუ WiFi კონფიგურაცია არ ინახება?
                            </summary>
                            <div class="mt-3 text-gray-600">
                                <p>შეამოწმე CSRF token და დარწმუნდი რომ MikroTik API კავშირი მუშაობს. ასევე გაასუფთავე browser cache.</p>
                            </div>
                        </details>

                        <details class="bg-gray-50 rounded-lg p-4">
                            <summary class="font-semibold text-gray-800 cursor-pointer">
                                როგორ ვტესტო Hotspot-ის მუშაობა?
                            </summary>
                            <div class="mt-3 text-gray-600">
                                <p>დაუკავშირდი WiFi-ს, გახსენი ნებისმიერი website (მაგ. google.com) და უნდა გადამისამართდე login გვერდზე. Test მომხმარებლები: test/test123, demo/demo123</p>
                            </div>
                        </details>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">🚀 სწრაფი მოქმედებები</h3>
                    <div class="space-y-3">
                        <button onclick="runCommand('php artisan hotspot:diagnose')" 
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                            🔍 დიაგნოსტიკა
                        </button>
                        <button onclick="runCommand('php artisan mikrotik:quick-fix')" 
                                class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                            🔧 სწრაფი აღდგენა
                        </button>
                        <a href="/dashboard" class="block w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors text-sm text-center">
                            📊 დაშბორდი
                        </a>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📈 სისტემის სტატუსი</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Laravel</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">✅ მუშაობს</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">MikroTik API</span>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs" id="mikrotik-status">🔄 შემოწმება...</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Hotspot</span>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs" id="hotspot-status">❌ ესადაგება</span>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold mb-4">💬 დახმარება</h3>
                    <p class="text-sm opacity-90 mb-4">
                        თუ პრობლემა კვლავ არსებობს, დაგვიკავშირდი:
                    </p>
                    <div class="space-y-2 text-sm">
                        <div>📧 david@splito.ge</div>
                        <div>🐙 GitHub Issues</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runCommand(command) {
    alert('გაუშვი Terminal-ში: ' + command);
}

// Check system status
window.addEventListener('load', function() {
    // Simulate API check
    setTimeout(() => {
        if (document.getElementById('mikrotik-status')) {
            document.getElementById('mikrotik-status').innerHTML = '✅ კავშირი';
            document.getElementById('mikrotik-status').className = 'px-2 py-1 bg-green-100 text-green-800 rounded text-xs';
        }
    }, 2000);
});
</script>

</body>
</html>
