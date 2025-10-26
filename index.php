<?php
/**
 * FitHub Gym Management System
 * Login Page
 */

// Define app root
define('APP_ROOT', __DIR__);

// Load required files
require_once APP_ROOT . '/includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard();
}

// Get flash message if any
$flashMessage = getFlashMessage();

// Get remembered email if exists
$rememberedEmail = $_COOKIE['remember_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo APP_NAME; ?> - Login</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo IMAGES_URL; ?>favicon.ico">
    
    <!-- Google Fonts -->
    <link href="<?php echo GOOGLE_FONTS_URL; ?>" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo FONTAWESOME_CDN; ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>style.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>login.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter Tight', sans-serif;
            background: linear-gradient(135deg, <?php echo COLOR_PRIMARY; ?> 0%, <?php echo COLOR_SECONDARY; ?> 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }
        
        .login-left {
            background: linear-gradient(135deg, <?php echo COLOR_PRIMARY; ?> 0%, <?php echo COLOR_SECONDARY; ?> 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .login-logo {
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .login-left h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .login-features {
            list-style: none;
            text-align: left;
            width: 100%;
            max-width: 300px;
        }
        
        .login-features li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .login-features i {
            width: 20px;
        }
        
        .login-right {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-right h2 {
            font-size: 28px;
            font-weight: 700;
            color: <?php echo COLOR_DARK; ?>;
            margin-bottom: 10px;
        }
        
        .login-right .subtitle {
            color: <?php echo COLOR_MEDIUM_GRAY; ?>;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: <?php echo COLOR_DARK; ?>;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: <?php echo COLOR_MEDIUM_GRAY; ?>;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid <?php echo COLOR_LIGHT_GRAY; ?>;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: <?php echo COLOR_PRIMARY; ?>;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .remember-me input[type="checkbox"] {
            cursor: pointer;
        }
        
        .forgot-password {
            color: <?php echo COLOR_PRIMARY; ?>;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, <?php echo COLOR_PRIMARY; ?> 0%, <?php echo COLOR_ACCENT; ?> 100%);
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: <?php echo COLOR_MEDIUM_GRAY; ?>;
        }
        
        .demo-credentials {
            margin-top: 25px;
            padding: 20px;
            background: <?php echo COLOR_LIGHT_GRAY; ?>;
            border-radius: 8px;
            font-size: 13px;
        }
        
        .demo-credentials h4 {
            margin-bottom: 10px;
            color: <?php echo COLOR_DARK; ?>;
        }
        
        .demo-credentials p {
            margin: 5px 0;
            color: <?php echo COLOR_MEDIUM_GRAY; ?>;
        }
        
        .demo-credentials strong {
            color: <?php echo COLOR_DARK; ?>;
        }
        
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            
            .login-left {
                display: none;
            }
            
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="login-logo">
                <i class="fas fa-dumbbell"></i>
            </div>
            <h1><?php echo GYM_NAME; ?></h1>
            <p>Transform your fitness journey with our comprehensive gym management system</p>
            
            <ul class="login-features">
                <li><i class="fas fa-check-circle"></i> Track Your Progress</li>
                <li><i class="fas fa-check-circle"></i> Manage Memberships</li>
                <li><i class="fas fa-check-circle"></i> Professional Trainers</li>
                <li><i class="fas fa-check-circle"></i> Custom Workout Plans</li>
            </ul>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-right">
            <h2>Welcome Back!</h2>
            <p class="subtitle">Please login to your account</p>
            
            <!-- Flash Messages -->
            <?php if ($flashMessage): ?>
            <div class="alert alert-<?php echo $flashMessage['type']; ?>" id="flashMessage">
                <i class="fas fa-<?php echo $flashMessage['type'] === 'success' ? 'check-circle' : ($flashMessage['type'] === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
                <span><?php echo sanitizeOutput($flashMessage['message']); ?></span>
            </div>
            <?php endif; ?>
            
            <!-- Alert for messages -->
            <div id="alertMessage" style="display: none;"></div>
            
            <!-- Login Form -->
            <form id="loginForm" method="POST" action="api/login.php">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="Enter your email"
                            value="<?php echo sanitizeOutput($rememberedEmail); ?>"
                            required
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Enter your password"
                            required
                        >
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember" <?php echo !empty($rememberedEmail) ? 'checked' : ''; ?>>
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <h4><i class="fas fa-info-circle"></i> Demo Credentials</h4>
                <p><strong>Admin:</strong> admin@fithub.com | Admin@123</p>
                <p><strong>Trainer:</strong> john.trainer@fithub.com | Trainer@123</p>
                <p><strong>Member:</strong> alice.member@email.com | Member@123</p>
            </div>
        </div>
    </div>
    
    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loginBtn = document.getElementById('loginBtn');
            const alertMessage = document.getElementById('alertMessage');
            
            // Disable button and show loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            
            // Get form data
            const formData = new FormData(this);
            formData.append('remember', document.getElementById('remember').checked);
            
            // Send AJAX request
            fetch('api/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alertMessage.className = 'alert alert-success';
                    alertMessage.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message + ' Redirecting...';
                    alertMessage.style.display = 'flex';
                    
                    // Redirect after 1 second
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1000);
                } else {
                    // Show error message
                    alertMessage.className = 'alert alert-error';
                    alertMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                    alertMessage.style.display = 'flex';
                    
                    // Re-enable button
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertMessage.className = 'alert alert-error';
                alertMessage.innerHTML = '<i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.';
                alertMessage.style.display = 'flex';
                
                // Re-enable button
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            });
        });
        
        // Auto-hide flash message after 5 seconds
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
