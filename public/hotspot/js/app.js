// Splito Hotspot Login JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    const submitBtn = document.querySelector('.login-btn');
    const btnText = document.querySelector('.btn-text');
    const spinner = document.querySelector('.spinner');

    // Form submission handling
    if (form) {
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Show spinner, hide text
            btnText.style.opacity = '0';
            spinner.style.display = 'block';
            
            // Simulate loading (will be replaced by actual form submission)
            setTimeout(function() {
                // Form will submit naturally after this
            }, 500);
        });
    }

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
        
        // Redirect to splito.ge after 3 seconds
        setTimeout(function() {
            window.location.href = 'https://splito.ge';
        }, 3000);
    }

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
});
