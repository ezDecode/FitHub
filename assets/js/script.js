/**
 * Simple Gym Management System - Main JavaScript
 * Version: 1.0.0
 */

// ===========================
// Document Ready
// ===========================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Gym Management System Loaded');
    
    // Initialize all functions
    initDeleteConfirmations();
    initFormValidation();
    updateCurrentTime();
    initTooltips();
    initAlertAutoClose();
});

// ===========================
// Delete Confirmations
// ===========================
function initDeleteConfirmations() {
    const deleteLinks = document.querySelectorAll('a[href*="delete="]');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });
    });
}

// ===========================
// Form Validation
// ===========================
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Email validation
            const emailInputs = form.querySelectorAll('input[type="email"]');
            emailInputs.forEach(input => {
                if (input.value && !isValidEmail(input.value)) {
                    e.preventDefault();
                    showError(input, 'Please enter a valid email address');
                }
            });
            
            // Phone validation (10 digits)
            const phoneInputs = form.querySelectorAll('input[name="phone"]');
            phoneInputs.forEach(input => {
                if (input.value && !isValidPhone(input.value)) {
                    e.preventDefault();
                    showError(input, 'Please enter a valid 10-digit phone number');
                }
            });
        });
    });
}

// ===========================
// Validation Functions
// ===========================
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function isValidPhone(phone) {
    const regex = /^[0-9]{10}$/;
    return regex.test(phone);
}

function showError(input, message) {
    input.classList.add('is-invalid');
    
    // Remove existing error message
    const existingError = input.parentElement.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    input.parentElement.appendChild(errorDiv);
    
    // Remove error on input
    input.addEventListener('input', function() {
        input.classList.remove('is-invalid');
        if (errorDiv) errorDiv.remove();
    });
}

// ===========================
// Update Current Time (for attendance page)
// ===========================
function updateCurrentTime() {
    const timeInput = document.getElementById('currentTime');
    
    if (timeInput) {
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour12: false });
            timeInput.value = timeString;
        }
        
        updateTime();
        setInterval(updateTime, 1000);
    }
}

// ===========================
// Initialize Bootstrap Tooltips
// ===========================
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// ===========================
// Auto-close Alerts
// ===========================
function initAlertAutoClose() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // Close after 5 seconds
    });
}

// ===========================
// Member Search Filter (for members page)
// ===========================
function filterMembers(searchText) {
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
// Export Table to CSV
// ===========================
function exportTableToCSV(filename) {
    const table = document.querySelector('table');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length - 1; j++) { // Skip last column (actions)
            row.push(cols[j].innerText);
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
// Print Function
// ===========================
function printPage() {
    window.print();
}

// ===========================
// Format Date
// ===========================
function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

// ===========================
// Calculate Days Between Dates
// ===========================
function daysBetween(date1, date2) {
    const oneDay = 24 * 60 * 60 * 1000;
    const firstDate = new Date(date1);
    const secondDate = new Date(date2);
    
    return Math.round(Math.abs((firstDate - secondDate) / oneDay));
}

// ===========================
// Show Loading Spinner
// ===========================
function showLoading(container) {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-container';
    spinner.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="loading-text mt-3">Loading data...</p>
        </div>
    `;
    
    if (container) {
        container.innerHTML = '';
        container.appendChild(spinner);
    }
}

// ===========================
// Hide Loading Spinner
// ===========================
function hideLoading(container) {
    if (container) {
        const spinner = container.querySelector('.spinner-container');
        if (spinner) {
            spinner.remove();
        }
    }
}

// ===========================
// Show Toast Notification
// ===========================
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    
    const toastId = 'toast-' + Date.now();
    const bgColor = type === 'success' ? 'bg-success' : 
                    type === 'error' ? 'bg-danger' : 
                    type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.innerHTML += toastHTML;
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove after hiding
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// ===========================
// Smooth Scroll
// ===========================
function smoothScroll(target) {
    document.querySelector(target).scrollIntoView({
        behavior: 'smooth'
    });
}

// ===========================
// Copy to Clipboard
// ===========================
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!', 'success');
    }, function() {
        showToast('Failed to copy', 'error');
    });
}

// ===========================
// Debounce Function
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
// Console Welcome Message
// ===========================
console.log('%c🏋️ Gym Management System v1.0.0', 'color: #667eea; font-size: 20px; font-weight: bold;');
console.log('%cDeveloped with ❤️ for learning PHP & MySQL', 'color: #764ba2; font-size: 14px;');
