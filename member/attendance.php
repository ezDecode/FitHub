<?php
session_start();
require_once '../config.php';
require_once '../includes/role_check.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../login.php');
    exit;
}

// Members only
requireRole('member');

$user_id = getCurrentUserId();

// Get member profile
$member_query = $conn->prepare("SELECT * FROM members WHERE user_id = ?");
$member_query->bind_param("i", $user_id);
$member_query->execute();
$member_result = $member_query->get_result();
$member = $member_result->fetch_assoc();
$member_query->close();

if (!$member) {
    header('Location: profile.php');
    exit;
}

$member_id = $member['id'];

// Get attendance records for this member
$attendance_query = $conn->prepare("
    SELECT * FROM attendance 
    WHERE member_id = ? 
    ORDER BY date DESC, check_in_time DESC 
    LIMIT 50
");
$attendance_query->bind_param("i", $member_id);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result();

$attendance_records = [];
while ($record = $attendance_result->fetch_assoc()) {
    $attendance_records[] = $record;
}
$attendance_query->close();

// Calculate statistics
$total_visits = count($attendance_records);
$this_month = date('Y-m');
$monthly_visits = count(array_filter($attendance_records, function($r) use ($this_month) {
    return strpos($r['date'], $this_month) === 0;
}));

// Calculate average session duration
$total_duration = 0;
$completed_sessions = 0;
foreach ($attendance_records as $record) {
    if ($record['check_out_time']) {
        $duration = strtotime($record['check_out_time']) - strtotime($record['check_in_time']);
        $total_duration += $duration;
        $completed_sessions++;
    }
}
$avg_duration_minutes = $completed_sessions > 0 ? round($total_duration / $completed_sessions / 60) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - FitHub</title>
    <link rel="stylesheet" href="../assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="../assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
    <?php include '../includes/navigation.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="attendance-overview-bar">
                <div class="overview-content">
                    <h2 class="page-section-title">My Attendance History</h2>
                    <p class="page-section-subtitle">Track your gym visits and workout patterns</p>
                </div>
            </div>

            <div class="attendance-summary-cards">
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">fitness_center</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $total_visits; ?></div>
                        <div class="summary-label">Total Visits</div>
                        <div class="summary-date">All time</div>
                    </div>
                </div>
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">calendar_month</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $monthly_visits; ?></div>
                        <div class="summary-label">This Month</div>
                        <div class="summary-subtext"><?php echo date('F Y'); ?></div>
                    </div>
                </div>
                <div class="attendance-summary-card">
                    <div class="summary-card-icon-wrapper">
                        <span class="material-symbols-rounded summary-icon">schedule</span>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number"><?php echo $avg_duration_minutes; ?></div>
                        <div class="summary-label">Avg Duration</div>
                        <div class="summary-subtext">Minutes per session</div>
                    </div>
                </div>
            </div>

            <div class="attendance-list-container">
                <div class="attendance-list-header">
                    <div class="list-header-left">
                        <span class="material-symbols-rounded list-header-icon">history</span>
                        <div>
                            <h2 class="list-header-title">Recent Visits</h2>
                            <p class="list-header-subtitle">Your last 50 gym check-ins</p>
                        </div>
                    </div>
                    <span class="list-header-badge"><?php echo min($total_visits, 50); ?> Records</span>
                </div>

                <?php if (count($attendance_records) > 0): ?>
                <div class="attendance-list">
                    <?php foreach ($attendance_records as $record): ?>
                    <div class="attendance-item <?php echo $record['check_out_time'] === null ? 'active-session' : 'completed-session'; ?>">
                        <div class="attendance-item-status">
                            <?php if ($record['check_out_time'] === null): ?>
                            <span class="status-dot"></span>
                            <?php else: ?>
                            <span class="material-symbols-rounded status-icon">check_circle</span>
                            <?php endif; ?>
                        </div>
                        <div class="attendance-item-content">
                            <div class="attendance-item-main">
                                <h3 class="attendance-member-name"><?php echo date('l, F j, Y', strtotime($record['date'])); ?></h3>
                                <span class="attendance-membership-badge">
                                    <?php echo $record['check_out_time'] ? 'Completed' : 'In Progress'; ?>
                                </span>
                            </div>
                            <div class="attendance-item-details">
                                <div class="attendance-detail">
                                    <span class="material-symbols-rounded attendance-detail-icon">login</span>
                                    <span class="attendance-detail-text">Check-in: <?php echo date('g:i A', strtotime($record['check_in_time'])); ?></span>
                                </div>
                                <?php if ($record['check_out_time'] !== null): ?>
                                <div class="attendance-detail">
                                    <span class="material-symbols-rounded attendance-detail-icon">logout</span>
                                    <span class="attendance-detail-text">Check-out: <?php echo date('g:i A', strtotime($record['check_out_time'])); ?></span>
                                </div>
                                <div class="attendance-detail">
                                    <span class="material-symbols-rounded attendance-detail-icon">schedule</span>
                                    <span class="attendance-detail-text">
                                        <?php 
                                            $duration = (strtotime($record['check_out_time']) - strtotime($record['check_in_time'])) / 60;
                                            $hours = floor($duration / 60);
                                            $minutes = floor($duration % 60);
                                            echo "Duration: {$hours}h {$minutes}m";
                                        ?>
                                    </span>
                                </div>
                                <?php else: ?>
                                <div class="attendance-detail">
                                    <span class="material-symbols-rounded attendance-detail-icon">pending</span>
                                    <span class="attendance-detail-text">Currently at gym</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span class="material-symbols-rounded empty-state-icon">event_busy</span>
                    <h3 class="empty-state-title">No Attendance Records</h3>
                    <p class="empty-state-description">You haven't checked in to the gym yet. Visit the gym and ask staff to check you in!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/script.js"></script>
    <script>
        document.getElementById('navbarToggle').addEventListener('click', function() {
            const menu = document.getElementById('navbarMenu');
            menu.classList.toggle('active');
        });

        document.querySelectorAll('.navbar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navbarMenu').classList.remove('active');
            });
        });
    </script>
</body>
</html>
