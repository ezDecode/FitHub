/**
 * Gym Management System - Enhanced Mobile-First JavaScript
 * Version: 3.0.0
 * Pure Vanilla JavaScript - No Dependencies
 */

// ===========================
// GLOBAL STATE
// ===========================
const GymApp = {
 isMobile: window.innerWidth < 768,
 isTablet: window.innerWidth >= 768 && window.innerWidth < 1024,
 isDesktop: window.innerWidth >= 1024,
 touchDevice: 'ontouchstart' in window
};

// ===========================
// DOCUMENT READY
// ===========================
document.addEventListener('DOMContentLoaded', function() {
 console.log('%c Gym Management System v3.0.0', 'color: #667eea; font-size: 20px; font-weight: bold;');
 console.log('%cMobile-First Modern UI Loaded', 'color: #764ba2; font-size: 14px;');
 
 // Initialize all features
 initializeApp();
});

// ===========================
// INITIALIZE APPLICATION
// ===========================
function initializeApp() {
 initMobileMenu();
 initFormValidation();
 initConfirmations();
 updateCurrent();
 autoCloseAlerts();
 initTouchFeeack();
 initLazyLoading();
 initResponsiveTableScroll();
 
 // resize listener for responsive updates
 let resizer;
 window.addEventListener('resize', function() {
 clearTimeout(resizer);
 resizer = setTimeout(function() {
 updateResponsiveState();
 }, 250);
 });
}

// ===========================
// UPDATE RESPONSIVE STATE
// ===========================
function updateResponsiveState() {
 GymApp.isMobile = window.innerWidth < 768;
 GymApp.isTablet = window.innerWidth >= 768 && window.innerWidth < 1024;
 GymApp.isDesktop = window.innerWidth >= 1024;
}

// ===========================
// MOBILE MENU TOGGLE
// Enhanced with smooth animations
// ===========================
function initMobileMenu() {
 const toggle = document.querySelector('.navbar-toggle');
 const menu = document.getElementById('navMenu');
 
 if (!toggle || !menu) return;
 
 toggle.addEventListener('click', function(e) {
 e.stopPropagation();
 toggleMenu();
 });
 
 // Close menu when clicking a link
 const menuLinks = menu.querySelectorAll('a');
 menuLinks.forEach(link => {
 link.addEventListener('click', function() {
 if (GymApp.isMobile) {
 menu.classList.remove('');
 document.body.style.overflow = '';
 }
 });
 });
 
 // Close menu when clicking outside
 document.addEventListener('click', function(e) {
 if (menu.classList.contains('') && 
 !menu.contains(e.target) && 
 !toggle.contains(e.target)) {
 menu.classList.remove('');
 document.body.style.overflow = '';
 }
 });
 
 // Close menu on escape key
 document.addEventListener('keydown', function(e) {
 if (e.key === 'Escape' && menu.classList.contains('')) {
 menu.classList.remove('');
 document.body.style.overflow = '';
 }
 });
}

function toggleMenu() {
 const menu = document.getElementById('navMenu');
 if (!menu) return;
 
 menu.classList.toggle('');
 
 // Prevent body scroll when menu is open
 if (menu.classList.contains('')) {
 document.body.style.overflow = 'hidden';
 } else {
 document.body.style.overflow = '';
 }
}

// ===========================
// FORM VALIDATION
// Enhanced with real- feeack
// ===========================
function initFormValidation() {
 const forms = document.querySelectorAll('form');
 
 forms.forEach(form => {
 // Email validation
 const emailInputs = form.querySelectorAll('input[type="email"]');
 emailInputs.forEach(input => {
 input.addEventListener('input', debounce(function() {
 if (this.value && !isValidEmail(this.value)) {
 showError(this, 'Please enter a valid email ress');
 } else {
 clearError(this);
 }
 }, 500));
 
 input.addEventListener('blur', function() {
 if (this.value && !isValidEmail(this.value)) {
 showError(this, 'Please enter a valid email ress');
 }
 });
 });
 
 // Phone validation
 const phoneInputs = form.querySelectorAll('input[name="phone"]');
 phoneInputs.forEach(input => {
 input.addEventListener('input', function() {
 // Only allow numbers
 this.value = this.value.replace(/[^0-9]/g, '');
 
 // Real- validation
 if (this.value.length > 0 && this.value.length !== 10) {
 showError(this, 'Phone number must be exactly 10 digits');
 } else {
 clearError(this);
 }
 });
 
 input.addEventListener('blur', function() {
 if (this.value && !isValidPhone(this.value)) {
 showError(this, 'Please enter a valid 10-digit phone number');
 }
 });
 });
 
 // Required field validation
 const requiredInputs = form.querySelectorAll('[required]');
 requiredInputs.forEach(input => {
 input.addEventListener('blur', function() {
 if (!this.value.trim()) {
 showError(this, 'This field is required');
 } else {
 clearError(this);
 }
 });
 });
 
 // Form submit validation
 form.addEventListener('submit', function(e) {
 let hasErrors = false;
 
 requiredInputs.forEach(input => {
 if (!input.value.trim()) {
 showError(input, 'This field is required');
 hasErrors = true;
 }
 });
 
 if (hasErrors) {
 e.preventDefault();
 showToast('Please fill in all required fields', 'danger');
 
 // Scroll to first error
 const firstError = form.querySelector('.error');
 if (firstError) {
 firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
 }
 }
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
 
 // new error with animation
 const errorDiv = document.createElement('div');
 errorDiv.className = 'error-message';
 errorDiv.textContent = message;
 errorDiv.style.animation = 'slideDown 0.3s ease';
 input.parentElement.appendChild(errorDiv);
 
 // haptic feeack on mobile
 if (GymApp.touchDevice && navigator.vibrate) {
 navigator.vibrate(50);
 }
}

function clearError(input) {
 input.classList.remove('error');
 const errorMsg = input.parentElement.querySelector('.error-message');
 if (errorMsg) {
 errorMsg.style.animation = 'fadeOut 0.2s ease';
 setTimeout(() => errorMsg.remove(), 200);
 }
}

// ===========================
// CONFIRMATIONS
// Enhanced with custom modal
// ===========================
function initConfirmations() {
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to delete this item?';
            const memberName = this.getAttribute('data-member-name') || 'this member';
            showDeleteModal(message, memberName, this);
        });
    });
}

// ===========================
// DELETE CONFIRMATION MODAL
// ===========================
function showDeleteModal(message, memberName, form) {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay show';
    overlay.id = 'deleteModal';
    
    overlay.innerHTML = `
        <div class="modal">
            <div class="modal-header">
                <span class="material-symbols-rounded modal-header-icon" style="color: #ef4444;">warning</span>
                <h3 class="modal-title">Confirm Deletion</h3>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">
                    ${message}<br><br>
                    <strong style="color: var(--white);">Member: ${memberName}</strong><br><br>
                    This action cannot be undone. All associated attendance records will remain in the system.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                    <span>Cancel</span>
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <span class="material-symbols-rounded">delete</span>
                    <span>Delete Member</span>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    
    // Store form reference for confirmation
    window.pendingDeleteForm = form;
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeDeleteModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', handleEscapeKey);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    }
    document.removeEventListener('keydown', handleEscapeKey);
    window.pendingDeleteForm = null;
}

function confirmDelete() {
    if (window.pendingDeleteForm) {
        closeDeleteModal();
        
        // Show loading toast
        showToast('Deleting member...', 'info', 'Deleting');
        
        // Submit the form
        window.pendingDeleteForm.submit();
    }
}

function handleEscapeKey(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
}

// ===========================
// REAL- CLOCK UPDATE
// Enhanced with better formatting
// ===========================
function updateCurrent() {
 const Input = document.getElementById('current');
 
 if (Input) {
 function update() {
 const now = new Date();
 const hours = String(now.getHours()).padStart(2, '0');
 const minutes = String(now.getMinutes()).padStart(2, '0');
 const seconds = String(now.getSeconds()).padStart(2, '0');
 Input.value = hours + ':' + minutes + ':' + seconds;
 }
 
 update();
 setInterval(update, 1000);
 }
}

// ===========================
// AUTO-CLOSE ALERTS
// Enhanced with smooth fade-out
// ===========================
function autoCloseAlerts() {
 const alerts = document.querySelectorAll('.alert');
 
 alerts.forEach(alert => {
 setTimeout(() => {
 alert.style.transition = 'all 0.5s ease';
 alert.style.opacity = '0';
 alert.style.transform = 'translateY(-10px)';
 setTimeout(() => {
 alert.style.display = 'none';
 }, 500);
 }, 5000);
 });
}

// ===========================
// TOUCH FEEACK
// ripple effect on touch devices
// ===========================
function initTouchFeeack() {
 if (!GymApp.touchDevice) return;
 
 const buttons = document.querySelectorAll('.btn, .card, .stats-card');
 
 buttons.forEach(element => {
 element.addEventListener('touchstart', function(e) {
 // class for visual feeack
 this.style.transform = 'scale(0.98)';
 });
 
 element.addEventListener('touchend', function(e) {
 // Remove class
 setTimeout(() => {
 this.style.transform = '';
 }, 100);
 });
 });
}

// ===========================
// LAZY LOADING
// Improve performance on mobile
// ===========================
function initLazyLoading() {
 const images = document.querySelectorAll('img[data-src]');
 
 if ('IntersectionObserver' in window) {
 const imageObserver = new IntersectionObserver((entries, observer) => {
 entries.forEach(entry => {
 if (entry.isIntersecting) {
 const img = entry.target;
 img.src = img.dataset.src;
 img.removeAttribute('data-src');
 observer.unobserve(img);
 }
 });
 });
 
 images.forEach(img => imageObserver.observe(img));
 } else {
 // Fallback for older browsers
 images.forEach(img => {
 img.src = img.dataset.src;
 img.removeAttribute('data-src');
 });
 }
}

// ===========================
// RESPONSIVE TABLE SCROLL HINT
// Show shadow when table can scroll
// ===========================
function initResponsiveTableScroll() {
 const tableContainers = document.querySelectorAll('.table-responsive');
 
 tableContainers.forEach(container => {
 const table = container.querySelector('table');
 if (!table) return;
 
 function updateScrollShadow() {
 const scrollLeft = container.scrollLeft;
 const scrollWidth = container.scrollWidth;
 const clientWidth = container.clientWidth;
 
 if (scrollWidth > clientWidth) {
 if (scrollLeft > 0) {
 container.style.boxShadow = 'inset 10px 0 10px -10px rgba(0,0,0,0.2)';
 } else {
 container.style.boxShadow = 'none';
 }
 }
 }
 
 container.addEventListener('scroll', updateScrollShadow);
 updateScrollShadow();
 });
}

// ===========================
// MODAL FUNCTIONS
// Enhanced with better animations
// ===========================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Focus first input in modal
    const firstInput = modal.querySelector('input, textarea, select');
    if (firstInput) {
 setTimeout(() => firstInput.focus(), 300);
 }
}

function closeModal(modalId) {
 const modal = document.getElementById(modalId);
 if (!modal) return;
 
 modal.classList.remove('show');
 document.body.style.overflow = '';
}

// Close modal on backdrop click
window.addEventListener('click', function(event) {
 if (event.target.classList.contains('modal')) {
 event.target.classList.remove('show');
 document.body.style.overflow = '';
 }
});

// ===========================
// TABLE /FILTER
// Enhanced with highlighting
// ===========================
function filterTable(Text) {
 const table = document.querySelector('table tbody');
 if (!table) return;
 
 const rows = table.getElementsByTagName('tr');
 Text = Text.toLowerCase().trim();
 let visibleCount = 0;
 
 for (let i = 0; i < rows.length; i++) {
 const row = rows[i];
 const text = row.textContent.toLowerCase();
 
 if (!Text || text.includes(Text)) {
 row.style.display = '';
 visibleCount++;
 
 // Highlight matching text
 if (Text) {
 highlightText(row, Text);
 }
 } else {
 row.style.display = 'none';
 }
 }
 
 // Show no results message
 if (visibleCount === 0 && Text) {
 showToast('No results found', 'info');
 }
}

function highlightText(element, Text) {
 // Remove existing highlights
 const highlighted = element.querySelectorAll('.highlight');
 highlighted.forEach(el => {
 el.outerHTML = el.textContent;
 });
 
 // new highlights
 const cells = element.getElementsByTagName('td');
 Array.from(cells).forEach(cell => {
 const text = cell.textContent;
 const lowerText = text.toLowerCase();
 const index = lowerText.indexOf(Text);
 
 if (index !== -1) {
 const before = text.substring(0, index);
 const match = text.substring(index, index + Text.length);
 const after = text.substring(index + Text.length);
 
 cell.innerHTML = before + 
 '<span class="highlight" style="background: yellow; padding: 2px 4px; border-radius: 3px;">' + 
 match + '</span>' + after;
 }
 });
}

// ===========================
// EXPORT TABLE TO CSV
// Enhanced for mobile
// ===========================
function exportTableToCSV(filename = 'gym-data.csv') {
 const table = document.querySelector('table');
 if (!table) {
 showToast('No table found to export', 'danger');
 return;
 }
 
 showLoading('Preparing export...');
 
 setTimeout(() => {
 let csv = [];
 const rows = table.querySelectorAll('tr');
 
 for (let i = 0; i < rows.length; i++) {
 const row = [];
 const cols = rows[i].querySelectorAll('td, th');
 
 // Skip actions column
 const colCount = cols[cols.length - 1]?.textContent.trim().toLowerCase() === 'actions' || 
 cols[cols.length - 1]?.textContent.trim().toLowerCase() === 'action'
 ? cols.length - 1 : cols.length;
 
 for (let j = 0; j < colCount; j++) {
 let data = cols[j].textContent.replace(/(\r\n|\n|\r)/gm, ' ').trim();
 data = data.replace(/"/g, '""');
 row.push('"' + data + '"');
 }
 
 csv.push(row.join(','));
 }
 
 downloadCSV(csv.join('\n'), filename);
 showToast('Export completed successfully!', 'success');
 }, 500);
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
 
 // Clean up
 window.URL.revokeObjectURL(downloadLink.href);
}

// ===========================
// PRINT FUNCTION
// ===========================
function printPage() {
 window.print();
}

// ===========================
// LOADING INDICATOR
// ===========================
function showLoading(message = 'Loading...') {
 const existingLoader = document.getElementById('global-loader');
 if (existingLoader) return;
 
 const loader = document.createElement('div');
 loader.id = 'global-loader';
 loader.style.cssText = `
 position: fixed;
 top: 0;
 left: 0;
 right: 0;
 bottom: 0;
 background: rgba(255, 255, 255, 0.95);
 backdrop-filter: blur(4px);
 z-index: 9999;
 display: flex;
 flex-direction: column;
 align-items: center;
 justify-content: center;
 gap: 1rem;
 `;
 
 loader.innerHTML = `
 <p style="color: #667eea; font-weight: 600;">${message}</p>
 `;
 
 document.body.appendChild(loader);
}

function hideLoading() {
 const loader = document.getElementById('global-loader');
 if (loader) {
 loader.style.opacity = '0';
 setTimeout(() => loader.remove(), 300);
 }
}

// ===========================
// TOAST NOTIFICATION
// Enhanced with icons and animations
// ===========================
function showToast(message, type = 'success', title = '') {
    const icons = {
        success: 'check_circle',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };
    
    const titles = {
        success: title || 'Success',
        error: title || 'Error',
        warning: title || 'Warning',
        info: title || 'Info'
    };
    
    // Create or get toast container
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="material-symbols-rounded toast-icon">${icons[type] || icons.info}</span>
        <div class="toast-content">
            <div class="toast-title">${titles[type]}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <span class="material-symbols-rounded">close</span>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Haptic feedback on mobile
    if (GymApp.touchDevice && navigator.vibrate) {
        navigator.vibrate(type === 'error' ? [50, 50, 50] : 50);
    }
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            if (container.children.length === 0) {
                container.remove();
            }
        }, 300);
    }, 5000);
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
 navigator.clipboard.writeText(text).then(
 () => showToast('Copied to clipboard!', 'success'),
 () => showToast('Failed to copy', 'danger')
 );
 } else {
 // Fallback
 const textArea = document.createElement('textarea');
 textArea.value = text;
 textArea.style.cssText = 'position:fixed;left:-999999px;';
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
// Optimize performance
// ===========================
function debounce(func, wait) {
 let out;
 return function executedFunction(...args) {
 const later = () => {
 clearTimeout(out);
 func.apply(this, args);
 };
 clearTimeout(out);
 out = setTimeout(later, wait);
 };
}

// ===========================
// THROTTLE FUNCTION
// Limit function calls
// ===========================
function throttle(func, limit) {
 let inThrottle;
 return function(...args) {
 if (!inThrottle) {
 func.apply(this, args);
 inThrottle = true;
 setTimeout(() => inThrottle = false, limit);
 }
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
// Prevent XSS
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
// SERVICE WORKER REGISTRATION
// Enable offline functionality (optional)
// ===========================
if ('serviceWorker' in navigator && window.location.protocol === 'https:') {
 window.addEventListener('load', function() {
 // Uncomment to enable service worker
 // navigator.serviceWorker.register('/sw.js')
 // .then(reg => console.log('Service Worker registered'))
 // .catch(err => console.log('Service Worker registration failed'));
 });
}

// ===========================
// PERFORMANCE MONITORING
// ===========================
window.addEventListener('load', function() {
 if ('performance' in window) {
 const perfData = window.performance.timing;
 const pageLoad = perfData.loadEventEnd - perfData.navigationStart;
 console.log(`%c Page loaded in ${pageLoad}ms`, 'color: #10b981; font-weight: bold;');
 }
});

// ===========================
// CONSOLE SIGNATURE
// ===========================
// console.log('%c----------------------------------------------------', 'color: #667eea;');
// console.log('%c Gym Management System v3.0.0', 'color: #667eea; font-size: 20px; font-weight: bold;');
// console.log('%c Mobile-First Responsive Design', 'color: #764ba2; font-size: 14px;');
// console.log('%c Modern UI with Pure CSS & JavaScript', 'color: #10b981; font-size: 14px;');
// console.log('%c----------------------------------------------------', 'color: #667eea;');




