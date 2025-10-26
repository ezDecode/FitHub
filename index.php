<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System - Home</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="gradient-bg">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-gear-wide-connected"></i> Gym Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="members.php">
                            <i class="bi bi-people"></i> Members
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php">
                            <i class="bi bi-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="bi bi-bar-chart"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">
                <i class="bi bi-trophy"></i> Welcome to Gym Management System
            </h1>
            <p class="lead mb-4">Simple, Easy, and Efficient Member & Attendance Management</p>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="container mb-5">
        <div class="row">
            <?php
            // Get total members count
            $total_members_query = "SELECT COUNT(*) as total FROM members";
            $result = $conn->query($total_members_query);
            $total_members = $result->fetch_assoc()['total'];

            // Get active members count
            $active_members_query = "SELECT COUNT(*) as total FROM members WHERE status = 'active'";
            $result = $conn->query($active_members_query);
            $active_members = $result->fetch_assoc()['total'];

            // Get today's attendance count
            $today = date('Y-m-d');
            $today_attendance_query = "SELECT COUNT(*) as total FROM attendance WHERE date = '$today'";
            $result = $conn->query($today_attendance_query);
            $today_attendance = $result->fetch_assoc()['total'];
            ?>

            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <h2 class="mt-3 mb-1"><?php echo $total_members; ?></h2>
                    <p class="text-muted mb-0">Total Members</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-person-check-fill text-success" style="font-size: 3rem;"></i>
                    <h2 class="mt-3 mb-1"><?php echo $active_members; ?></h2>
                    <p class="text-muted mb-0">Active Members</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <i class="bi bi-calendar-check-fill text-info" style="font-size: 3rem;"></i>
                    <h2 class="mt-3 mb-1"><?php echo $today_attendance; ?></h2>
                    <p class="text-muted mb-0">Today's Check-ins</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container pb-5">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card feature-card border-0 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Add Members</h5>
                        <p class="card-text text-muted">Register new gym members quickly</p>
                        <a href="members.php" class="btn btn-primary btn-sm">Go to Members</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card feature-card border-0 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-list-check text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">View Members</h5>
                        <p class="card-text text-muted">See all registered members</p>
                        <a href="members.php" class="btn btn-success btn-sm">View List</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card feature-card border-0 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-info" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Track Attendance</h5>
                        <p class="card-text text-muted">Mark check-in and check-out</p>
                        <a href="attendance.php" class="btn btn-info btn-sm">Track Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card feature-card border-0 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up text-warning" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">View Reports</h5>
                        <p class="card-text text-muted">Basic attendance statistics</p>
                        <a href="reports.php" class="btn btn-warning btn-sm">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2025 Simple Gym Management System | Built with PHP & Bootstrap 5</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>
