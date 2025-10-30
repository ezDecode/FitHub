<?php
session_start();
require_once 'config.php';

// if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
 header('Location: login.php');
 exit;
}

// Get date range from URL parameters or use default
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch report data
$total_checkins = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$active_days = $conn->query("SELECT COUNT(DISTINCT date) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$unique_members = $conn->query("SELECT COUNT(DISTINCT member_id) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'")->fetch_assoc()['total'];

$avg_daily = $active_days > 0 ? round($total_checkins / $active_days, 1) : 0;

// Daily check-in trends
$result = $conn->query("
    SELECT date, COUNT(*) as checkins 
    FROM attendance
    WHERE date BETWEEN '$start_date' AND '$end_date' 
    GROUP BY date 
    ORDER BY date
");
$daily_trends = [];
while ($row = $result->fetch_assoc()) {
    $daily_trends[] = $row;
}

// Top members
$result = $conn->query("
    SELECT m.name, m.membership_type, COUNT(a.id) as checkins 
    FROM members m 
    JOIN attendance a ON m.id = a.member_id 
    WHERE a.date BETWEEN '$start_date' AND '$end_date' 
    GROUP BY m.id 
    ORDER BY checkins DESC 
    LIMIT 10
");
$top_members = [];
while ($row = $result->fetch_assoc()) {
    $top_members[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - FitHub Gym Management</title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li><a href="attendance.php">Attendance</a></li>
                <li><a href="reports.php" class="active">Analytics</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Reports Header -->
            <div class="reports-header-bar">
                <div class="reports-header-content">
                    <h2 class="page-section-title">Analytics & Reports</h2>
                    <p class="page-section-subtitle">Comprehensive insights into your gym performance</p>
                </div>
                <div class="reports-date-range">
                    <span class="material-symbols-rounded">date_range</span>
                    <span><?php echo date('M j', strtotime($start_date)); ?> - <?php echo date('M j, Y', strtotime($end_date)); ?></span>
                </div>
            </div>

            <!-- Filter Reports -->
            <div class="reports-filter-card">
                <div class="form-header">
                    <div class="form-header-left">
                        <span class="material-symbols-rounded form-header-icon">analytics</span>
                        <h2 class="form-header-title">Filter Reports</h2>
                    </div>
                    <button type="button" class="filter-toggle-btn" onclick="toggleFilterForm()">
                        <span class="material-symbols-rounded">expand_more</span>
                    </button>
                </div>
                <form method="GET" class="filter-form" id="filterForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="<?php echo $start_date; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                value="<?php echo $end_date; ?>" required>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-rounded">trending_up</span>
                            <span>Generate Report</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Analytics Overview -->
            <div class="reports-analytics-grid">
                <div class="reports-analytics-card">
                    <div class="analytics-card-icon-wrapper">
                        <span class="material-symbols-rounded analytics-icon">task_alt</span>
                    </div>
                    <div class="analytics-content">
                        <div class="analytics-number"><?php echo $total_checkins; ?></div>
                        <div class="analytics-label">Total Check-ins</div>
                        <div class="analytics-subtext">All visits combined</div>
                    </div>
                </div>
                <div class="reports-analytics-card">
                    <div class="analytics-card-icon-wrapper">
                        <span class="material-symbols-rounded analytics-icon">calendar_month</span>
                    </div>
                    <div class="analytics-content">
                        <div class="analytics-number"><?php echo $active_days; ?></div>
                        <div class="analytics-label">Active Days</div>
                        <div class="analytics-subtext">With activity</div>
                    </div>
                </div>
                <div class="reports-analytics-card">
                    <div class="analytics-card-icon-wrapper">
                        <span class="material-symbols-rounded analytics-icon">groups</span>
                    </div>
                    <div class="analytics-content">
                        <div class="analytics-number"><?php echo $unique_members; ?></div>
                        <div class="analytics-label">Unique Members</div>
                        <div class="analytics-subtext">Distinct visitors</div>
                    </div>
                </div>
                <div class="reports-analytics-card">
                    <div class="analytics-card-icon-wrapper">
                        <span class="material-symbols-rounded analytics-icon">trending_up</span>
                    </div>
                    <div class="analytics-content">
                        <div class="analytics-number"><?php echo $avg_daily; ?></div>
                        <div class="analytics-label">Avg Daily</div>
                        <div class="analytics-subtext">Per day average</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="reports-charts-grid">
                <!-- Daily Check-in Trends -->
                <div class="reports-chart-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <span class="material-symbols-rounded">show_chart</span>
                            <span>Daily Check-in Trends</span>
                        </div>
                        <div class="chart-badge"><?php echo $active_days; ?> Days</div>
                    </div>
                    <div class="chart-content">
                        <canvas id="dailyTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Top Members -->
                <div class="reports-chart-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <span class="material-symbols-rounded">emoji_events</span>
                            <span>Top Members</span>
                        </div>
                        <div class="chart-badge">Top <?php echo count($top_members); ?></div>
                    </div>
                    <div class="chart-content">
                        <?php if (count($top_members) > 0): ?>
                            <div class="top-members-list">
                                <?php foreach ($top_members as $index => $member): ?>
                                    <div class="top-member-item">
                                        <div class="top-member-rank">#<?php echo $index + 1; ?></div>
                                        <div class="top-member-content">
                                            <h4 class="top-member-name"><?php echo htmlspecialchars($member['name']); ?></h4>
                                            <span class="top-member-badge"><?php echo htmlspecialchars($member['membership_type']); ?></span>
                                        </div>
                                        <div class="top-member-checkins">
                                            <span class="checkin-number"><?php echo $member['checkins']; ?></span>
                                            <span class="checkin-label">check-ins</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <h3 class="empty-state-title">No Data Available</h3>
                                <p class="empty-state-description">No attendance data found for the selected date range.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Additional Insights -->
            <div class="insights-section">
                <div class="insight-card">
                    <div class="insight-header">
                        <span class="material-symbols-rounded insight-icon">assignment</span>
                        <h3 class="insight-title">Report Summary</h3>
                    </div>
                    <div class="insight-content">
                        <p>From <?php echo date('M j', strtotime($start_date)); ?> to
                            <?php echo date('M j, Y', strtotime($end_date)); ?>, your gym had <?php echo $total_checkins; ?>
                            total check-ins across <?php echo $active_days; ?> days. The average daily attendance was
                            <?php echo $avg_daily; ?> members per day.</p>
                    </div>
                </div>
                <div class="insight-card">
                    <div class="insight-header">
                        <span class="material-symbols-rounded insight-icon">lightbulb</span>
                        <h3 class="insight-title">Insights & Recommendations</h3>
                    </div>
                    <div class="insight-content">
                        <ul class="insight-list">
                            <li><span class="material-symbols-rounded">schedule</span>Monitor peak attendance days to optimize staff scheduling</li>
                            <li><span class="material-symbols-rounded">star</span>Engage with top members to maintain high retention rates</li>
                            <li><span class="material-symbols-rounded">trending_down</span>Analyze low-attendance days to identify improvement opportunities</li>
                            <li><span class="material-symbols-rounded">feedback</span>Consider member feedback to enhance gym experience</li>
                        </ul>
                    </div>
                </div>
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

    // Toggle filter form
    function toggleFilterForm() {
        const form = document.getElementById('filterForm');
        const btn = document.querySelector('.filter-toggle-btn .material-symbols-rounded');
        if (form.style.display === 'none') {
            form.style.display = 'flex';
            btn.textContent = 'expand_more';
        } else {
            form.style.display = 'none';
            btn.textContent = 'expand_less';
        }
    }

    // Initialize Chart.js
    const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
    const dailyTrendsData = <?php echo json_encode($daily_trends); ?>;

    const labels = dailyTrendsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    });

    const data = dailyTrendsData.map(item => item.checkins);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Check-ins',
                data: data,
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                borderColor: 'rgba(255, 255, 255, 0.3)',
                borderWidth: 1,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    }
                }
            }
        }
    });
    </script>
</body>

</html>
</html>