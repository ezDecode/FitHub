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
        $current_date = date('Y-m-d');
        
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, check_in_time, date) VALUES (?, ?, ?)");
        if ($stmt->bind_param("iss", $member_id, $check_in_time, $current_date) && $stmt->execute()) {
            $message = "Member checked in successfully at " . date('g:i A');
            $message_type = "success";
        } else {
            $message = "Error checking in member: " . $stmt->error;
            $message_type = "danger";
        }
    }
    
    if (isset($_POST['check_out'])) {
        $attendance_id = $_POST['attendance_id'];
        $check_out_time = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("UPDATE attendance SET check_out_time = ? WHERE id = ?");
        if ($stmt->bind_param("si", $check_out_time, $attendance_id) && $stmt->execute()) {
            $message = "Member checked out successfully at " . date('g:i A');
            $message_type = "success";
        } else {
            $message = "Error checking out member: " . $stmt->error;
            $message_type = "danger";
        }
    }
}

// Fetch today's attendance
$today_date = date('Y-m-d'); // Get today's date in IST
$current_datetime = date('Y-m-d H:i:s'); // Get current datetime in IST

$stmt = $conn->prepare("
    SELECT a.*, m.name, m.membership_type,
        CASE 
            WHEN a.check_out_time IS NULL THEN 'Still in Gym'
            ELSE DATE_FORMAT(a.check_out_time, '%h:%i %p')
        END as check_out_display,
        CASE 
            WHEN a.check_out_time IS NULL THEN TIMESTAMPDIFF(MINUTE, a.check_in_time, ?)
            ELSE TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time)
        END as duration_minutes
    FROM attendance a 
    JOIN members m ON a.member_id = m.id 
    WHERE a.date = ?
    ORDER BY a.check_in_time DESC
");
$stmt->bind_param("ss", $current_datetime, $today_date);
$stmt->execute();
$result = $stmt->get_result();
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
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Quick Stats Overview -->
            <div class="attendance-overview-bar">
                <div class="overview-content">
                    <h2 class="page-section-title">Attendance Tracking</h2>
                    <p class="page-section-subtitle">Monitor real-time gym activity</p>
                </div>
                <div class="overview-live-indicator">
                    <span class="live-dot"></span>
                    <span class="live-text">Live</span>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php endif; ?>
            
            <!-- Check-in Form -->
            <div class="checkin-form-card">
                <div class="form-header">
                    <div class="form-header-left">
                        <span class="material-symbols-rounded form-header-icon">check_circle</span>
                        <h2 class="form-header-title">Member Check-in</h2>
                    </div>
                    <div class="checkin-time-display">
                        <span class="material-symbols-rounded">schedule</span>
                        <span id="liveTimeDisplay"><?php echo date('g:i A'); ?></span>
                    </div>
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
                            <label for="current_time" class="form-label">Current Time (IST)</label>
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
            <div class="attendance-summary-cards">
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">task_alt</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $total_checkins; ?></div>
                        <div class="summary-label">Total Check-ins</div>
                        <div class="summary-date"><?php echo date('F j, Y'); ?></div>
                    </div>
                </div>
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">fitness_center</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $currently_in_gym; ?></div>
                        <div class="summary-label">Currently in Gym</div>
                        <div class="summary-subtext">Active right now</div>
                    </div>
                </div>
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">trending_up</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $total_checkins - $currently_in_gym; ?></div>
                        <div class="summary-label">Completed Today</div>
                        <div class="summary-subtext">Checkouts</div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Attendance -->
            <div class="attendance-list-container">
                <div class="attendance-list-header">
                    <div class="list-header-left">
                        <span class="material-symbols-rounded list-header-icon">list</span>
                        <div>
                            <h2 class="list-header-title">Today's Attendance</h2>
                            <p class="list-header-subtitle"><?php echo date('F j, Y'); ?></p>
                        </div>
                    </div>
                    <span class="list-header-badge"><?php echo $total_checkins; ?> Records</span>
                </div>
                <?php if (count($today_attendance) > 0): ?>
                <div class="attendance-list">
                    <?php foreach ($today_attendance as $attendance): ?>
                        <div class="attendance-item <?php echo $attendance['check_out_time'] === null ? 'active-session' : 'completed-session'; ?>">
                            <div class="attendance-item-status">
                                <?php if ($attendance['check_out_time'] === null): ?>
                                    <span class="status-dot"></span>
                                <?php else: ?>
                                    <span class="material-symbols-rounded status-icon">check_circle</span>
                                <?php endif; ?>
                            </div>
                            <div class="attendance-item-content">
                                <div class="attendance-item-main">
                                    <h3 class="attendance-member-name"><?php echo htmlspecialchars($attendance['name']); ?></h3>
                                    <span class="attendance-membership-badge"><?php echo $attendance['membership_type']; ?></span>
                                </div>
                                <div class="attendance-item-details">
                                    <div class="attendance-detail">
                                        <span class="material-symbols-rounded attendance-detail-icon">login</span>
                                        <span class="attendance-detail-text"><?php echo date('g:i A', strtotime($attendance['check_in_time'])); ?></span>
                                    </div>
                                    <?php if ($attendance['check_out_time'] !== null): ?>
                                        <div class="attendance-detail">
                                            <span class="material-symbols-rounded attendance-detail-icon">logout</span>
                                            <span class="attendance-detail-text"><?php echo $attendance['check_out_display']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="attendance-detail">
                                        <span class="material-symbols-rounded attendance-detail-icon">schedule</span>
                                        <span class="attendance-detail-text" 
                                            <?php if ($attendance['check_out_time'] === null): ?>
                                                data-check-in="<?php echo strtotime($attendance['check_in_time']); ?>" 
                                                data-live-duration="true"
                                            <?php endif; ?>>
                                            <?php 
                                                $hours = floor($attendance['duration_minutes'] / 60);
                                                $minutes = $attendance['duration_minutes'] % 60;
                                                echo $hours . 'h ' . $minutes . 'm';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="attendance-item-actions">
                                <?php if ($attendance['check_out_time'] === null): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">
                                    <button type="submit" name="check_out" class="btn btn-sm btn-warning">
                                        <span class="material-symbols-rounded">logout</span>
                                        <span>Check Out</span>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span class="completed-badge">
                                    <span class="material-symbols-rounded">check_circle</span>
                                    <span>Completed</span>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

    // Update time displays every second
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const displayString = now.toLocaleTimeString('en-US', {
            hour12: true,
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Update form time
        const formTime = document.getElementById('current_time');
        if (formTime) {
            formTime.value = timeString;
        }
        
        // Update display time
        const displayTime = document.getElementById('liveTimeDisplay');
        if (displayTime) {
            displayTime.textContent = displayString;
        }
    }

    // Update time immediately and then every second
    updateTime();
    setInterval(updateTime, 1000);

    // Update live durations for active sessions
    function updateLiveDurations() {
        const liveDurations = document.querySelectorAll('[data-live-duration="true"]');
        const now = Math.floor(Date.now() / 1000); // Current time in seconds
        
        liveDurations.forEach(element => {
            const checkInTime = parseInt(element.getAttribute('data-check-in'));
            const durationSeconds = now - checkInTime;
            const durationMinutes = Math.floor(durationSeconds / 60);
            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;
            
            element.textContent = hours + 'h ' + minutes + 'm';
        });
    }

    // Update durations every 30 seconds
    if (document.querySelectorAll('[data-live-duration="true"]').length > 0) {
        updateLiveDurations();
        setInterval(updateLiveDurations, 30000); // Update every 30 seconds
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);

    // Form validation
    const checkinForm = document.querySelector('.checkin-form');
    if (checkinForm) {
        checkinForm.addEventListener('submit', function(e) {
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
    }
    </script>
</body>

</html>