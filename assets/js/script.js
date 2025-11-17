document.addEventListener('DOMContentLoaded', function() {
    setupMobileMenu();
    setupFormValidation();
    setupDeleteConfirmations();
    autoHideAlerts();
    updateTimeDisplays();
});

function setupMobileMenu() {
    var menuButton = document.querySelector('.navbar-toggle');
    var menu = document.getElementById('navbarMenu');
    
    if (menuButton && menu) {
        menuButton.addEventListener('click', function() {
            if (menu.classList.contains('active')) {
                menu.classList.remove('active');
            } else {
                menu.classList.add('active');
            }
        });
        
        var menuLinks = menu.querySelectorAll('a');
        for (var i = 0; i < menuLinks.length; i++) {
            menuLinks[i].addEventListener('click', function() {
                menu.classList.remove('active');
            });
        }
    }
}

function autoHideAlerts() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        for (var i = 0; i < alerts.length; i++) {
            alerts[i].style.display = 'none';
        }
    }, 5000);
}

function showToast(message, type, title) {
    var icon = 'info';
    if (type === 'success') icon = 'check_circle';
    if (type === 'error') icon = 'error';
    if (type === 'warning') icon = 'warning';
    
    var toastTitle = title || 'Info';
    if (type === 'success') toastTitle = title || 'Success';
    if (type === 'error') toastTitle = title || 'Error';
    
    var container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
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
    
    container.appendChild(toast);
    
    setTimeout(function() {
        toast.remove();
    }, 5000);
}

function updateTimeDisplays() {
    var timeInput = document.getElementById('current_time');
    
    if (timeInput) {
        setInterval(function() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;
            if (seconds < 10) seconds = '0' + seconds;
            
            timeInput.value = hours + ':' + minutes + ':' + seconds;
        }, 1000);
    }
}

function updateLiveDurations() {
    var durationElements = document.querySelectorAll('[data-live-duration="true"]');
    
    for (var i = 0; i < durationElements.length; i++) {
        var element = durationElements[i];
        var checkInTime = parseInt(element.getAttribute('data-check-in'));
        var currentTime = Math.floor(Date.now() / 1000);
        var totalSeconds = currentTime - checkInTime;
        var totalMinutes = Math.floor(totalSeconds / 60);
        var hours = Math.floor(totalMinutes / 60);
        var minutes = totalMinutes % 60;
        element.textContent = hours + 'h ' + minutes + 'm';
    }
}

setInterval(updateLiveDurations, 30000);
updateLiveDurations();

function setupDeleteConfirmations() {
    var deleteForms = document.querySelectorAll('form[data-confirm]');
    
    for (var i = 0; i < deleteForms.length; i++) {
        deleteForms[i].addEventListener('submit', function(e) {
            var memberName = this.getAttribute('data-member-name') || 'this member';
            var confirmed = confirm('Are you sure you want to delete ' + memberName + '? This cannot be undone.');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    }
}

function toggleMemberForm() {
    var formContainer = document.getElementById('memberFormContainer');
    var button = document.getElementById('toggleFormBtn');
    
    var isHidden = formContainer.style.display === 'none' || formContainer.style.display === '';
    
    if (isHidden) {
        formContainer.style.display = 'block';
        button.innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
    } else {
        formContainer.style.display = 'none';
        button.innerHTML = '<span class="material-symbols-rounded">person_add</span><span>Add New Member</span>';
    }
}

function isValidEmail(email) {
    var hasAtSymbol = email.includes('@');
    var hasDot = email.includes('.');
    return hasAtSymbol && hasDot;
}

function isValidPhone(phone) {
    var numbersOnly = phone.replace(/\D/g, '');
    var isExactly10Digits = numbersOnly.length === 10;
    return isExactly10Digits;
}

function setupFormValidation() {
    var forms = document.querySelectorAll('form');
    
    for (var i = 0; i < forms.length; i++) {
        var form = forms[i];
        
        var emailInputs = form.querySelectorAll('input[type="email"]');
        for (var j = 0; j < emailInputs.length; j++) {
            emailInputs[j].addEventListener('blur', function() {
                var emailValue = this.value;
                var hasValue = emailValue.length > 0;
                var isValid = isValidEmail(emailValue);
                
                if (hasValue && !isValid) {
                    alert('Please enter a valid email address');
                    this.focus();
                }
            });
        }
        
        var phoneInputs = form.querySelectorAll('input[name="phone"]');
        for (var k = 0; k < phoneInputs.length; k++) {
            phoneInputs[k].addEventListener('input', function() {
                var cleanedValue = this.value.replace(/[^0-9]/g, '');
                this.value = cleanedValue;
            });
            
            phoneInputs[k].addEventListener('blur', function() {
                var phoneValue = this.value;
                var hasValue = phoneValue.length > 0;
                var isValid = isValidPhone(phoneValue);
                
                if (hasValue && !isValid) {
                    alert('Phone number must be exactly 10 digits');
                    this.focus();
                }
            });
        }
    }
}

