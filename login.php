<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($username) && !empty($password)) {
        // Query the users table
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role, status FROM users WHERE username = ? AND status = 'active'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password using password_verify for bcrypt hashes
            if (password_verify($password, $user['password'])) {
                // Update last_login
                $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        
        $stmt->close();
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitHub Gym Management</title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.1">
    <link rel="stylesheet" href="assets/css/style.css?v=3.1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <span class="material-symbols-rounded brand-icon">fitness_center</span>
                    <span class="brand-text">FitHub</span>
                </div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to access your gym management system</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="material-symbols-rounded">error</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <span class="material-symbols-rounded">person</span> Username
                    </label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Enter your username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <span class="material-symbols-rounded">lock</span> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="material-symbols-rounded">login</span>
                    <span>Sign In</span>
                </button>
            </form>
            
            <div class="login-footer" style="margin-top: 2rem;">
                <div class="demo-credentials">
                    <p style="margin: 0 0 0.5rem 0; color: var(--gray-400);">Demo Credentials:</p>
                    <p style="margin: 0.25rem 0;">Admin: <code>admin</code> / <code>password</code></p>
                    <p style="margin: 0.25rem 0;">Staff: <code>staff1</code> / <code>password</code></p>
                    <p style="margin: 0.25rem 0;">Member: <code>member1</code> / <code>password</code></p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--black);
            padding: var(--space-4);
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-xl);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--space-8);
        }
        
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-3);
            font-size: var(--text-2xl);
            font-weight: var(--font-semibold);
            color: var(--white);
            margin-bottom: var(--space-6);
        }
        
        .login-title {
            font-size: var(--text-3xl);
            font-weight: var(--font-semibold);
            color: var(--white);
            margin-bottom: var(--space-2);
        }
        
        .login-subtitle {
            color: var(--gray-400);
            font-size: var(--text-base);
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: var(--space-6);
        }
        
        .login-footer {
            margin-top: var(--space-8);
            text-align: center;
        }
        
        .demo-credentials {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: var(--space-4);
            font-size: var(--text-sm);
            color: var(--gray-300);
        }
        
        .demo-credentials code {
            background: rgba(255, 255, 255, 0.1);
            padding: var(--space-1) var(--space-2);
            border-radius: var(--radius-sm);
            font-family: 'Courier New', monospace;
            color: var(--white);
        }
    </style>
</body>
</html>
