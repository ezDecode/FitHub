<?php
require_once 'config.php';

// Get date filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get overall statistics
$total_members_query = "SELECT COUNT(*) as total FROM members";
$total_members = $conn->query($total_members_query)->fetch_assoc()['total'];

$active_members_query = "SELECT COUNT(*) as total FROM members WHERE status = 'active'";
$active_members = $conn->query($active_members_query)->fetch_assoc()['total'];

$total_checkins_query = "SELECT COUNT(*) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'";
$total_checkins = $conn->query($total_checkins_query)->fetch_assoc()['total'];

// Get member-wise attendance count
$member_attendance_query = "
    SELECT 
        m.id,
        m.name, 
        m.email,
        m.membership_type,
        COUNT(a.id) as visit_count,
        MAX(a.date) as last_visit
    FROM members m
    LEFT JOIN attendance a ON m.id = a.member_id AND a.date BETWEEN '$start_date' AND '$end_date'
    WHERE m.status = 'active'
    GROUP BY m.id, m.name, m.email, m.membership_type
    ORDER BY visit_count DESC, m.name ASC
";
$member_attendance_result = $conn->query($member_attendance_query);

// Get daily attendance summary
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

// Get top performers
$top_performers_query = "
    SELECT 
        m.name,
        m.membership_type,
        COUNT(a.id) as visit_count
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
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
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="members.php"><i class="bi bi-people"></i> Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-graph-up"></i> Attendance Reports</h2>
        </div>

        <!-- Date Filter -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" action="reports.php" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card blue shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Members</h6>
                            <h2 class="mb-0"><?php echo $total_members; ?></h2>
                        </div>
                        <i class="bi bi-people stat-icon text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card green shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Members</h6>
                            <h2 class="mb-0"><?php echo $active_members; ?></h2>
                        </div>
                        <i class="bi bi-person-check stat-icon text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card orange shadow">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Check-ins</h6>
                            <h2 class="mb-0"><?php echo $total_checkins; ?></h2>
                            <small class="text-muted"><?php echo date('M d', strtotime($start_date)) . ' - ' . date('M d, Y', strtotime($end_date)); ?></small>
                        </div>
                        <i class="bi bi-calendar-check stat-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Member-wise Attendance -->
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Member Attendance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
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
                                                    <small class="text-muted"><?php echo escape_html($member['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo escape_html($member['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php echo $member['visit_count']; ?> visits
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($member['last_visit']): ?>
                                                        <?php echo date('M d, Y', strtotime($member['last_visit'])); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No visits</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $visit_count = $member['visit_count'];
                                                    $max_visits = 30; // Assuming monthly max
                                                    $percentage = min(($visit_count / $max_visits) * 100, 100);
                                                    $color = $percentage > 70 ? 'success' : ($percentage > 40 ? 'warning' : 'danger');
                                                    ?>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-<?php echo $color; ?>" 
                                                             style="width: <?php echo $percentage; ?>%">
                                                            <?php echo round($percentage); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="bi bi-inbox"></i> No attendance data available
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Cards -->
            <div class="col-md-4">
                <!-- Top Performers -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-trophy"></i> Top Performers</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($top_performers_result->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php $rank = 1; ?>
                                <?php while ($performer = $top_performers_result->fetch_assoc()): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <span class="badge bg-warning text-dark me-2">#<?php echo $rank++; ?></span>
                                            <strong><?php echo escape_html($performer['name']); ?></strong>
                                            <br><small class="text-muted"><?php echo escape_html($performer['membership_type']); ?></small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">
                                            <?php echo $performer['visit_count']; ?> visits
                                        </span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Daily Summary -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-week"></i> Daily Summary (Last 10 Days)</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($daily_summary_result->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($day = $daily_summary_result->fetch_assoc()): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong><?php echo date('M d, Y', strtotime($day['date'])); ?></strong>
                                            <br><small class="text-muted"><?php echo date('l', strtotime($day['date'])); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success"><?php echo $day['total_checkins']; ?> check-ins</span>
                                            <br><small class="text-muted"><?php echo $day['completed_visits']; ?> completed</small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No data available</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>
