<?php
date_default_timezone_set('Asia/Kolkata');

$db_host = 'localhost';
$db_name = 'gym_system';
$db_user = 'root';
$db_pass = '';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Cannot connect to database: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
