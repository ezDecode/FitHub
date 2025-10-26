<?php
/**
 * FitHub Gym Management System
 * Login API Endpoint
 * 
 * Handles user login requests
 */

// Define app root
define('APP_ROOT', dirname(__DIR__));

// Load required files
require_once APP_ROOT . '/includes/auth.php';

// Set JSON header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Get POST data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';
    
    // Validate input
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required'
        ]);
        exit();
    }
    
    // Authenticate user
    $result = authenticateUser($email, $password);
    
    if ($result['success']) {
        // Set remember me cookie if requested
        if ($remember) {
            // Set cookie for 30 days
            $cookieExpiry = time() + (30 * 24 * 60 * 60);
            setcookie('remember_email', $email, $cookieExpiry, '/', '', false, true);
        }
        
        // Determine redirect URL based on role
        $redirectUrl = '';
        switch ($result['user']['role']) {
            case ROLE_ADMIN:
                $redirectUrl = BASE_URL . 'admin/dashboard.php';
                break;
            case ROLE_TRAINER:
                $redirectUrl = BASE_URL . 'trainer/dashboard.php';
                break;
            case ROLE_MEMBER:
                $redirectUrl = BASE_URL . 'member/dashboard.php';
                break;
            default:
                $redirectUrl = BASE_URL;
        }
        
        // Check if there's a redirect URL stored in session
        if (isset($_SESSION['redirect_after_login'])) {
            $redirectUrl = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'redirect_url' => $redirectUrl,
            'user' => [
                'name' => $result['user']['name'],
                'role' => $result['user']['role']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    error_log("Login API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during login. Please try again.'
    ]);
}
?>
