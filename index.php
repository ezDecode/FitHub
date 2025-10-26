<?php
require_once 'config.php';

// Get statistics
$total_members_query = "SELECT COUNT(*) as total FROM members";
$result = $conn->query($total_members_query);
$total_members = $result->fetch_assoc()['total'];

$active_members_query = "SELECT COUNT(*) as total FROM members WHERE status = 'active'";
$result = $conn->query($active_members_query);
$active_members = $result->fetch_assoc()['total'];

$today = date('Y-m-d');
$today_attendance_query = "SELECT COUNT(*) as total FROM attendance WHERE date = '$today'";
$result = $conn->query($today_attendance_query);
$today_attendance = $result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Modern Gym Management System - Manage members, track attendance, and view reports">
    <meta name="theme-color" content="#667eea">
    <title>Gym Management System - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">🏋️ Gym Management System</a>
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
            <ul class="navbar-menu" id="navMenu">
                <li><a href="index.php" class="active">🏠 Home</a></li>
                <li><a href="members.php">👥 Members</a></li>
                <li><a href="attendance.php">📅 Attendance</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🏆 Welcome to Gym Management System</h1>
            <p>Simple, Easy, and Efficient Member & Attendance Management</p>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="container mt-4">
        <div class="row">
            <div class="col-12 col-4">
                <div class="stats-card">
                    <div class="icon">👥</div>
                    <h2><?php echo $total_members; ?></h2>
                    <p>Total Members</p>
                </div>
            </div>
            <div class="col-12 col-4">
                <div class="stats-card">
                    <div class="icon">✅</div>
                    <h2><?php echo $active_members; ?></h2>
                    <p>Active Members</p>
                </div>
            </div>
            <div class="col-12 col-4">
                <div class="stats-card">
                    <div class="icon">📋</div>
                    <h2><?php echo $today_attendance; ?></h2>
                    <p>Today's Check-ins</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mt-4">
        <div class="row">
            <div class="col-6 col-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon" style="font-size: 3rem; margin-bottom: 15px;">➕</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem;">Add Members</h3>
                        <p style="color: #6b7280; margin-bottom: 1rem;">Register new gym members quickly</p>
                        <a href="members.php" class="btn btn-primary btn-sm btn-block">Go to Members</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon" style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem;">View Members</h3>
                        <p style="color: #6b7280; margin-bottom: 1rem;">See all registered members</p>
                        <a href="members.php" class="btn btn-success btn-sm btn-block">View List</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon" style="font-size: 3rem; margin-bottom: 15px;">⏰</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem;">Track Attendance</h3>
                        <p style="color: #6b7280; margin-bottom: 1rem;">Mark check-in and check-out</p>
                        <a href="attendance.php" class="btn btn-info btn-sm btn-block">Track Now</a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="icon" style="font-size: 3rem; margin-bottom: 15px;">📈</div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 1rem 0 0.5rem;">View Reports</h3>
                        <p style="color: #6b7280; margin-bottom: 1rem;">Basic attendance statistics</p>
                        <a href="reports.php" class="btn btn-warning btn-sm btn-block">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Simple Gym Management System | Built with Pure HTML, CSS, PHP & MySQL</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
