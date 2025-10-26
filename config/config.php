<?php
/**
 * FitHub Gym Management System
 * Main Configuration File
 * 
 * This file contains all application constants and settings
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ==============================================
// ENVIRONMENT SETTINGS
// ==============================================
$environment = getenv('APP_ENV') ?: 'development';
define('ENVIRONMENT', $environment);

// Error reporting based on environment
if (ENVIRONMENT === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APP_ROOT . '/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// ==============================================
// APPLICATION SETTINGS
// ==============================================
define('APP_NAME', 'FitHub Gym Management System');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'A comprehensive web-based gym management system');

// ==============================================
// URL SETTINGS
// ==============================================
// Automatically detect protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

define('BASE_URL', $protocol . $host . '/');
define('APP_URL', BASE_URL);

// ==============================================
// DIRECTORY PATHS
// ==============================================
define('CONFIG_PATH', APP_ROOT . '/config');
define('INCLUDES_PATH', APP_ROOT . '/includes');
define('API_PATH', APP_ROOT . '/api');
define('UPLOADS_PATH', APP_ROOT . '/uploads');
define('LOGS_PATH', APP_ROOT . '/logs');
define('ASSETS_PATH', APP_ROOT . '/assets');

// Public URL paths
define('CSS_URL', BASE_URL . 'assets/css/');
define('JS_URL', BASE_URL . 'assets/js/');
define('IMAGES_URL', BASE_URL . 'assets/images/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// ==============================================
// UPLOAD SETTINGS
// ==============================================
define('MAX_FILE_SIZE', 2097152); // 2MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Upload directories
define('MEMBER_PHOTOS_PATH', UPLOADS_PATH . '/members');
define('TRAINER_PHOTOS_PATH', UPLOADS_PATH . '/trainers');

// ==============================================
// SESSION SETTINGS
// ==============================================
define('SESSION_LIFETIME', 1800); // 30 minutes in seconds
define('SESSION_NAME', 'fithub_session');
define('SESSION_COOKIE_SECURE', ENVIRONMENT === 'production'); // HTTPS only in production
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SAMESITE', 'Strict');

// ==============================================
// SECURITY SETTINGS
// ==============================================
define('PASSWORD_MIN_LENGTH', 8);
define('BCRYPT_COST', 10);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour

// ==============================================
// DATE & TIME SETTINGS
// ==============================================
date_default_timezone_set('Asia/Kolkata'); // Change according to your timezone
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd M Y');
define('DISPLAY_DATETIME_FORMAT', 'd M Y h:i A');

// ==============================================
// PAGINATION SETTINGS
// ==============================================
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGINATION_LINKS', 5);

// ==============================================
// BUSINESS SETTINGS
// ==============================================
define('GYM_NAME', 'FitHub Fitness Center');
define('GYM_EMAIL', 'info@fithub.com');
define('GYM_PHONE', '+91-9876543210');
define('GYM_ADDRESS', '123 Fitness Street, Health City, HC 12345');

// Currency settings
define('CURRENCY_SYMBOL', '₹');
define('CURRENCY_CODE', 'INR');
define('CURRENCY_POSITION', 'left'); // 'left' or 'right'

// ==============================================
// MEMBERSHIP SETTINGS
// ==============================================
define('MEMBERSHIP_EXPIRY_ALERT_DAYS', 7); // Alert when membership expires in X days
define('AUTO_SUSPEND_EXPIRED_MEMBERS', true);

// ==============================================
// ATTENDANCE SETTINGS
// ==============================================
define('ALLOW_MULTIPLE_CHECKINS', false); // Allow only one check-in per day
define('AUTO_CHECKOUT_HOURS', 4); // Auto checkout after X hours if not checked out

// ==============================================
// PAYMENT SETTINGS
// ==============================================
define('PAYMENT_METHODS', ['cash', 'card', 'upi', 'online']);
define('RECEIPT_NUMBER_PREFIX', 'REC-');
define('RECEIPT_NUMBER_LENGTH', 3);

// ==============================================
// NOTIFICATION SETTINGS
// ==============================================
define('ENABLE_EMAIL_NOTIFICATIONS', false); // Enable in production with proper SMTP setup
define('ENABLE_SMS_NOTIFICATIONS', false); // Enable if SMS gateway is configured

// ==============================================
// CHART & ANALYTICS SETTINGS
// ==============================================
define('DASHBOARD_CHART_MONTHS', 6); // Show last 6 months in revenue chart
define('RECENT_PAYMENTS_COUNT', 10); // Show last 10 payments on dashboard

// ==============================================
// VALIDATION RULES
// ==============================================
define('PHONE_MIN_LENGTH', 10);
define('PHONE_MAX_LENGTH', 15);
define('NAME_MIN_LENGTH', 2);
define('NAME_MAX_LENGTH', 255);

// ==============================================
// USER ROLES
// ==============================================
define('ROLE_ADMIN', 'admin');
define('ROLE_TRAINER', 'trainer');
define('ROLE_MEMBER', 'member');

// Role permissions mapping
define('ROLE_PERMISSIONS', [
    ROLE_ADMIN => [
        'full_system_access',
        'manage_members',
        'manage_trainers',
        'manage_plans',
        'manage_payments',
        'view_analytics',
        'system_configuration'
    ],
    ROLE_TRAINER => [
        'view_assigned_members',
        'mark_attendance',
        'update_workout_plans',
        'view_schedule'
    ],
    ROLE_MEMBER => [
        'view_membership_status',
        'check_attendance_history',
        'make_payments',
        'view_workout_plans',
        'update_profile'
    ]
]);

// ==============================================
// DESIGN SYSTEM COLORS
// ==============================================
define('COLOR_PRIMARY', '#FF6B35');
define('COLOR_SECONDARY', '#004E89');
define('COLOR_ACCENT', '#F7931E');
define('COLOR_SUCCESS', '#2ECC71');
define('COLOR_WARNING', '#F39C12');
define('COLOR_DANGER', '#E74C3C');
define('COLOR_DARK', '#1A1A2E');
define('COLOR_LIGHT_GRAY', '#F5F5F5');
define('COLOR_MEDIUM_GRAY', '#95A5A6');

// ==============================================
// EXTERNAL LIBRARIES (CDN URLs)
// ==============================================
define('CHARTJS_CDN', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js');
define('SWEETALERT2_JS_CDN', 'https://cdn.jsdelivr.net/npm/sweetalert2@11');
define('SWEETALERT2_CSS_CDN', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
define('FLATPICKR_JS_CDN', 'https://cdn.jsdelivr.net/npm/flatpickr');
define('FLATPICKR_CSS_CDN', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
define('FONTAWESOME_CDN', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
define('JSPDF_CDN', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
define('AXIOS_CDN', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js');
define('GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Inter+Tight:wght@300;400;500;600;700&display=swap');

// ==============================================
// API SETTINGS
// ==============================================
define('API_RESPONSE_JSON', true);
define('API_RATE_LIMIT', 100); // Requests per minute
define('API_ENABLE_CORS', false); // Enable if API is accessed from different domain

// ==============================================
// LOGGING SETTINGS
// ==============================================
define('ENABLE_LOGGING', true);
define('LOG_QUERIES', ENVIRONMENT === 'development');
define('LOG_ERRORS', true);
define('LOG_AUTH_ATTEMPTS', true);

// ==============================================
// MAINTENANCE MODE
// ==============================================
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'System is under maintenance. Please check back soon.');

// ==============================================
// HELPER FUNCTIONS
// ==============================================

/**
 * Format currency amount
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function formatCurrency($amount) {
    $formatted = number_format($amount, 2);
    if (CURRENCY_POSITION === 'left') {
        return CURRENCY_SYMBOL . $formatted;
    } else {
        return $formatted . ' ' . CURRENCY_SYMBOL;
    }
}

/**
 * Format date for display
 * @param string $date Date string
 * @param bool $includeTime Include time in format
 * @return string Formatted date
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) return 'N/A';
    
    $timestamp = strtotime($date);
    if ($timestamp === false) return 'Invalid Date';
    
    $format = $includeTime ? DISPLAY_DATETIME_FORMAT : DISPLAY_DATE_FORMAT;
    return date($format, $timestamp);
}

/**
 * Sanitize output for XSS prevention
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Check if user is logged in
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user role
 * @return string|null User role or null if not logged in
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user has specific role
 * @param string $role Role to check
 * @return bool True if user has role, false otherwise
 */
function hasRole($role) {
    return getUserRole() === $role;
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// ==============================================
// AUTO-CREATE REQUIRED DIRECTORIES
// ==============================================
$requiredDirs = [
    UPLOADS_PATH,
    MEMBER_PHOTOS_PATH,
    TRAINER_PHOTOS_PATH,
    LOGS_PATH
];

foreach ($requiredDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ==============================================
// CONFIGURATION COMPLETE
// ==============================================
?>
