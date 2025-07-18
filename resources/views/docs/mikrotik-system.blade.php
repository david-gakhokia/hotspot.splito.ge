<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroTik Hotspot Management System - დოკუმენტაცია</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/autoloader/prism-autoloader.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold">📖 MikroTik Hotspot Management System</h1>
                    <div class="text-sm">
                        <span class="bg-green-500 px-3 py-1 rounded-full">v1.0.0</span>
                    </div>
                </div>
                <p class="mt-2 text-blue-100">სრული დოკუმენტაცია და განხორციელების გზამკვლევი</p>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="bg-white border-b shadow-sm">
            <div class="container mx-auto px-4">
                <div class="flex space-x-8 py-4">
                    <a href="#overview" class="text-blue-600 hover:text-blue-800 font-medium">მიმოხილვა</a>
                    <a href="#installation" class="text-blue-600 hover:text-blue-800 font-medium">ინსტალაცია</a>
                    <a href="#configuration" class="text-blue-600 hover:text-blue-800 font-medium">კონფიგურაცია</a>
                    <a href="#api" class="text-blue-600 hover:text-blue-800 font-medium">API</a>
                    <a href="#features" class="text-blue-600 hover:text-blue-800 font-medium">ფუნქციები</a>
                    <a href="#troubleshooting" class="text-blue-600 hover:text-blue-800 font-medium">თრუბლშუტინგი</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Overview Section -->
            <section id="overview" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">🎯 სისტემის მიმოხილვა</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">🔧 ტექნიკური სპეციფიკაცია</h3>
                            <ul class="text-sm space-y-2">
                                <li><strong>Framework:</strong> Laravel 12</li>
                                <li><strong>PHP:</strong> 8.2+</li>
                                <li><strong>Frontend:</strong> Alpine.js + Tailwind CSS</li>
                                <li><strong>API Protocol:</strong> MikroTik Binary API</li>
                                <li><strong>Port:</strong> 8728 (API)</li>
                                <li><strong>Database:</strong> SQLite</li>
                                <li><strong>Charts:</strong> Chart.js</li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-3">✨ მთავარი ფუნქციები</h3>
                            <ul class="text-sm space-y-2">
                                <li>🔥 Real-time მონიტორინგი</li>
                                <li>👥 მომხმარებელთა მართვა</li>
                                <li>📊 ინტერაქტიული დაშბორდი</li>
                                <li>🔗 RESTful API</li>
                                <li>🧪 API ტესტერი</li>
                                <li>📱 რესპონსიული დიზაინი</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded">
                        <h4 class="font-semibold text-yellow-800 mb-2">📝 რა არის განხორციელებული</h4>
                        <p class="text-yellow-700">
                            ეს სისტემა არის სრული MikroTik RouterOS Hotspot Management გადაწყვეტა Laravel 12 framework-ზე, 
                            რომელიც იძლევა ვებ-ინტერფეისის საშუალებით ჰოტსპოტ მომხმარებლების მართვის, real-time მონიტორინგისა და 
                            კონფიგურაციის შესაძლებლობას. სისტემა იყენებს MikroTik-ის Binary API-ს (port 8728) უშუალო 
                            კომუნიკაციისთვის RouterOS-თან და Laravel Herd-ს development environment-ისთვის.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Installation Section -->
            <section id="installation" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">⚙️ ინსტალაცია და დაყენება</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">1. მოთხოვნები</h3>
                            <div class="bg-gray-50 p-4 rounded">
                                <ul class="list-disc list-inside space-y-1 text-sm">
                                    <li>PHP 8.2+</li>
                                    <li>Laravel 12</li>
                                    <li>Composer</li>
                                    <li>Laravel Herd (ან Valet/XAMPP)</li>
                                    <li>MikroTik RouterOS 6.0+</li>
                                    <li>RouterBoard device with API access</li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">2. პროექტის ინსტალაცია</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code>git clone https://github.com/david-gakhokia/hotspot.splito.ge.git
cd hotspot.splito.ge
composer install
cp .env.example .env
php artisan key:generate</code></pre>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">3. MikroTik კონფიგურაცია</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code># MikroTik Terminal-ში:
/ip service enable api
/ip service set api port=8728
/user add name=hotspot-api group=full password=your-password</code></pre>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">4. Laravel კონფიგურაცია</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code># .env ფაილში დაამატეთ:
MIKROTIK_HOST=192.168.88.1
MIKROTIK_USERNAME=hotspot-api
MIKROTIK_PASSWORD=your-password
MIKROTIK_PORT=8728

# Laravel Herd-ით local development:
# https://hotspot.splito.ge.test/</code></pre>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">5. ჰოტსპოტის ავტომატური კონფიგურაცია</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code># სრული კონფიგურაცია:
php artisan hotspot:setup

# სწრაფი კონფიგურაცია:
php artisan hotspot:quick-setup

# ტესტირება:
php artisan mikrotik:test</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Configuration Section -->
            <section id="configuration" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">🔧 სისტემის კონფიგურაცია</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">ქსელის პარამეტრები</h3>
                            <div class="bg-blue-50 p-4 rounded text-sm">
                                <p><strong>Hotspot Network:</strong> 192.168.88.0/24</p>
                                <p><strong>Gateway:</strong> 192.168.88.1</p>
                                <p><strong>DHCP Pool:</strong> 192.168.88.10-100</p>
                                <p><strong>DNS:</strong> 8.8.8.8, 8.8.4.4</p>
                                <p><strong>Interface:</strong> bridge (auto-detect)</p>
                                <p><strong>Device:</strong> RouterBoard 951Ui-2HnD</p>
                                <p><strong>Firmware:</strong> 6.49.6</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-3">მომხმარებლის პროფილები</h3>
                            <div class="space-y-2 text-sm">
                                <div class="bg-green-50 p-3 rounded">
                                    <strong>Admin Profile:</strong> 1 დღე, უსასრულო ტრაფიკი
                                </div>
                                <div class="bg-yellow-50 p-3 rounded">
                                    <strong>Default Profile:</strong> 1 საათი, შეზღუდული ტრაფიკი
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3">ტესტ მომხმარებლები</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded">
                                <h4 class="font-medium text-blue-800">Admin</h4>
                                <p class="text-sm">username: admin</p>
                                <p class="text-sm">password: admin123</p>
                                <span class="text-xs text-green-600">1 დღე</span>
                            </div>
                            <div class="bg-purple-50 p-4 rounded">
                                <h4 class="font-medium text-purple-800">Test</h4>
                                <p class="text-sm">username: test</p>
                                <p class="text-sm">password: test123</p>
                                <span class="text-xs text-blue-600">1 საათი</span>
                            </div>
                            <div class="bg-orange-50 p-4 rounded">
                                <h4 class="font-medium text-orange-800">Guest</h4>
                                <p class="text-sm">username: guest</p>
                                <p class="text-sm">password: guest123</p>
                                <span class="text-xs text-blue-600">1 საათი</span>
                            </div>
                            <div class="bg-red-50 p-4 rounded">
                                <h4 class="font-medium text-red-800">Demo</h4>
                                <p class="text-sm">username: demo</p>
                                <p class="text-sm">password: demo2025</p>
                                <span class="text-xs text-green-600">1 დღე</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- API Section -->
            <section id="api" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">🔗 API დოკუმენტაცია</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">მომხმარებელთა მართვა</h3>
                            <div class="space-y-4">
                                <div class="border rounded p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">GET</span>
                                        <code class="text-blue-600">/mikrotik/users</code>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">ყველა ჰოტსპოტ მომხმარებლის მიღება</p>
                                    <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto"><code>{
  "success": true,
  "data": [
    {
      ".id": "*1",
      "name": "admin",
      "password": "admin123",
      "profile": "default",
      "disabled": "false"
    }
  ],
  "count": 1
}</code></pre>
                                </div>
                                
                                <div class="border rounded p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">GET</span>
                                        <code class="text-blue-600">/mikrotik/active</code>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">აქტიური სესიების მიღება</p>
                                    <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto"><code>{
  "success": true,
  "data": [
    {
      ".id": "*1",
      "user": "admin",
      "address": "192.168.88.100",
      "session-id": "0x123456",
      "uptime": "1h30m",
      "bytes-in": "1024000",
      "bytes-out": "2048000"
    }
  ],
  "count": 1
}</code></pre>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">სისტემის ინფორმაცია</h3>
                            <div class="space-y-4">
                                <div class="border rounded p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">GET</span>
                                        <code class="text-blue-600">/mikrotik/api</code>
                                    </div>
                                    <p class="text-sm text-gray-600">სრული სისტემის ინფორმაცია</p>
                                </div>
                                
                                <div class="border rounded p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">GET</span>
                                        <code class="text-blue-600">/mikrotik/system-info</code>
                                    </div>
                                    <p class="text-sm text-gray-600">მოწყობილობის ინფორმაცია</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-3">JavaScript გამოყენების მაგალითი</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code>// მომხმარებლების მიღება
async function getUsers() {
    const response = await fetch('/mikrotik/users');
    const data = await response.json();
    console.log(data.data);
}

// აქტიური სესიების მიღება
async function getActiveSessions() {
    const response = await fetch('/mikrotik/active');
    const data = await response.json();
    console.log(data.data);
}

// Real-time განახლება
setInterval(async () => {
    await getUsers();
    await getActiveSessions();
}, 5000);</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">🌟 სისტემის ფუნქციები</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">📊 Real-time მონიტორინგი</h3>
                            <ul class="text-sm space-y-2">
                                <li>• ცოცხალი სტატისტიკა</li>
                                <li>• ინტერაქტიული ჩარტები</li>
                                <li>• ავტომატური განახლება</li>
                                <li>• ტრაფიკის მონიტორინგი</li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-3">👥 მომხმარებელთა მართვა</h3>
                            <ul class="text-sm space-y-2">
                                <li>• მომხმარებლების ნახვა</li>
                                <li>• აქტიური სესიები</li>
                                <li>• პროფილების მართვა</li>
                                <li>• ტრაფიკის კონტროლი</li>
                            </ul>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-800 mb-3">🔧 კონფიგურაცია</h3>
                            <ul class="text-sm space-y-2">
                                <li>• ავტომატური სეტაპი</li>
                                <li>• DHCP კონფიგურაცია</li>
                                <li>• Firewall წესები</li>
                                <li>• NAT სეტინგები</li>
                            </ul>
                        </div>
                        
                        <div class="bg-orange-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-orange-800 mb-3">🧪 ტესტირება</h3>
                            <ul class="text-sm space-y-2">
                                <li>• ინტერაქტიული API ტესტერი</li>
                                <li>• კონექშენის ტესტი</li>
                                <li>• Debug ინფორმაცია</li>
                                <li>• Error ჰენდლინგი</li>
                            </ul>
                        </div>
                        
                        <div class="bg-red-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-red-800 mb-3">📱 ინტერფეისი</h3>
                            <ul class="text-sm space-y-2">
                                <li>• რესპონსიული დიზაინი</li>
                                <li>• თანამედროვე UI</li>
                                <li>• ადვილი ნავიგაცია</li>
                                <li>• ქართული ენა</li>
                            </ul>
                        </div>
                        
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-3">🔗 ინტეგრაცია</h3>
                            <ul class="text-sm space-y-2">
                                <li>• RESTful API</li>
                                <li>• JSON რესპონსები</li>
                                <li>• Binary Protocol</li>
                                <li>• Laravel Integration</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Troubleshooting Section -->
            <section id="troubleshooting" class="mb-12">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">🔍 თრუბლშუტინგი</h2>
                    
                    <div class="space-y-6">
                        <div class="border border-red-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-red-800 mb-3">❌ შესაძლო პრობლემები</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-red-700">1. კონექშენის პრობლემა</h4>
                                    <p class="text-sm text-gray-600 mb-2">Connection timeout ან კავშირის გაწყვეტა</p>
                                    <div class="bg-red-50 p-3 rounded text-sm">
                                        <strong>გადაწყვეტა:</strong>
                                        <ul class="list-disc list-inside mt-1">
                                            <li>შეამოწმეთ MikroTik API სერვისი ჩართულია</li>
                                            <li>დაადასტურეთ firewall წესები</li>
                                            <li>გადაამოწმეთ credentials</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-red-700">2. ცარიელი რესპონსები</h4>
                                    <p class="text-sm text-gray-600 mb-2">API აბრუნებს ცარიელ მონაცემებს</p>
                                    <div class="bg-red-50 p-3 rounded text-sm">
                                        <strong>გადაწყვეტა:</strong>
                                        <ul class="list-disc list-inside mt-1">
                                            <li>შეამოწმეთ მომხმარებლის permissions</li>
                                            <li>გადაამოწმეთ RouterOS ვერსია</li>
                                            <li>შეამოწმეთ API commands syntax</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="font-medium text-red-700">3. Parsing ერორები</h4>
                                    <p class="text-sm text-gray-600 mb-2">Binary protocol parsing ფეილი</p>
                                    <div class="bg-red-50 p-3 rounded text-sm">
                                        <strong>გადაწყვეტა:</strong>
                                        <ul class="list-disc list-inside mt-1">
                                            <li>შეამოწმეთ response format logs-ში</li>
                                            <li>გადაამოწმეთ binary protocol implementation</li>
                                            <li>ტესტი raw socket connection-ით</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border border-green-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-green-800 mb-3">✅ Debug ბრძანებები</h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded text-sm overflow-x-auto"><code># კონექშენის ტესტი
php artisan mikrotik:test

# სისტემის ლოგები
tail -f storage/logs/laravel.log

# Route-ების ნახვა
php artisan route:list | findstr mikrotik

# კონფიგურაციის განახლება
php artisan config:cache</code></pre>
                        </div>

                        <div class="border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">📞 მხარდაჭერა</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-medium mb-2">📧 კონტაქტი</h4>
                                    <p class="text-sm text-gray-600">david.gakhokia@gmail.com</p>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-2">🔗 GitHub</h4>
                                    <a href="https://github.com/david-gakhokia/hotspot.splito.ge" class="text-blue-600 hover:underline text-sm">Repository Link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Links -->
            <section class="mb-12">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold mb-6">🚀 სწრაფი ლინკები</h2>
                    <p class="mb-4 text-blue-100">Laravel Herd Environment: https://hotspot.splito.ge.test/</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="/mikrotik/test" class="bg-white bg-opacity-20 p-4 rounded-lg hover:bg-opacity-30 transition-all">
                            <h3 class="font-semibold mb-2">📊 მთავარი დაშბორდი</h3>
                            <p class="text-sm opacity-90">სისტემის მთავარი გვერდი</p>
                        </a>
                        <a href="/mikrotik/realtime" class="bg-white bg-opacity-20 p-4 rounded-lg hover:bg-opacity-30 transition-all">
                            <h3 class="font-semibold mb-2">🔥 Real-time Monitor</h3>
                            <p class="text-sm opacity-90">ცოცხალი მონიტორინგი</p>
                        </a>
                        <a href="/mikrotik/test-api" class="bg-white bg-opacity-20 p-4 rounded-lg hover:bg-opacity-30 transition-all">
                            <h3 class="font-semibold mb-2">🧪 API ტესტერი</h3>
                            <p class="text-sm opacity-90">API endpoints ტესტირება</p>
                        </a>
                        <a href="/docs/mikrotik" class="bg-white bg-opacity-20 p-4 rounded-lg hover:bg-opacity-30 transition-all">
                            <h3 class="font-semibold mb-2">� დოკუმენტაცია</h3>
                            <p class="text-sm opacity-90">სრული მითითებები</p>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white text-center py-8">
            <div class="container mx-auto px-4">
                <p class="mb-2">&copy; 2025 MikroTik Hotspot Management System</p>
                <p class="text-gray-400 text-sm">Built with ❤️ using Laravel 12, Alpine.js, Tailwind CSS & MikroTik Binary API</p>
                <p class="text-gray-500 text-xs mt-2">დეველოპერი: David Gakhokia | ვერსია: 1.0.0 | Laravel Herd Environment</p>
            </div>
        </footer>
    </div>
</body>
</html>
