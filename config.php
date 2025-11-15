<?php
// ========================================
// DATABASE CONNECTION FILE
// ========================================
// This file connects to the MySQL database
// Include this file at the top of every page

// Set timezone to India (IST)
date_default_timezone_set('Asia/Kolkata');

// Database connection settings
$db_host = 'localhost';        // Where is the database? (Usually localhost)
$db_name = 'gym_system';       // What is the database name?
$db_user = 'root';             // Database username
$db_pass = '';                 // Database password (empty for XAMPP)

// Connect to the database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check if connection worked
if ($conn->connect_error) {
    // If connection failed, stop and show error
    die("Cannot connect to database: " . $conn->connect_error);
}

// Set character encoding (supports all languages and special characters)
$conn->set_charset("utf8mb4");
?>
