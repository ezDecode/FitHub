<?php
require_once 'config.php';

// Get date filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get statistics
$total_members = $conn->query("SELECT COUNT(*) as total FROM members")->fetch_assoc()['total'];
$active_members = $conn->query("SELECT COUNT(*) as total FROM members WHERE status = 'active'")->fetch_assoc()['total'];
$total_checkins = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

// Member attendance
$member_attendance_query = "
    SELECT 
        m.id, m.name, m.email, m.membership_type,
        COUNT(a.id) as visit_count,
        MAX(a.date) as last_visit
    FROM members m
    LEFT JOIN attendance a ON m.id = a.member_id AND a.date BETWEEN '$start_date' AND '$end_date'
    WHERE m.status = 'active'
    GROUP BY m.id, m.name, m.email, m.membership_type
    ORDER BY visit_count DESC, m.name ASC
";
$member_attendance_result = $conn->query($member_attendance_query);

// Daily summary
$daily_summary_query = "
    SELECT 
        date,
        COUNT(*) as total_checkins,
        COUNT(CASE WHEN check_out IS NOT NULL THEN 1 END) as completed_visits
    FROM attendance
    WHERE date BETWEEN '$start_date' AND '$end_date'
    GROUP BY date
    ORDER BY date DESC
    LIMIT 10
";
$daily_summary_result = $conn->query($daily_summary_query);

// Top performers
$top_performers_query = "
    SELECT m.name, m.membership_type, COUNT(a.id) as visit_count
    FROM members m
    JOIN attendance a ON m.id = a.member_id
    WHERE a.date BETWEEN '$start_date' AND '$end_date'
    GROUP BY m.id, m.name, m.membership_type
    ORDER BY visit_count DESC
    LIMIT 5
";
$top_performers_result = $conn->query($top_performers_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Gym System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">🏋️ Gym Management System</a>
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
            <ul class="navbar-menu" id="navMenu">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="members.php">👥 Members</a></li>
                <li><a href="attendance.php">📅 Attendance</a></li>
                <li><a href="reports.php" class="active">📊 Reports</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>📈 Attendance Reports</h2>

        <!-- Date Filter -->
        <div class="card mt-2">
            <div class="card-body">
                <form method="GET" action="reports.php">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">🔍 Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-2">
            <div class="col-4">
                <div class="stats-card">
                    <div class="icon">👥</div>
                    <h2><?php echo $total_members; ?></h2>
                    <p>Total Members</p>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card">
                    <div class="icon">✅</div>
                    <h2><?php echo $active_members; ?></h2>
                    <p>Active Members</p>
                </div>
            </div>
            <div class="col-4">
                <div class="stats-card">
                    <div class="icon">📋</div>
                    <h2><?php echo $total_checkins; ?></h2>
                    <p>Total Check-ins</p>
                    <small><?php echo date('M d', strtotime($start_date)) . ' - ' . date('M d, Y', strtotime($end_date)); ?></small>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <!-- Member-wise Attendance -->
            <div class="col-8">
                <div class="card">
                    <div class="card-header">👤 Member Attendance Summary</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Membership</th>
                                        <th>Visits</th>
                                        <th>Last Visit</th>
                                        <th>Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($member_attendance_result->num_rows > 0): ?>
                                        <?php while ($member = $member_attendance_result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo escape_html($member['name']); ?></strong><br>
                                                    <small style="color: #666;"><?php echo escape_html($member['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo escape_html($member['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo $member['visit_count']; ?> visits
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($member['last_visit']): ?>
                                                        <?php echo date('M d, Y', strtotime($member['last_visit'])); ?>
                                                    <?php else: ?>
                                                        <span style="color: #999;">No visits</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $visit_count = $member['visit_count'];
                                                    $max_visits = 30;
                                                    $percentage = min(($visit_count / $max_visits) * 100, 100);
                                                    $color_class = $percentage > 70 ? 'success' : ($percentage > 40 ? 'warning' : 'danger');
                                                    ?>
                                                    <div class="progress">
                                                        <div class="progress-bar <?php echo $color_class; ?>" style="width: <?php echo $percentage; ?>%">
                                                            <?php echo round($percentage); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No attendance data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Cards -->
            <div class="col-4">
                <!-- Top Performers -->
                <div class="card">
                    <div class="card-header success">🏆 Top Performers</div>
                    <div class="card-body">
                        <?php if ($top_performers_result->num_rows > 0): ?>
                            <?php $rank = 1; ?>
                            <?php while ($performer = $top_performers_result->fetch_assoc()): ?>
                                <div style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span class="badge badge-warning">#<?php echo $rank++; ?></span>
                                        <strong><?php echo escape_html($performer['name']); ?></strong>
                                        <br><small style="color: #666;"><?php echo escape_html($performer['membership_type']); ?></small>
                                    </div>
                                    <span class="badge badge-primary">
                                        <?php echo $performer['visit_count']; ?> visits
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #999;">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Daily Summary -->
                <div class="card mt-2">
                    <div class="card-header info">📅 Daily Summary (Last 10 Days)</div>
                    <div class="card-body">
                        <?php if ($daily_summary_result->num_rows > 0): ?>
                            <?php while ($day = $daily_summary_result->fetch_assoc()): ?>
                                <div style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong><?php echo date('M d, Y', strtotime($day['date'])); ?></strong>
                                        <br><small style="color: #666;"><?php echo date('l', strtotime($day['date'])); ?></small>
                                    </div>
                                    <div style="text-align: right;">
                                        <span class="badge badge-success"><?php echo $day['total_checkins']; ?> check-ins</span>
                                        <br><small style="color: #666;"><?php echo $day['completed_visits']; ?> completed</small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="color: #999;">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2025 Simple Gym Management System | Built with Pure HTML, CSS, PHP & MySQL</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
