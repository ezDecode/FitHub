<?php
// ========================================
// AUTHENTICATION CHECK
// ========================================
// This file checks if a user is logged in
// Include at the top of every protected page

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    // User is NOT logged in
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Optional: Check session timeout (30 minutes = 1800 seconds)
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    // If inactive for more than 30 minutes
    if ($inactive_time > 1800) {
        // Destroy session
        session_unset();
        session_destroy();
        
        // Redirect to login with timeout message
        header('Location: login.php?timeout=1');
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
