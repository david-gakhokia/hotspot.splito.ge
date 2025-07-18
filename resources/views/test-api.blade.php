<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikroTik API Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">MikroTik API Endpoint Tests</h1>
        
        <!-- Test Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <button id="testUsers" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Test /mikrotik/users
            </button>
            <button id="testActive" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Test /mikrotik/active
            </button>
            <button id="testApi" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Test /mikrotik/api
            </button>
        </div>

        <!-- Response Display -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">API Response</h2>
            <pre id="response" class="bg-gray-100 p-4 rounded overflow-auto max-h-96 text-sm"></pre>
        </div>

        <!-- Status Display -->
        <div id="status" class="mt-4 p-4 rounded hidden"></div>
    </div>

    <script>
        const responseEl = document.getElementById('response');
        const statusEl = document.getElementById('status');

        function showStatus(message, isError = false) {
            statusEl.textContent = message;
            statusEl.className = `mt-4 p-4 rounded ${isError ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}`;
            statusEl.classList.remove('hidden');
            setTimeout(() => {
                statusEl.classList.add('hidden');
            }, 3000);
        }

        async function testEndpoint(url, endpoint) {
            try {
                showStatus(`Testing ${endpoint}...`);
                responseEl.textContent = 'Loading...';
                
                const response = await fetch(url);
                const data = await response.json();
                
                if (response.ok) {
                    responseEl.textContent = JSON.stringify(data, null, 2);
                    showStatus(`✅ ${endpoint} successful!`);
                } else {
                    responseEl.textContent = JSON.stringify(data, null, 2);
                    showStatus(`❌ ${endpoint} failed: ${data.error || 'Unknown error'}`, true);
                }
            } catch (error) {
                responseEl.textContent = `Error: ${error.message}`;
                showStatus(`❌ ${endpoint} failed: ${error.message}`, true);
            }
        }

        // Event listeners
        document.getElementById('testUsers').addEventListener('click', () => {
            testEndpoint('/mikrotik/users', 'Users API');
        });

        document.getElementById('testActive').addEventListener('click', () => {
            testEndpoint('/mikrotik/active', 'Active Users API');
        });

        document.getElementById('testApi').addEventListener('click', () => {
            testEndpoint('/mikrotik/api', 'System API');
        });

        // Auto-test users endpoint on page load
        window.addEventListener('load', () => {
            testEndpoint('/mikrotik/users', 'Users API');
        });
    </script>
</body>
</html>
