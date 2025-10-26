<?php
/**
 * FitHub Gym Management System
 * Session Management
 * 
 * Handles secure session initialization, management, and validation
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Load configuration if not already loaded
if (!defined('SESSION_LIFETIME')) {
    require_once APP_ROOT . '/config/config.php';
}

/**
 * Initialize secure session
 */
function initSession() {
    // Session already started
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    
    // Configure session settings
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', SESSION_COOKIE_SAMESITE);
    
    // Use secure cookies in production
    if (SESSION_COOKIE_SECURE) {
        ini_set('session.cookie_secure', 1);
    }
    
    // Set session name
    session_name(SESSION_NAME);
    
    // Set session lifetime
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_set_cookie_params(SESSION_LIFETIME);
    
    // Start session
    session_start();
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    } else if (time() - $_SESSION['created_at'] > 1800) {
        // Regenerate session ID every 30 minutes
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
    
    // Check session timeout
    checkSessionTimeout();
    
    // Validate session
    validateSession();
}

/**
 * Check if session has timed out
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = time() - $_SESSION['last_activity'];
        
        if ($inactive > SESSION_LIFETIME) {
            // Session has expired
            destroySession();
            
            // Redirect to login with timeout message
            if (!isAjaxRequest()) {
                $_SESSION['error_message'] = 'Your session has expired due to inactivity. Please login again.';
                redirect(BASE_URL . 'index.php');
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Session expired']);
                exit();
            }
        }
    }
    
    // Update last activity timestamp
    $_SESSION['last_activity'] = time();
}

/**
 * Validate session data
 */
function validateSession() {
    // Skip validation if not logged in
    if (!isLoggedIn()) {
        return;
    }
    
    // Validate user agent
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } else if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        // User agent mismatch - possible session hijacking
        destroySession();
        
        if (!isAjaxRequest()) {
            $_SESSION['error_message'] = 'Session validation failed. Please login again.';
            redirect(BASE_URL . 'index.php');
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Session invalid']);
            exit();
        }
    }
    
    // Validate IP address (optional - may cause issues with dynamic IPs)
    if (defined('VALIDATE_SESSION_IP') && VALIDATE_SESSION_IP === true) {
        if (!isset($_SESSION['ip_address'])) {
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        } else if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            destroySession();
            
            if (!isAjaxRequest()) {
                $_SESSION['error_message'] = 'Session validation failed. Please login again.';
                redirect(BASE_URL . 'index.php');
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Session invalid']);
                exit();
            }
        }
    }
}

/**
 * Destroy session and clear all data
 */
function destroySession() {
    // Initialize session if not started
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Set login session data
 * @param int $userId User ID
 * @param string $email User email
 * @param string $role User role
 * @param string $name User full name
 */
function setLoginSession($userId, $email, $role, $name = '') {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Set session data
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_name'] = $name;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    
    // Generate CSRF token
    generateCSRFToken();
    
    // Log successful login
    if (defined('LOG_AUTH_ATTEMPTS') && LOG_AUTH_ATTEMPTS) {
        logAuthAttempt($email, 'success', 'Login successful');
    }
}

/**
 * Get current user ID
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user email
 * @return string|null User email or null if not logged in
 */
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Get current user name
 * @return string|null User name or null if not logged in
 */
function getCurrentUserName() {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Check if request is AJAX
 * @return bool True if AJAX request, false otherwise
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Require login - redirect to login page if not authenticated
 * @param string $requiredRole Optional role requirement
 */
function requireLogin($requiredRole = null) {
    if (!isLoggedIn()) {
        if (isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit();
        } else {
            $_SESSION['error_message'] = 'Please login to access this page.';
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            redirect(BASE_URL . 'index.php');
        }
    }
    
    // Check role requirement
    if ($requiredRole !== null && !hasRole($requiredRole)) {
        if (isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        } else {
            $_SESSION['error_message'] = 'You do not have permission to access this page.';
            redirectToDashboard();
        }
    }
}

/**
 * Redirect to appropriate dashboard based on user role
 */
function redirectToDashboard() {
    $role = getUserRole();
    
    switch ($role) {
        case ROLE_ADMIN:
            redirect(BASE_URL . 'admin/dashboard.php');
            break;
        case ROLE_TRAINER:
            redirect(BASE_URL . 'trainer/dashboard.php');
            break;
        case ROLE_MEMBER:
            redirect(BASE_URL . 'member/dashboard.php');
            break;
        default:
            redirect(BASE_URL . 'index.php');
    }
}

/**
 * Log authentication attempt
 * @param string $email Email/username
 * @param string $status Status (success/failed)
 * @param string $message Additional message
 */
function logAuthAttempt($email, $status, $message = '') {
    if (!defined('ENABLE_LOGGING') || !ENABLE_LOGGING) {
        return;
    }
    
    $logFile = LOGS_PATH . '/auth.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $logEntry = "[{$timestamp}] {$status} - Email: {$email} - IP: {$ip} - {$message}" . PHP_EOL;
    
    // Append to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Set flash message
 * @param string $message Message text
 * @param string $type Message type (success, error, warning, info)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear flash message
 * @return array|null Flash message array or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Check if user has permission
 * @param string $permission Permission to check
 * @return bool True if user has permission, false otherwise
 */
function hasPermission($permission) {
    $role = getUserRole();
    if (!$role) return false;
    
    $permissions = ROLE_PERMISSIONS[$role] ?? [];
    return in_array($permission, $permissions);
}

// Initialize session automatically when this file is included
initSession();
?>
