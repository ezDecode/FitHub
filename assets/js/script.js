/**
 * Simple Gym Management System - Pure JavaScript
 * NO Bootstrap - Vanilla JS Only
 * Version: 2.0.0
 */

// ===========================
// DOCUMENT READY
// ===========================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🏋️ Gym Management System Loaded (Pure JS)');
    
    // Initialize functions
    initFormValidation();
    initDeleteConfirmations();
    updateCurrentTime();
    autoCloseAlerts();
});

// ===========================
// MOBILE MENU TOGGLE
// ===========================
function toggleMenu() {
    const menu = document.getElementById('navMenu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// ===========================
// FORM VALIDATION
// ===========================
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Email validation
        const emailInputs = form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value && !isValidEmail(this.value)) {
                    showError(this, 'Please enter a valid email address');
                } else {
                    clearError(this);
                }
            });
        });
        
        // Phone validation
        const phoneInputs = form.querySelectorAll('input[name="phone"]');
        phoneInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value && !isValidPhone(this.value)) {
                    showError(this, 'Please enter a valid 10-digit phone number');
                } else {
                    clearError(this);
                }
            });
            
            // Only allow numbers
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    });
}

function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function isValidPhone(phone) {
    const regex = /^[0-9]{10}$/;
    return regex.test(phone);
}

function showError(input, message) {
    input.classList.add('error');
    
    // Remove existing error
    const existingError = input.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
}

function clearError(input) {
    input.classList.remove('error');
    const errorMsg = input.parentElement.querySelector('.error-message');
    if (errorMsg) {
        errorMsg.remove();
    }
}

// ===========================
// DELETE CONFIRMATIONS
// ===========================
function initDeleteConfirmations() {
    const deleteLinks = document.querySelectorAll('a[href*="delete="]');
    
    deleteLinks.forEach(link => {
        // Remove existing onclick if present
        link.removeAttribute('onclick');
        
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });
    });
}

// ===========================
// REAL-TIME CLOCK UPDATE
// ===========================
function updateCurrentTime() {
    const timeInput = document.getElementById('currentTime');
    
    if (timeInput) {
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeInput.value = hours + ':' + minutes + ':' + seconds;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    }
}

// ===========================
// AUTO-CLOSE ALERTS
// ===========================
function autoCloseAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
}

// ===========================
// MODAL FUNCTIONS
// ===========================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Close modal on outside click
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
});

// ===========================
// TABLE SEARCH/FILTER
// ===========================
function filterTable(searchText) {
    const table = document.querySelector('table tbody');
    if (!table) return;
    
    const rows = table.getElementsByTagName('tr');
    searchText = searchText.toLowerCase();
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        
        if (text.includes(searchText)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// ===========================
// EXPORT TABLE TO CSV
// ===========================
function exportTableToCSV(filename = 'data.csv') {
    const table = document.querySelector('table');
    if (!table) {
        alert('No table found to export');
        return;
    }
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        // Skip last column if it's "Actions"
        const colCount = cols[cols.length - 1].textContent.trim() === 'Actions' ? cols.length - 1 : cols.length;
        
        for (let j = 0; j < colCount; j++) {
            let data = cols[j].textContent.replace(/(\r\n|\n|\r)/gm, ' ').trim();
            data = data.replace(/"/g, '""'); // Escape quotes
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// ===========================
// PRINT FUNCTION
// ===========================
function printPage() {
    window.print();
}

// ===========================
// SHOW LOADING SPINNER
// ===========================
function showLoading(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    spinner.innerHTML = `
        <div style="text-align: center; padding: 40px;">
            <div style="border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
            <p style="margin-top: 15px; color: #666;">Loading...</p>
        </div>
    `;
    
    container.innerHTML = '';
    container.appendChild(spinner);
    
    // Add spin animation if not exists
    if (!document.getElementById('spin-animation')) {
        const style = document.createElement('style');
        style.id = 'spin-animation';
        style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    }
}

function hideLoading(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const spinner = container.querySelector('.loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// ===========================
// TOAST NOTIFICATION
// ===========================
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'alert alert-' + type;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '10000';
    toast.style.minWidth = '250px';
    toast.style.animation = 'slideInRight 0.3s ease';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transition = 'opacity 0.5s';
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 500);
    }, 3000);
    
    // Add animation if not exists
    if (!document.getElementById('toast-animation')) {
        const style = document.createElement('style');
        style.id = 'toast-animation';
        style.textContent = '@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
        document.head.appendChild(style);
    }
}

// ===========================
// SMOOTH SCROLL
// ===========================
function smoothScroll(targetId) {
    const target = document.getElementById(targetId);
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ===========================
// COPY TO CLIPBOARD
// ===========================
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied to clipboard!', 'success');
        }, function() {
            showToast('Failed to copy', 'danger');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showToast('Copied to clipboard!', 'success');
        } catch (err) {
            showToast('Failed to copy', 'danger');
        }
        document.body.removeChild(textArea);
    }
}

// ===========================
// DEBOUNCE FUNCTION
// ===========================
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===========================
// FORMAT DATE
// ===========================
function formatDate(dateString, format = 'long') {
    const date = new Date(dateString);
    const options = format === 'long' 
        ? { year: 'numeric', month: 'long', day: 'numeric' }
        : { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// ===========================
// CALCULATE DAYS BETWEEN
// ===========================
function daysBetween(date1, date2) {
    const oneDay = 24 * 60 * 60 * 1000;
    const firstDate = new Date(date1);
    const secondDate = new Date(date2);
    return Math.round(Math.abs((firstDate - secondDate) / oneDay));
}

// ===========================
// ESCAPE HTML
// ===========================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===========================
// GET QUERY PARAMETER
// ===========================
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

// ===========================
// CONSOLE WELCOME MESSAGE
// ===========================
console.log('%c🏋️ Gym Management System v2.0.0', 'color: #667eea; font-size: 20px; font-weight: bold;');
console.log('%cBuilt with Pure HTML, CSS, JavaScript, PHP & MySQL', 'color: #764ba2; font-size: 14px;');
console.log('%cNO Bootstrap - 100% Custom Code!', 'color: #28a745; font-size: 14px; font-weight: bold;');
