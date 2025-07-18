<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>კეთილი იყოს თქვენი მობრძანება | WiFi Hotspot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 50%, #16a34a 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .welcome-animation {
            animation: welcomeSlide 1s ease-out;
        }
        @keyframes welcomeSlide {
            from { opacity: 0; transform: translateY(-30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .stats-card {
            animation: statsSlide 0.8s ease-out 0.3s both;
        }
        @keyframes statsSlide {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div x-data="hotspotDashboard()" x-init="init()" class="min-h-screen p-4">
        <!-- Header -->
        <header class="text-center mb-8 welcome-animation">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white bg-opacity-20 rounded-full mb-6">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">წარმატებული ავტორიზაცია! 🎉</h1>
            <p class="text-white text-opacity-90 text-lg">
                კეთილი იყოს თქვენი მობრძანება, <span class="font-semibold">{{ $username ?? 'Guest' }}</span>
            </p>
            <p class="text-white text-opacity-80 mt-2">ახლა შეგიძლიათ გამოიყენოთ ინტერნეტი!</p>
        </header>

        <!-- Status Cards -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Connection Status -->
                <div class="glass rounded-2xl p-6 stats-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white font-semibold">🌐 კავშირის სტატუსი</h3>
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-white text-2xl font-bold">ხაზზე</p>
                    <p class="text-white text-opacity-70 text-sm">სტაბილური კავშირი</p>
                </div>

                <!-- Session Time -->
                <div class="glass rounded-2xl p-6 stats-card" style="animation-delay: 0.5s;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white font-semibold">⏰ სესიის დრო</h3>
                    </div>
                    <p x-text="sessionTime" class="text-white text-2xl font-bold">00:00:00</p>
                    <p class="text-white text-opacity-70 text-sm">დაკავშირების დრო</p>
                </div>

                <!-- Data Usage -->
                <div class="glass rounded-2xl p-6 stats-card" style="animation-delay: 0.7s;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white font-semibold">📊 ტრაფიკი</h3>
                    </div>
                    <p x-text="dataUsage" class="text-white text-2xl font-bold">0 MB</p>
                    <p class="text-white text-opacity-70 text-sm">გამოყენებული</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="max-w-2xl mx-auto mb-8">
            <div class="glass rounded-2xl p-8">
                <h2 class="text-white text-xl font-bold mb-6 text-center">🚀 სწრაფი მოქმედებები</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="https://google.com" target="_blank" class="flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                        </svg>
                        ინტერნეტ ბრაუზინგი
                    </a>
                    
                    <button @click="checkSpeed()" class="flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        სიჩქარის ტესტი
                    </button>
                    
                    <a href="/mikrotik/realtime" target="_blank" class="flex items-center justify-center bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        სისტემის მონიტორი
                    </button>
                    
                    <button @click="logout()" class="flex items-center justify-center bg-red-500 bg-opacity-30 hover:bg-opacity-50 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        გამოსვლა
                    </button>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="max-w-4xl mx-auto">
            <div class="glass rounded-2xl p-8">
                <h2 class="text-white text-xl font-bold mb-6">📡 სისტემის ინფორმაცია</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                        <h4 class="text-white font-medium mb-2">📍 IP მისამართი</h4>
                        <p x-text="userInfo.ip" class="text-white text-opacity-80">მუშავდება...</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                        <h4 class="text-white font-medium mb-2">🌐 MAC მისამართი</h4>
                        <p x-text="userInfo.mac" class="text-white text-opacity-80">მუშავდება...</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                        <h4 class="text-white font-medium mb-2">🚀 სიჩქარე</h4>
                        <p x-text="userInfo.speed" class="text-white text-opacity-80">მუშავდება...</p>
                    </div>
                    
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                        <h4 class="text-white font-medium mb-2">⏳ სესიის ლიმიტი</h4>
                        <p x-text="userInfo.timeLimit" class="text-white text-opacity-80">მუშავდება...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-8">
            <p class="text-white text-opacity-60 text-sm">
                © 2025 WiFi Hotspot Management System
            </p>
            <p class="text-white text-opacity-40 text-xs mt-1">
                Powered by MikroTik RouterOS & Laravel 12
            </p>
        </footer>
    </div>

    <script>
        function hotspotDashboard() {
            return {
                sessionTime: '00:00:00',
                dataUsage: '0 MB',
                sessionStart: new Date(),
                userInfo: {
                    ip: 'მუშავდება...',
                    mac: 'მუშავდება...',
                    speed: 'მუშავდება...',
                    timeLimit: 'მუშავდება...'
                },

                init() {
                    this.startSessionTimer();
                    this.loadUserInfo();
                    this.startDataUsageMonitoring();
                },

                startSessionTimer() {
                    setInterval(() => {
                        const now = new Date();
                        const diff = now - this.sessionStart;
                        
                        const hours = Math.floor(diff / (1000 * 60 * 60));
                        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        
                        this.sessionTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    }, 1000);
                },

                async loadUserInfo() {
                    try {
                        // Get user's IP
                        const ipResponse = await fetch('https://api.ipify.org?format=json');
                        const ipData = await ipResponse.json();
                        this.userInfo.ip = ipData.ip || 'N/A';

                        // Mock data for demonstration
                        this.userInfo.mac = 'xx:xx:xx:xx:xx:xx';
                        this.userInfo.speed = '10 Mbps';
                        this.userInfo.timeLimit = '{{ $username === "admin" ? "1 დღე" : "1 საათი" }}';
                    } catch (error) {
                        console.error('Error loading user info:', error);
                    }
                },

                startDataUsageMonitoring() {
                    // Mock data usage tracking
                    let usage = 0;
                    setInterval(() => {
                        usage += Math.random() * 0.1; // Random usage increment
                        this.dataUsage = `${usage.toFixed(1)} MB`;
                    }, 5000);
                },

                async checkSpeed() {
                    alert('🚀 სიჩქარის ტესტი იწყება... (ეს არის demo ფუნქცია)');
                    
                    // In real implementation, this would trigger a speed test
                    setTimeout(() => {
                        alert('📊 სიჩქარის ტესტის შედეგი:\n\nDownload: 25.4 Mbps\nUpload: 8.7 Mbps\nPing: 15ms');
                    }, 3000);
                },

                async logout() {
                    if (confirm('დარწმუნებული ხართ, რომ გსურთ გამოსვლა?')) {
                        try {
                            const response = await fetch('/hotspot/logout', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            const data = await response.json();
                            
                            if (data.success) {
                                alert('წარმატებით გამოხვედით!');
                                window.location.href = '/hotspot/login';
                            } else {
                                alert('გამოსვლისას მოხდა შეცდომა.');
                            }
                        } catch (error) {
                            console.error('Logout error:', error);
                            alert('კავშირის შეცდომა.');
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>
