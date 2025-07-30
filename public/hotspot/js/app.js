// Splito Hotspot Login JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    const submitBtn = document.querySelector('.login-btn');
    const btnText = document.querySelector('.btn-text');
    const spinner = document.querySelector('.spinner');

    // Initialize auto-connect preference
    initializeAutoConnect();
    
    // Initialize ad tracking
    initializeAdTracking();

    // Form submission handling
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Show spinner, hide text
            btnText.style.opacity = '0';
            spinner.style.display = 'block';
            
            // Track login attempt
            trackEvent('login_attempt', {
                'login_method': getLoginMethod()
            });
            
            // Simulate loading (will be replaced by actual form submission)
            setTimeout(function() {
                // Form will submit naturally after this
            }, 500);
        });
    }

    // Quick login functionality
    window.quickLogin = function(username, password) {
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const autoConnect = document.getElementById('autoConnect');
        
        if (usernameInput && passwordInput) {
            usernameInput.value = username;
            passwordInput.value = password;
            
            // Visual feedback
            usernameInput.style.background = '#e0f2fe';
            passwordInput.style.background = '#e0f2fe';
            
            setTimeout(function() {
                usernameInput.style.background = '';
                passwordInput.style.background = '';
            }, 1000);
            
            // Track quick login usage
            trackEvent('quick_login_used', {
                'login_type': username
            });
            
            // Auto-submit if auto-connect is enabled
            if (autoConnect && autoConnect.checked) {
                setTimeout(function() {
                    form.submit();
                }, 800);
            }
        }
    };

    // Auto-focus on first empty input
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    
    if (usernameInput && !usernameInput.value) {
        usernameInput.focus();
    } else if (passwordInput && !passwordInput.value) {
        passwordInput.focus();
    }

    // Enhanced form validation
    const inputs = document.querySelectorAll('input[required]');
    inputs.forEach(function(input) {
        input.addEventListener('invalid', function(e) {
            e.preventDefault();
            this.classList.add('error');
            
            // Remove error class after user starts typing
            this.addEventListener('input', function() {
                this.classList.remove('error');
            }, { once: true });
        });
    });

    // Redirect handling after successful login
    function handleSuccessfulLogin() {
        submitBtn.classList.remove('loading');
        submitBtn.classList.add('success');
        btnText.textContent = 'წარმატებულია!';
        btnText.style.opacity = '1';
        spinner.style.display = 'none';
        
        // Show success message
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.innerHTML = `
            <span class="icon">✅</span>
            <span>ავტორიზაცია წარმატებულია! გადამისამართება...</span>
        `;
        
        form.appendChild(successDiv);
        
        // Track successful login
        trackEvent('login_success', {
            'login_method': getLoginMethod()
        });
        
        // Handle auto-connect flow
        const autoConnect = document.getElementById('autoConnect');
        if (autoConnect && autoConnect.checked) {
            handleAutoConnectFlow();
        } else {
            // Standard redirect
            setTimeout(function() {
                window.location.href = 'https://splito.ge';
            }, 3000);
        }
    }

    // Auto-connect flow with advertisement
    function handleAutoConnectFlow() {
        // Show Tower Group ad first
        showTowerGroupModal();
        
        // After 3 seconds, close ad and redirect
        setTimeout(function() {
            closeTowerGroupModal();
            window.location.href = 'https://splito.ge';
        }, 5000);
    }

    // Tower Group modal functions
    function showTowerGroupModal() {
        const modal = createTowerGroupModal();
        document.body.appendChild(modal);
        
        // Track ad impression
        trackEvent('ad_impression', {
            'ad_name': 'tower_group_modal',
            'trigger': 'auto_connect'
        });
        
        setTimeout(function() {
            modal.classList.add('show');
        }, 100);
    }

    function createTowerGroupModal() {
        const modal = document.createElement('div');
        modal.className = 'tower-group-modal';
        modal.innerHTML = `
            <div class="modal-overlay" onclick="closeTowerGroupModal()"></div>
            <div class="modal-content">
                <button class="modal-close" onclick="closeTowerGroupModal()">×</button>
                <div class="modal-header">
                    <h2>Tower Group</h2>
                    <p>Summer 2024 - ახალი პროექტი</p>
                </div>
                <div class="modal-body">
                    <div class="modal-image">
                        <img src="images/tower-group-modal.jpg" alt="Tower Group" onerror="this.style.display='none'">
                    </div>
                    <p>თაუერ გრუპის პანორამა ელდარია ინვესტირებაში. ახვეცეთერ პროექტები და ვითბალშეთ ბეთების ბომბვალები მოდალები.</p>
                    <button class="modal-cta" onclick="openTowerGroupSite()">
                        გაიგეთ მეტი
                    </button>
                </div>
            </div>
        `;
        return modal;
    }

    window.closeTowerGroupModal = function() {
        const modal = document.querySelector('.tower-group-modal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(function() {
                modal.remove();
            }, 300);
        }
    };

    window.openTowerGroupSite = function() {
        trackEvent('ad_click', {
            'ad_name': 'tower_group_modal',
            'source': 'auto_connect'
        });
        window.open('https://towergroup.ge', '_blank');
        closeTowerGroupModal();
    };

    // Check if login was successful (this will be determined by MikroTik)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('login') === 'success' || window.location.href.indexOf('status') > -1) {
        handleSuccessfulLogin();
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Enter key submits form if focused on input
        if (e.key === 'Enter' && (e.target.tagName === 'INPUT')) {
            const form = e.target.closest('form');
            if (form) {
                const submitButton = form.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.click();
                }
            }
        }
    });

    // Session timer (5 minutes = 300 seconds)
    let sessionTime = 300;
    const timerDisplay = document.querySelector('.session-timer');
    
    function updateTimer() {
        if (sessionTime > 0) {
            const minutes = Math.floor(sessionTime / 60);
            const seconds = sessionTime % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timerDisplay) {
                timerDisplay.textContent = display;
            }
            
            sessionTime--;
            setTimeout(updateTimer, 1000);
        } else {
            // Session expired
            if (timerDisplay) {
                timerDisplay.textContent = 'სესია ამოიწურა';
                timerDisplay.style.color = '#dc2626';
            }
        }
    }

    // Start timer if user is logged in
    if (window.location.href.indexOf('status') > -1) {
        updateTimer();
    }

    // Add some visual enhancements
    const card = document.querySelector('.login-card');
    if (card) {
        // Add subtle hover effect to card
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 20px 40px -5px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
    }

    // Helper functions
    function initializeAutoConnect() {
        const autoConnectCheckbox = document.getElementById('autoConnect');
        if (autoConnectCheckbox) {
            // Load saved preference
            const savedAutoConnect = localStorage.getItem('autoConnect');
            if (savedAutoConnect !== null) {
                autoConnectCheckbox.checked = savedAutoConnect === 'true';
            }
            
            // Save preference when changed
            autoConnectCheckbox.addEventListener('change', function() {
                localStorage.setItem('autoConnect', this.checked);
                trackEvent('auto_connect_toggled', {
                    'enabled': this.checked
                });
            });
        }
    }

    function initializeAdTracking() {
        // Track page view
        trackEvent('hotspot_page_view', {
            'page': 'login',
            'timestamp': new Date().toISOString()
        });
        
        // Track ad banner impression
        const adBanner = document.querySelector('.ad-banner');
        if (adBanner) {
            trackEvent('ad_impression', {
                'ad_name': 'tower_group_banner',
                'position': 'top'
            });
        }
    }

    function getLoginMethod() {
        const username = document.getElementById('username')?.value || '';
        if (username === 'guest') return 'quick_guest';
        if (username === 'free') return 'quick_free';
        return 'manual';
    }

    function trackEvent(eventName, parameters = {}) {
        // Google Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, parameters);
        }
        
        // Console logging for debugging
        console.log('Event tracked:', eventName, parameters);
        
        // Could also send to custom analytics endpoint
        // sendToAnalytics(eventName, parameters);
    }

    // Advertisement banner click handler
    window.openTowerGroupAd = function() {
        trackEvent('ad_click', {
            'ad_name': 'tower_group_banner',
            'position': 'top'
        });
        window.open('https://towergroup.ge', '_blank');
    };
});

// Additional modal styles (will be injected)
const modalStyles = `
<style>
.tower-group-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.tower-group-modal.show {
    opacity: 1;
    visibility: visible;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 16px;
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    font-size: 18px;
    cursor: pointer;
    z-index: 1;
    transition: background 0.2s ease;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 20px 20px;
    text-align: center;
}

.modal-header h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.modal-header p {
    opacity: 0.9;
    font-size: 14px;
}

.modal-body {
    padding: 25px 20px;
    text-align: center;
}

.modal-image {
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.modal-body p {
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 20px;
    font-size: 14px;
}

.modal-cta {
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.modal-cta:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}
</style>
`;

// Inject modal styles
document.head.insertAdjacentHTML('beforeend', modalStyles);
