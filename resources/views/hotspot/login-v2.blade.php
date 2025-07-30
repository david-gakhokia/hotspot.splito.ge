<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi ავტორიზაცია | Hotspot Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-form {
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div x-data="hotspotLogin()" class="w-full max-w-md">
        <!-- Logo და ზედა ინფო -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">WiFi ავტორიზაცია</h1>
            <p class="text-white text-opacity-80">გთხოვთ, შეიყვანოთ თქვენი მონაცემები</p>
        </div>

        <!-- Login Form -->
        <div class="glass rounded-2xl p-8 login-form">
            <form @submit.prevent="login()" class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-white text-sm font-medium mb-2">
                        👤 მომხმარებლის სახელი
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        x-model="credentials.username"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 border border-white border-opacity-30 text-white placeholder-white placeholder-opacity-70 focus:bg-opacity-30 focus:border-opacity-50 focus:outline-none transition-all"
                        placeholder="შეიყვანეთ username"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-white text-sm font-medium mb-2">
                        🔒 პაროლი
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        x-model="credentials.password"
                        required
                        class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-20 border border-white border-opacity-30 text-white placeholder-white placeholder-opacity-70 focus:bg-opacity-30 focus:border-opacity-50 focus:outline-none transition-all"
                        placeholder="შეიყვანეთ password"
                    >
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 disabled:opacity-50"
                    :class="loading ? 'cursor-not-allowed' : 'cursor-pointer'"
                >
                    <span x-show="!loading">🚀 შესვლა</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        მიმდინარეობს...
                    </span>
                </button>

                <!-- Error Message -->
                <div x-show="error" x-transition class="bg-red-500 bg-opacity-20 border border-red-500 border-opacity-30 text-white p-3 rounded-lg text-sm">
                    <span x-text="error"></span>
                </div>

                <!-- Success Message -->
                <div x-show="success" x-transition class="bg-green-500 bg-opacity-20 border border-green-500 border-opacity-30 text-white p-3 rounded-lg text-sm">
                    <span x-text="success"></span>
                </div>
            </form>
        </div>

        <!-- Test Credentials -->
        <div class="mt-6 text-center">
            <details class="glass rounded-lg p-4">
                <summary class="text-white cursor-pointer font-medium">🔑 ტესტ მონაცემები</summary>
                <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                    <div class="bg-white bg-opacity-10 p-3 rounded">
                        <p class="text-white font-medium">Admin</p>
                        <p class="text-white text-opacity-80">admin / admin123</p>
                        <p class="text-green-300 text-xs">1 დღე</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded">
                        <p class="text-white font-medium">Test</p>
                        <p class="text-white text-opacity-80">test / test123</p>
                        <p class="text-blue-300 text-xs">1 საათი</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded">
                        <p class="text-white font-medium">Guest</p>
                        <p class="text-white text-opacity-80">guest / guest123</p>
                        <p class="text-blue-300 text-xs">1 საათი</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-3 rounded">
                        <p class="text-white font-medium">Demo</p>
                        <p class="text-white text-opacity-80">demo / demo2025</p>
                        <p class="text-green-300 text-xs">1 დღე</p>
                    </div>
                </div>
            </details>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white text-opacity-60 text-sm">
                © 2025 Hotspot Management System
            </p>
            <div class="mt-2">
                <a href="/mikrotik/test" class="text-white text-opacity-60 hover:text-opacity-100 text-xs underline">
                    📊 სისტემის დაშბორდი
                </a>
            </div>
        </div>
    </div>

    <script>
        function hotspotLogin() {
            return {
                loading: false,
                error: '',
                success: '',
                credentials: {
                    username: '',
                    password: ''
                },

                async login() {
                    this.loading = true;
                    this.error = '';
                    this.success = '';

                    try {
                        // MikroTik hotspot login simulation
                        // In real implementation, this would submit to MikroTik login URL
                        const response = await fetch('/hotspot/authenticate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.credentials)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.success = 'ავტორიზაცია წარმატებულია! ახლა შეგიძლიათ გამოიყენოთ ინტერნეტი.';
                            
                            // Redirect to success page or original destination
                            setTimeout(() => {
                                window.location.href = data.redirect || '/hotspot/welcome';
                            }, 2000);
                        } else {
                            this.error = data.message || 'ავტორიზაცია ვერ მოხერხდა. შეამოწმეთ მონაცემები.';
                        }
                    } catch (error) {
                        console.error('Login error:', error);
                        this.error = 'კავშირის შეცდომა. სცადეთ თავიდან.';
                    } finally {
                        this.loading = false;
                    }
                },

                // Quick login for testing
                quickLogin(username, password) {
                    this.credentials.username = username;
                    this.credentials.password = password;
                    this.login();
                }
            }
        }

        // Auto-fill from URL parameters if available
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const username = urlParams.get('username');
            const dst = urlParams.get('dst');
            
            if (username) {
                // Auto-fill username if provided by MikroTik
                Alpine.store('credentials', { username: username, password: '' });
            }
            
            if (dst) {
                console.log('Destination URL:', dst);
                // Store destination for redirect after login
                sessionStorage.setItem('loginDestination', dst);
            }
        });
    </script>
</body>
</html>
