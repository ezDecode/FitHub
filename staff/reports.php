<?php
session_start();
require_once '../config.php';
require_once '../includes/role_check.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../login.php');
    exit;
}

// Reports should be available to staff and admin only
requireRole(['admin', 'staff']);

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$query1 = "SELECT COUNT(*) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'";
$result1 = $conn->query($query1);
$total_checkins = $result1->fetch_assoc()['total'];

$query2 = "SELECT COUNT(DISTINCT date) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'";
$result2 = $conn->query($query2);
$active_days = $result2->fetch_assoc()['total'];

$query3 = "SELECT COUNT(DISTINCT member_id) as total FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'";
$result3 = $conn->query($query3);
$unique_members = $result3->fetch_assoc()['total'];

if ($active_days > 0) {
    $avg_daily = round($total_checkins / $active_days, 1);
} else {
    $avg_daily = 0;
}

$query4 = "SELECT date, COUNT(*) as checkins 
          FROM attendance
          WHERE date BETWEEN '$start_date' AND '$end_date' 
          GROUP BY date 
          ORDER BY date";
$result4 = $conn->query($query4);

$daily_trends = [];
while ($one_day = $result4->fetch_assoc()) {
    $daily_trends[] = $one_day;
}

$query5 = "SELECT m.name, m.membership_type, COUNT(a.id) as checkins 
          FROM members m 
          JOIN attendance a ON m.id = a.member_id 
          WHERE a.date BETWEEN '$start_date' AND '$end_date' 
          GROUP BY m.id 
          ORDER BY checkins DESC 
          LIMIT 10";
$result5 = $conn->query($query5);

$top_members = [];
while ($one_member = $result5->fetch_assoc()) {
    $top_members[] = $one_member;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - FitHub Gym Management</title>
    <link rel="stylesheet" href="../assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="../assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../includes/navigation.php'; ?>
    
    <main class="main-content">
        <div class="container">
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

            <div class="reports-charts-grid">
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