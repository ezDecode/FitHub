/**
 * FitHub Gym Management System
 * Main JavaScript File
 * 
 * Common utilities and functions used throughout the application
 */

// ==============================================
// GLOBAL APP OBJECT
// ==============================================
const FitHub = {
    // Base configuration
    config: {
        baseUrl: window.location.origin + '/',
        apiUrl: window.location.origin + '/api/',
        csrfToken: null
    },
    
    // Initialize application
    init() {
        this.initCSRFToken();
        this.initSidebar();
        this.initTooltips();
        this.initConfirmDialogs();
    },
    
    // Get CSRF token
    initCSRFToken() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            this.config.csrfToken = metaToken.getAttribute('content');
        }
    },
    
    // Initialize sidebar toggle
    initSidebar() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-menu-overlay');
        
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                if (overlay) {
                    overlay.classList.toggle('show');
                }
            });
            
            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        }
    },
    
    // Initialize tooltips
    initTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            element.addEventListener('mouseenter', function() {
                const text = this.getAttribute('data-tooltip');
                this.setAttribute('title', text);
            });
        });
    },
    
    // Initialize confirm dialogs
    initConfirmDialogs() {
        const confirmLinks = document.querySelectorAll('[data-confirm]');
        confirmLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const message = this.getAttribute('data-confirm') || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }
};

// ==============================================
// AJAX UTILITY FUNCTIONS
// ==============================================

/**
 * Make AJAX GET request
 * @param {string} url - Request URL
 * @param {object} params - Query parameters
 * @returns {Promise} Response data
 */
async function ajaxGet(url, params = {}) {
    try {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;
        
        const response = await fetch(fullUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('AJAX GET Error:', error);
        throw error;
    }
}

/**
 * Make AJAX POST request
 * @param {string} url - Request URL
 * @param {object|FormData} data - Request data
 * @returns {Promise} Response data
 */
async function ajaxPost(url, data = {}) {
    try {
        let body;
        let headers = {
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        if (data instanceof FormData) {
            body = data;
        } else {
            body = new URLSearchParams(data);
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        
        // Add CSRF token if available
        if (FitHub.config.csrfToken) {
            if (body instanceof FormData) {
                body.append('csrf_token', FitHub.config.csrfToken);
            } else {
                body.append('csrf_token', FitHub.config.csrfToken);
            }
        }
        
        const response = await fetch(url, {
            method: 'POST',
            headers: headers,
            body: body
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('AJAX POST Error:', error);
        throw error;
    }
}

/**
 * Make AJAX request with JSON data
 * @param {string} url - Request URL
 * @param {object} data - Request data
 * @param {string} method - HTTP method
 * @returns {Promise} Response data
 */
async function ajaxJSON(url, data = {}, method = 'POST') {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('AJAX JSON Error:', error);
        throw error;
    }
}

// ==============================================
// UI HELPER FUNCTIONS
// ==============================================

/**
 * Show loading spinner
 * @param {HTMLElement} element - Element to show spinner in
 */
function showLoading(element) {
    if (element) {
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        element.disabled = true;
    }
}

/**
 * Hide loading spinner
 * @param {HTMLElement} element - Element to hide spinner from
 * @param {string} originalText - Original text to restore
 */
function hideLoading(element, originalText) {
    if (element) {
        element.innerHTML = originalText;
        element.disabled = false;
    }
}

/**
 * Show alert message
 * @param {string} message - Message text
 * @param {string} type - Alert type (success, error, warning, info)
 * @param {number} duration - Auto-hide duration in milliseconds
 */
function showAlert(message, type = 'info', duration = 5000) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const iconMap = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${iconMap[type]}"></i>
        <span>${message}</span>
    `;
    
    alertContainer.appendChild(alert);
    
    if (duration > 0) {
        setTimeout(() => {
            alert.style.animation = 'slideOut 0.3s ease-in-out';
            setTimeout(() => alert.remove(), 300);
        }, duration);
    }
}

/**
 * Create alert container if it doesn't exist
 * @returns {HTMLElement} Alert container
 */
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '10000';
    container.style.minWidth = '300px';
    document.body.appendChild(container);
    return container;
}

/**
 * Show confirmation dialog using SweetAlert2
 * @param {string} title - Dialog title
 * @param {string} text - Dialog text
 * @param {Function} callback - Callback function if confirmed
 */
function confirmDialog(title, text, callback) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF6B35',
            cancelButtonColor: '#95A5A6',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    } else {
        if (confirm(text)) {
            if (typeof callback === 'function') {
                callback();
            }
        }
    }
}

/**
 * Show success toast
 * @param {string} message - Message text
 */
function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    } else {
        showAlert(message, 'success');
    }
}

/**
 * Show error toast
 * @param {string} message - Message text
 */
function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    } else {
        showAlert(message, 'error');
    }
}

// ==============================================
// FORM UTILITIES
// ==============================================

/**
 * Serialize form data to object
 * @param {HTMLFormElement} form - Form element
 * @returns {object} Form data as object
 */
function serializeForm(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        if (data[key]) {
            if (!Array.isArray(data[key])) {
                data[key] = [data[key]];
            }
            data[key].push(value);
        } else {
            data[key] = value;
        }
    }
    
    return data;
}

/**
 * Validate form fields
 * @param {HTMLFormElement} form - Form element
 * @returns {boolean} True if valid, false otherwise
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Clear form validation
 * @param {HTMLFormElement} form - Form element
 */
function clearValidation(form) {
    const inputs = form.querySelectorAll('.is-invalid, .is-valid');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
}

/**
 * Reset form
 * @param {HTMLFormElement} form - Form element
 */
function resetForm(form) {
    form.reset();
    clearValidation(form);
}

// ==============================================
// DATA FORMATTING FUNCTIONS
// ==============================================

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @param {string} symbol - Currency symbol
 * @returns {string} Formatted currency
 */
function formatCurrency(amount, symbol = '₹') {
    return `${symbol}${parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
}

/**
 * Format date
 * @param {string} date - Date string
 * @param {object} options - Formatting options
 * @returns {string} Formatted date
 */
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    
    const opts = { ...defaultOptions, ...options };
    return new Date(date).toLocaleDateString('en-IN', opts);
}

/**
 * Format datetime
 * @param {string} datetime - Datetime string
 * @returns {string} Formatted datetime
 */
function formatDateTime(datetime) {
    return new Date(datetime).toLocaleString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Get relative time (e.g., "2 hours ago")
 * @param {string} datetime - Datetime string
 * @returns {string} Relative time
 */
function getRelativeTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
    
    return formatDate(datetime);
}

// ==============================================
// STRING UTILITIES
// ==============================================

/**
 * Truncate string
 * @param {string} str - String to truncate
 * @param {number} length - Maximum length
 * @returns {string} Truncated string
 */
function truncate(str, length = 50) {
    return str.length > length ? str.substring(0, length) + '...' : str;
}

/**
 * Capitalize first letter
 * @param {string} str - String to capitalize
 * @returns {string} Capitalized string
 */
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Escape HTML
 * @param {string} str - String to escape
 * @returns {string} Escaped string
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ==============================================
// STORAGE UTILITIES
// ==============================================

/**
 * Set local storage item
 * @param {string} key - Storage key
 * @param {*} value - Value to store
 */
function setStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (e) {
        console.error('Storage Error:', e);
    }
}

/**
 * Get local storage item
 * @param {string} key - Storage key
 * @returns {*} Stored value
 */
function getStorage(key) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
    } catch (e) {
        console.error('Storage Error:', e);
        return null;
    }
}

/**
 * Remove local storage item
 * @param {string} key - Storage key
 */
function removeStorage(key) {
    try {
        localStorage.removeItem(key);
    } catch (e) {
        console.error('Storage Error:', e);
    }
}

// ==============================================
// INITIALIZATION
// ==============================================

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    FitHub.init();
});

// Export functions for use in other scripts
window.FitHub = FitHub;
window.ajaxGet = ajaxGet;
window.ajaxPost = ajaxPost;
window.ajaxJSON = ajaxJSON;
window.showAlert = showAlert;
window.showSuccess = showSuccess;
window.showError = showError;
window.confirmDialog = confirmDialog;
window.serializeForm = serializeForm;
window.validateForm = validateForm;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;
