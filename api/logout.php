<?php
/**
 * FitHub Gym Management System
 * Logout API Endpoint
 * 
 * Handles user logout requests
 */

// Define app root
define('APP_ROOT', dirname(__DIR__));

// Load required files
require_once APP_ROOT . '/includes/auth.php';

// Set JSON header for AJAX requests
if (isAjaxRequest()) {
    header('Content-Type: application/json');
}

try {
    // Logout user
    $result = logoutUser();
    
    if (isAjaxRequest()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Logged out successfully',
            'redirect_url' => BASE_URL . 'index.php'
        ]);
    } else {
        // Redirect to login page
        setFlashMessage('You have been logged out successfully.', 'success');
        redirect(BASE_URL . 'index.php');
    }
    
} catch (Exception $e) {
    error_log("Logout API Error: " . $e->getMessage());
    
    if (isAjaxRequest()) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred during logout'
        ]);
    } else {
        redirect(BASE_URL . 'index.php');
    }
}
?>
