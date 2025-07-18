// MikroTik API Integration Examples
// ================================

// 1. Get all hotspot users
async function getAllUsers() {
    try {
        const response = await fetch('/mikrotik/users');
        const data = await response.json();
        
        if (data.success) {
            console.log(`Found ${data.count} users:`, data.data);
            return data.data;
        } else {
            console.error('Error:', data.error);
        }
    } catch (error) {
        console.error('Network error:', error);
    }
}

// 2. Get active sessions
async function getActiveSessions() {
    try {
        const response = await fetch('/mikrotik/active');
        const data = await response.json();
        
        if (data.success) {
            console.log(`Found ${data.count} active sessions:`, data.data);
            return data.data;
        } else {
            console.error('Error:', data.error);
        }
    } catch (error) {
        console.error('Network error:', error);
    }
}

// 3. Get complete system information
async function getSystemInfo() {
    try {
        const response = await fetch('/mikrotik/api');
        const data = await response.json();
        
        if (response.ok) {
            console.log('System information:', data);
            return data;
        } else {
            console.error('Error:', data.error);
        }
    } catch (error) {
        console.error('Network error:', error);
    }
}

// 4. Real-time dashboard update example
async function updateDashboard() {
    const [users, active, system] = await Promise.all([
        getAllUsers(),
        getActiveSessions(),
        getSystemInfo()
    ]);
    
    // Update UI elements
    document.getElementById('userCount').textContent = users?.length || 0;
    document.getElementById('activeCount').textContent = active?.length || 0;
    document.getElementById('deviceName').textContent = system?.identity?.name || 'Unknown';
    
    // Update user list
    const userList = document.getElementById('userList');
    if (userList && users) {
        userList.innerHTML = users.map(user => `
            <div class="user-item">
                <strong>${user.name}</strong> - ${user.profile}
                ${user.disabled === 'true' ? '(Disabled)' : '(Active)'}
            </div>
        `).join('');
    }
    
    // Update active sessions
    const activeList = document.getElementById('activeList');
    if (activeList && active) {
        activeList.innerHTML = active.map(session => `
            <div class="session-item">
                <strong>${session.user}</strong> - ${session.address}
                <small>Session: ${session['session-id']}</small>
            </div>
        `).join('');
    }
}

// 5. Periodic updates (every 30 seconds)
function startRealTimeUpdates() {
    updateDashboard(); // Initial load
    setInterval(updateDashboard, 30000); // Update every 30 seconds
}

// 6. Error handling with retry
async function fetchWithRetry(url, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            const response = await fetch(url);
            if (response.ok) {
                return await response.json();
            }
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            
            console.warn(`Attempt ${i + 1} failed, retrying...`);
            await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1))); // Exponential backoff
        }
    }
}

// 7. Usage examples
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 MikroTik API Examples loaded');
    
    // Example: Get users on button click
    const getUsersBtn = document.getElementById('getUsersBtn');
    if (getUsersBtn) {
        getUsersBtn.addEventListener('click', getAllUsers);
    }
    
    // Example: Start real-time updates
    const realtimeBtn = document.getElementById('realtimeBtn');
    if (realtimeBtn) {
        realtimeBtn.addEventListener('click', startRealTimeUpdates);
    }
    
    // Example: Manual system info
    const systemBtn = document.getElementById('systemBtn');
    if (systemBtn) {
        systemBtn.addEventListener('click', getSystemInfo);
    }
});

// 8. Export functions for external use
window.MikroTikAPI = {
    getAllUsers,
    getActiveSessions,
    getSystemInfo,
    updateDashboard,
    startRealTimeUpdates,
    fetchWithRetry
};

/*
Usage in HTML:
===============

<!DOCTYPE html>
<html>
<head>
    <title>MikroTik Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <div class="stats">
            <div>Users: <span id="userCount">-</span></div>
            <div>Active: <span id="activeCount">-</span></div>
            <div>Device: <span id="deviceName">-</span></div>
        </div>
        
        <div class="controls">
            <button id="getUsersBtn">Get Users</button>
            <button id="realtimeBtn">Start Real-time</button>
            <button id="systemBtn">System Info</button>
        </div>
        
        <div class="content">
            <div id="userList"></div>
            <div id="activeList"></div>
        </div>
    </div>
    
    <script src="mikrotik-api.js"></script>
</body>
</html>

API Response Examples:
======================

/mikrotik/users response:
{
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
}

/mikrotik/active response:
{
    "success": true,
    "data": [
        {
            ".id": "*1",
            "user": "admin",
            "address": "192.168.88.100",
            "mac-address": "AA:BB:CC:DD:EE:FF",
            "session-id": "0x123456",
            "uptime": "1h30m",
            "bytes-in": "1024000",
            "bytes-out": "2048000"
        }
    ],
    "count": 1
}
*/
