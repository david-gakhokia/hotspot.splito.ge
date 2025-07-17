<x-layouts.app :title="'Login Page Designer'">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="text-purple-500">🎨</span>
                        Login Page Designer
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Design and customize your hotspot login page</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('hotspot.server.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Server Config
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Customization Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Customize Login Page</h2>
                
                <form method="POST" action="{{ route('hotspot.login-designer.customize') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Template Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Choose Template</label>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($templates as $key => $template)
                                <div class="relative">
                                    <input type="radio" name="template" value="{{ $key }}" id="template_{{ $key }}" class="sr-only peer" {{ $key === 'default' ? 'checked' : '' }}>
                                    <label for="template_{{ $key }}" class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-gray-200 dark:border-gray-600 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 transition-all">
                                        <div class="text-center">
                                            <h3 class="font-medium text-gray-900 dark:text-white">{{ $template['name'] }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $template['description'] }}</p>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Profile Selection -->
                    <div class="mb-6">
                        <label for="profile_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Apply to Profile</label>
                        <select name="profile_id" id="profile_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select a profile</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile['.id'] ?? '' }}">{{ $profile['name'] ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Basic Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Page Title</label>
                            <input type="text" name="title" id="title" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white" placeholder="Hotspot Login">
                        </div>
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                            <input type="file" name="logo" id="logo" accept="image/*" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Color Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="background_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Background Color</label>
                            <input type="color" name="background_color" id="background_color" value="#f3f4f6" class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Color</label>
                            <input type="color" name="primary_color" id="primary_color" value="#3b82f6" class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        <div>
                            <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secondary Color</label>
                            <input type="color" name="secondary_color" id="secondary_color" value="#6b7280" class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Text Settings -->
                    <div class="mb-6">
                        <label for="welcome_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Welcome Message</label>
                        <textarea name="welcome_message" id="welcome_message" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white" placeholder="Welcome to our WiFi network"></textarea>
                    </div>

                    <div class="mb-6">
                        <label for="footer_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Footer Text</label>
                        <input type="text" name="footer_text" id="footer_text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white" placeholder="Powered by MikroTik">
                    </div>

                    <!-- Advanced Settings -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label for="custom_css" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom CSS</label>
                            <button type="button" onclick="toggleAdvanced()" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-200">
                                <span id="advanced-toggle">Show Advanced</span>
                            </button>
                        </div>
                        <div id="advanced-settings" class="hidden">
                            <textarea name="custom_css" id="custom_css" rows="6" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white font-mono text-sm" placeholder="/* Add your custom CSS here */"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="previewDesign()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Apply Design
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Panel -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Live Preview</h2>
                
                <div id="preview-container" class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                    <iframe id="preview-iframe" class="w-full h-96 border-0" src="about:blank"></iframe>
                </div>
                
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        This is how your login page will look to users
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAdvanced() {
            const settings = document.getElementById('advanced-settings');
            const toggle = document.getElementById('advanced-toggle');
            
            if (settings.classList.contains('hidden')) {
                settings.classList.remove('hidden');
                toggle.textContent = 'Hide Advanced';
            } else {
                settings.classList.add('hidden');
                toggle.textContent = 'Show Advanced';
            }
        }

        function previewDesign() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            
            fetch('{{ route("hotspot.login-designer.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                const iframe = document.getElementById('preview-iframe');
                iframe.srcdoc = html;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Preview failed. Please try again.');
            });
        }

        // Auto-preview when template changes
        document.querySelectorAll('input[name="template"]').forEach(radio => {
            radio.addEventListener('change', previewDesign);
        });

        // Auto-preview when colors change
        document.querySelectorAll('input[type="color"]').forEach(input => {
            input.addEventListener('change', previewDesign);
        });

        // Initial preview
        document.addEventListener('DOMContentLoaded', previewDesign);
    </script>
</x-layouts.app>
