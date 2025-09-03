// Additional JavaScript functionality for the raffle wheel

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success/error messages after 5 seconds
    const messages = document.querySelectorAll('.success, .error');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
    
    // Add hover effects to wheel segments
    const wheelSegments = document.querySelectorAll('.wheel-segment');
    wheelSegments.forEach(segment => {
        segment.addEventListener('mouseenter', function() {
            this.style.transform = this.style.transform + ' scale(1.02)';
            this.style.zIndex = '5';
        });
        
        segment.addEventListener('mouseleave', function() {
            this.style.transform = this.style.transform.replace(' scale(1.02)', '');
            this.style.zIndex = '1';
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Press 'S' to spin the wheel (if available)
        if (e.key.toLowerCase() === 's' || e.key === ' ') {
            e.preventDefault();
            const spinBtn = document.getElementById('spinBtn');
            if (spinBtn && !spinBtn.disabled) {
                spinBtn.click();
            }
        }
        
        // Press 'R' to refresh/reload the page
        if (e.key.toLowerCase() === 'r' && e.ctrlKey) {
            e.preventDefault();
            window.location.reload();
        }
    });
    
    // Form validation enhancements
    const nameInputs = document.querySelectorAll('input[name="name"]');
    nameInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const feedback = this.nextElementSibling || document.createElement('div');
            
            if (!this.nextElementSibling) {
                feedback.className = 'input-feedback';
                this.parentNode.appendChild(feedback);
            }
            
            if (value.length > 20) {
                feedback.textContent = `Too long! ${value.length}/20 characters`;
                feedback.className = 'input-feedback error-text';
                this.style.borderColor = '#e74c3c';
            } else if (value.length > 15) {
                feedback.textContent = `${value.length}/20 characters`;
                feedback.className = 'input-feedback warning-text';
                this.style.borderColor = '#f39c12';
            } else if (value.length > 0) {
                feedback.textContent = `${value.length}/20 characters`;
                feedback.className = 'input-feedback success-text';
                this.style.borderColor = '#27ae60';
            } else {
                feedback.textContent = '';
                this.style.borderColor = '#e1e8ed';
            }
        });
    });
    
    // Smooth scroll for navigation
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add loading state to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
                
                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }, 3000);
            }
        });
    });
    
    // Real-time participant count updates (for multiple users)
    if (window.location.pathname.includes('/signup/')) {
        // Update participant list every 10 seconds
        setInterval(function() {
            const participantsList = document.querySelector('.participants');
            if (participantsList) {
                fetch(window.location.href + '?ajax=count')
                    .then(response => response.text())
                    .then(html => {
                        // Update only if the content changed
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newList = doc.querySelector('.participants');
                        if (newList && newList.innerHTML !== participantsList.innerHTML) {
                            participantsList.innerHTML = newList.innerHTML;
                        }
                    })
                    .catch(() => {
                        // Silently fail - not critical
                    });
            }
        }, 10000);
    }
    
    // Confirmation dialogs for destructive actions
    const deleteButtons = document.querySelectorAll('button[name="delete_raffle"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const raffleTitle = this.closest('tr').querySelector('.raffle-title').textContent;
            const confirmed = confirm(`Are you sure you want to delete the raffle "${raffleTitle}"? This action cannot be undone.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
    
    // Copy URL to clipboard functionality (if we want to add it later)
    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('URL copied to clipboard!');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showToast('URL copied to clipboard!');
            } catch (err) {
                console.error('Could not copy text: ', err);
            }
            document.body.removeChild(textArea);
        }
    }
    
    // Toast notification system
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        
        // Style the toast
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '12px 20px',
            borderRadius: '6px',
            color: 'white',
            fontWeight: '500',
            zIndex: '1000',
            opacity: '0',
            transform: 'translateY(-20px)',
            transition: 'all 0.3s ease'
        });
        
        // Set background color based on type
        const colors = {
            info: '#3498db',
            success: '#27ae60',
            warning: '#f39c12',
            error: '#e74c3c'
        };
        toast.style.backgroundColor = colors[type] || colors.info;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Make functions available globally if needed
    window.raffly = {
        copyToClipboard,
        showToast
    };
});
