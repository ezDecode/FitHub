/**
 * ========================================
 * FITHUB GYM MANAGEMENT SYSTEM
 * Simple JavaScript for Beginners
 * ========================================
 */

// ========================================
// WHEN PAGE LOADS, RUN THIS CODE
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Setup mobile menu
    setupMobileMenu();
    
    // Setup form validation
    setupFormValidation();
    
    // Setup delete confirmations
    setupDeleteConfirmations();
    
    // Auto-hide alerts after 5 seconds
    autoHideAlerts();
    
    // Update time displays
    updateTimeDisplays();
});

// ========================================
// MOBILE MENU - Show/Hide navigation on mobile
// ========================================
function setupMobileMenu() {
    // Get the menu button
    var menuButton = document.querySelector('.navbar-toggle');
    var menu = document.getElementById('navbarMenu');
    
    // If menu button exists
    if (menuButton && menu) {
        // When button is clicked, toggle menu
        menuButton.addEventListener('click', function() {
            menu.classList.toggle('active');
        });
        
        // When clicking a menu link, close the menu
        var menuLinks = menu.querySelectorAll('a');
        for (var i = 0; i < menuLinks.length; i++) {
            menuLinks[i].addEventListener('click', function() {
                menu.classList.remove('active');
            });
        }
    }
}

// ========================================
// AUTO-HIDE ALERTS - Hide messages after 5 seconds
// ========================================
function autoHideAlerts() {
    // Wait 5 seconds (5000 milliseconds)
    setTimeout(function() {
        // Find all alert boxes
        var alerts = document.querySelectorAll('.alert');
        
        // Hide each alert
        for (var i = 0; i < alerts.length; i++) {
            alerts[i].style.display = 'none';
        }
    }, 5000);
}

// ========================================
// SHOW TOAST NOTIFICATION - Show popup message
// ========================================
function showToast(message, type, title) {
    // Choose icon based on type
    var icon = 'info';
    if (type === 'success') icon = 'check_circle';
    if (type === 'error') icon = 'error';
    if (type === 'warning') icon = 'warning';
    
    // Choose title based on type
    var toastTitle = title || 'Info';
    if (type === 'success') toastTitle = title || 'Success';
    if (type === 'error') toastTitle = title || 'Error';
    
    // Find or create toast container
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    // Create toast HTML
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = '<span class="material-symbols-rounded toast-icon">' + icon + '</span>' +
                     '<div class="toast-content">' +
                     '<div class="toast-title">' + toastTitle + '</div>' +
                     '<div class="toast-message">' + message + '</div>' +
                     '</div>' +
                     '<button class="toast-close" onclick="this.parentElement.remove()">' +
                     '<span class="material-symbols-rounded">close</span>' +
                     '</button>';
    
    // Add toast to page
    container.appendChild(toast);
    
    // Remove toast after 5 seconds
    setTimeout(function() {
        toast.remove();
    }, 5000);
}

// ========================================
// UPDATE TIME - Show current time in form
// ========================================
function updateTimeDisplays() {
    // Find the time input field
    var timeInput = document.getElementById('current_time');
    
    // If field exists, update it every second
    if (timeInput) {
        setInterval(function() {
            // Get current time
            var now = new Date();
            
            // Format: 14:30:25 (HH:MM:SS)
            var hours = String(now.getHours()).padStart(2, '0');
            var minutes = String(now.getMinutes()).padStart(2, '0');
            var seconds = String(now.getSeconds()).padStart(2, '0');
            
            // Update the field
            timeInput.value = hours + ':' + minutes + ':' + seconds;
        }, 1000); // Run every 1000 milliseconds (1 second)
    }
}

// ========================================
// UPDATE LIVE DURATIONS - For gym sessions in progress
// ========================================
function updateLiveDurations() {
    // Find all elements showing live duration
    var durationElements = document.querySelectorAll('[data-live-duration="true"]');
    
    // Update each one
    for (var i = 0; i < durationElements.length; i++) {
        var element = durationElements[i];
        
        // Get when they checked in (stored in data-check-in)
        var checkInTime = parseInt(element.getAttribute('data-check-in'));
        
        // Get current time
        var now = Math.floor(Date.now() / 1000);
        
        // Calculate how long they've been in gym
        var durationSeconds = now - checkInTime;
        var durationMinutes = Math.floor(durationSeconds / 60);
        var hours = Math.floor(durationMinutes / 60);
        var minutes = durationMinutes % 60;
        
        // Update display: "2h 30m"
        element.textContent = hours + 'h ' + minutes + 'm';
    }
}

// Update durations every 30 seconds
setInterval(updateLiveDurations, 30000);
// Also update when page loads
updateLiveDurations();

// ========================================
// DELETE CONFIRMATION - Ask before deleting
// ========================================
function setupDeleteConfirmations() {
    // Find all delete forms
    var deleteForms = document.querySelectorAll('form[data-confirm]');
    
    // Add confirmation to each form
    for (var i = 0; i < deleteForms.length; i++) {
        deleteForms[i].addEventListener('submit', function(e) {
            // Get member name
            var memberName = this.getAttribute('data-member-name') || 'this member';
            
            // Ask user to confirm
            var confirmed = confirm('Are you sure you want to delete ' + memberName + '? This cannot be undone.');
            
            // If user clicked "Cancel", stop the form
            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
}

// ========================================
// TOGGLE MEMBER FORM - Show/Hide add member form
// ========================================
function toggleMemberForm() {
    var formContainer = document.getElementById('memberFormContainer');
    var button = document.getElementById('toggleFormBtn');
    
    // Check if form is hidden
    if (formContainer.style.display === 'none' || formContainer.style.display === '') {
        // Show the form
        formContainer.style.display = 'block';
        button.innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
    } else {
        // Hide the form
        formContainer.style.display = 'none';
        button.innerHTML = '<span class="material-symbols-rounded">person_add</span><span>Add New Member</span>';
    }
}

// ========================================
// VALIDATE EMAIL - Check if email is valid
// ========================================
function isValidEmail(email) {
    // Email must have @ and . (dot)
    return email.includes('@') && email.includes('.');
}

// ========================================
// VALIDATE PHONE - Check if phone is valid
// ========================================
function isValidPhone(phone) {
    // Remove everything that's not a number
    var digitsOnly = phone.replace(/\D/g, '');
    
    // Must be exactly 10 digits
    return digitsOnly.length === 10;
}

// ========================================
// FORM VALIDATION - Check form inputs
// ========================================
function setupFormValidation() {
    // Find all forms on page
    var forms = document.querySelectorAll('form');
    
    // Setup validation for each form
    for (var i = 0; i < forms.length; i++) {
        var form = forms[i];
        
        // VALIDATE EMAIL FIELDS
        var emailInputs = form.querySelectorAll('input[type="email"]');
        for (var j = 0; j < emailInputs.length; j++) {
            emailInputs[j].addEventListener('blur', function() {
                // When user leaves the field, check if email is valid
                if (this.value && !isValidEmail(this.value)) {
                    alert('Please enter a valid email address');
                    this.focus();
                }
            });
        }
        
        // VALIDATE PHONE FIELDS
        var phoneInputs = form.querySelectorAll('input[name="phone"]');
        for (var k = 0; k < phoneInputs.length; k++) {
            // Only allow numbers while typing
            phoneInputs[k].addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Check length when done typing
            phoneInputs[k].addEventListener('blur', function() {
                if (this.value && !isValidPhone(this.value)) {
                    alert('Phone number must be exactly 10 digits');
                    this.focus();
                }
            });
        }
    }
}

