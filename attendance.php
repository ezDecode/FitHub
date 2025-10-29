<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

$message = '';
$message_type = '';

// Handle check-in/check-out
if ($_POST) {
    if (isset($_POST['check_in'])) {
        $member_id = $_POST['member_id'];
        $check_in_time = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, check_in_time, date) VALUES (?, ?, CURDATE())");
        if ($stmt->bind_param("is", $member_id, $check_in_time) && $stmt->execute()) {
            $message = "Member checked in successfully!";
            $message_type = "success";
        } else {
            $message = "Error checking in member!";
            $message_type = "danger";
        }
    }
    
    if (isset($_POST['check_out'])) {
        $attendance_id = $_POST['attendance_id'];
        $check_out_time = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("UPDATE attendance SET check_out_time = ? WHERE id = ?");
        if ($stmt->bind_param("si", $check_out_time, $attendance_id) && $stmt->execute()) {
            $message = "Member checked out successfully!";
            $message_type = "success";
        } else {
            $message = "Error checking out member!";
            $message_type = "danger";
        }
    }
}

// Fetch today's attendance
$result = $conn->query("
    SELECT a.*, m.name, m.membership_type,
        CASE 
            WHEN a.check_out_time IS NULL THEN 'Still in Gym'
            ELSE DATE_FORMAT(a.check_out_time, '%h:%i %p')
        END as check_out_display,
        CASE 
            WHEN a.check_out_time IS NULL THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, NOW())
            ELSE TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time)
        END as duration_minutes
    FROM attendance a 
    JOIN members m ON a.member_id = m.id 
    WHERE a.date = CURDATE() 
    ORDER BY a.check_in_time DESC
");
$today_attendance = [];
while ($row = $result->fetch_assoc()) {
    $today_attendance[] = $row;
}

// Fetch all members for dropdown
$result = $conn->query("SELECT * FROM members WHERE status = 'active' ORDER BY name");
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

// Calculate today's summary
$total_checkins = count($today_attendance);
$currently_in_gym = count(array_filter($today_attendance, function($attendance) {
    return $attendance['check_out_time'] === null;
}));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - FitHub Gym Management</title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>

<body>
    <!-- Modern Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="material-symbols-rounded brand-icon">fitness_center</span>
                <span class="brand-text">FitHub</span>
            </a>
            <button class="navbar-toggle" id="navbarToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="members.php">Members</a></li>
                <li><a href="attendance.php" class="active">Attendance</a></li>
                <li><a href="reports.php">Analytics</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Attendance Tracking</h1>
            <p class="page-subtitle">Track member check-ins and check-outs with real-time monitoring.</p>
        </div>
    </section>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Alert Messages -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php endif; ?>
            
            <!-- Check-in Form -->
            <div class="form-container">
                <div class="form-header">
                    <span class="material-symbols-rounded form-header-icon">check_circle</span>
                    <h2 class="form-header-title">Member Check-in</h2>
                </div>
                <form method="POST" class="checkin-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="member_id" class="form-label">Select Member *</label>
                            <select id="member_id" name="member_id" class="form-control" required>
                                <option value="">Choose a member...</option>
                                <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['id']; ?>">
                                    <?php echo htmlspecialchars($member['name']); ?>
                                    (<?php echo $member['membership_type']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="current_time" class="form-label">Current Time</label>
                            <input type="text" id="current_time" class="form-control" readonly
                                value="<?php echo date('H:i:s'); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="today_date" class="form-label">Today's Date</label>
                            <input type="text" id="today_date" class="form-control" readonly
                                value="<?php echo date('F j, Y'); ?>">
                        </div>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="check_in_confirm" name="check_in_confirm" required>
                                <label for="check_in_confirm" class="checkbox-label">
                                    <span class="material-symbols-rounded">check_circle</span> Check In Member
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="check_in" class="btn btn-primary">
                            <span class="material-symbols-rounded">check_circle</span>
                            <span>Check In Member</span>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Today's Summary -->
            <div class="summary-cards">
                <div class="summary-card">
                    <span class="material-symbols-rounded summary-icon">task_alt</span>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $total_checkins; ?></div>
                        <div class="summary-label">Total Check-ins</div>
                    </div>
                </div>
                <div class="summary-card">
                    <span class="material-symbols-rounded summary-icon">fitness_center</span>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $currently_in_gym; ?></div>
                        <div class="summary-label">Currently in Gym</div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Attendance -->
            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">
                        📋 Today's Attendance - <?php echo date('F j, Y'); ?>
                    </div>
                    <div class="table-count"><?php echo $total_checkins; ?> Total</div>
                </div>
                <?php if (count($today_attendance) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Check-in Time</th>
                                <th>Check-out Time</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($today_attendance as $attendance): ?>
                            <tr>
                                <td>
                                    <div class="member-info">
                                        <div class="member-name"><?php echo htmlspecialchars($attendance['name']); ?>
                                        </div>
                                        <div class="member-detail"><?php echo $attendance['membership_type']; ?> Member
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="time-info"><?php echo date('g:i A', strtotime($attendance['check_in_time'])); ?></div>
                                </td>
                                <td>
                                    <div class="time-info"><?php echo $attendance['check_out_display']; ?></div>
                                </td>
                                <td>
                                    <div class="duration-info">
                                        <?php 
                                            $hours = floor($attendance['duration_minutes'] / 60);
                                            $minutes = $attendance['duration_minutes'] % 60;
                                            echo $hours . 'h ' . $minutes . 'm';
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($attendance['check_out_time'] === null): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">
                                        <button type="submit" name="check_out" class="btn btn-sm btn-warning">
                                            <span class="material-symbols-rounded">logout</span>
                                            <span>Check Out</span>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="badge badge-success">
                                        <span class="material-symbols-rounded">check_circle</span> Completed
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="material-symbols-rounded empty-state-icon">event_busy</span>
                    <h3 class="empty-state-title">No Check-ins Today</h3>
                    <p class="empty-state-description">No members have checked in today. Start tracking attendance by checking in
                        your first member.</p>
                    <a href="#checkin" class="btn btn-primary">
                        <span class="material-symbols-rounded">check_circle</span>
                        <span>Check In Member</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
    <script>
    // Mobile navigation toggle
    document.getElementById('navbarToggle').addEventListener('click', function() {
        const menu = document.getElementById('navbarMenu');
        menu.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.navbar-menu a').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('navbarMenu').classList.remove('active');
        });
    });

    // Update time every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current_time').value = timeString;
    }

    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);

    // Form validation
    document.querySelector('.checkin-form').addEventListener('submit', function(e) {
        const memberId = document.getElementById('member_id').value;
        const confirmCheckbox = document.getElementById('check_in_confirm');

        if (!memberId) {
            e.preventDefault();
            alert('Please select a member to check in.');
            return false;
        }

        if (!confirmCheckbox.checked) {
            e.preventDefault();
            alert('Please confirm the check-in by checking the checkbox.');
            return false;
        }
    });
    </script>
</body>

</html>