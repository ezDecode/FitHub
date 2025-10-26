<?php
/**
 * Simple Gym Management System - Configuration File
 * Database Connection Setup
 * Version: 1.0.0
 */

// Start session for any future authentication
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'gym_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Timezone setting
date_default_timezone_set('UTC');

// Display errors in development (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function for safe output
function escape_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Helper function for success messages
function show_success($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . escape_html($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

// Helper function for error messages
function show_error($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . escape_html($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

// Helper function for validation
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function for phone validation (10 digits)
function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}
?>
