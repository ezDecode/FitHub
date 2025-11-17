<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

$result = $conn->query("SELECT COUNT(*) as total FROM members");
$total_members = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM members WHERE status = 'active'");
$active_members = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE date = CURDATE()");
$today_checkins = $result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitHub - Premium Gym Management System</title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
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
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="members.php">Members</a></li>
                <li><a href="attendance.php">Attendance</a></li>
                <li><a href="reports.php">Analytics</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero-section-new">
        <div class="hero-gradient-bg"></div>
        <div class="container">
            <div class="hero-content-new">
                <h1 class="hero-title-new">
                    Elevate Your Gym to
                    <span class="gradient-text">New Heights</span>
                </h1>
                <p class="hero-subtitle-new">
                    Streamline operations, boost member engagement, and grow your fitness business with our all-in-one intelligent management system.
                </p>
                
                <div class="hero-cta">
                    <a href="members.php" class="cta-primary">
                        <span>Get Started</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.16669 10H15.8334M15.8334 10L10 4.16666M15.8334 10L10 15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="reports.php" class="cta-secondary">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 5V15M10 15L5 10M10 15L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>View Analytics</span>
                    </a>
                </div>

                <div class="hero-stats-grid">
                    <div class="stat-card">
                        <span class="material-symbols-rounded stat-icon">groups</span>
                        <div class="stat-content">
                            <div class="stat-number" data-target="<?php echo $total_members; ?>">0</div>
                            <div class="stat-label">Total Members</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="material-symbols-rounded stat-icon">trending_up</span>
                        <div class="stat-content">
                            <div class="stat-number" data-target="<?php echo $active_members; ?>">0</div>
                            <div class="stat-label">Active Members</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <span class="material-symbols-rounded stat-icon">event_available</span>
                        <div class="stat-content">
                            <div class="stat-number" data-target="<?php echo $today_checkins; ?>">0</div>
                            <div class="stat-label">Today's Check-ins</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="main-content">
        <section class="features-section">
            <div class="container">
                <div class="section-header-new">
                    <span class="section-label">Features We Offer</span>
                    <h2 class="section-title-new">Everything You Need to Manage Your Gym</h2>
                    <p class="section-subtitle-new">
                        Comprehensive tools designed to streamline operations and enhance member experience
                    </p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <span class="material-symbols-rounded feature-icon">group</span>
                                <div class="feature-icon-bg"></div>
                            </div>
                            <span class="feature-badge">Essential</span>
                        </div>
                        <h3 class="feature-title">Member Management</h3>
                        <p class="feature-description">
                            Complete member database with profiles, subscriptions, and history tracking. Manage all member data in one centralized location.
                        </p>
                        <div class="feature-list">
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Profile Management</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Membership Plans</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Activity History</span>
                            </div>
                        </div>
                        <a href="members.php" class="feature-link">
                            <span>Explore Members</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </a>
                    </div>

                    <div class="feature-card feature-card-highlight">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <span class="material-symbols-rounded feature-icon">how_to_reg</span>
                                <div class="feature-icon-bg"></div>
                            </div>
                            <span class="feature-badge feature-badge-highlight">Popular</span>
                        </div>
                        <h3 class="feature-title">Real-Time Attendance</h3>
                        <p class="feature-description">
                            Smart check-in system with automated tracking, instant notifications, and comprehensive attendance reports.
                        </p>
                        <div class="feature-list">
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Quick Check-in/out</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Live Tracking</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Attendance Reports</span>
                            </div>
                        </div>
                        <a href="attendance.php" class="feature-link">
                            <span>Track Attendance</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                        </a>
                    </div>

                    <div class="feature-card">
                        <div class="feature-card-header">
                            <div class="feature-icon-wrapper">
                                <span class="material-symbols-rounded feature-icon">insights</span>
                                <div class="feature-icon-bg"></div>
                            </div>
                            <span class="feature-badge">Analytics</span>
                        </div>
                        <h3 class="feature-title">Advanced Analytics</h3>
                        <p class="feature-description">
                            Powerful insights and detailed reports to make data-driven decisions and grow your business effectively.
                        </p>
                        <div class="feature-list">
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Revenue Insights</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Member Analytics</span>
                            </div>
                            <div class="feature-list-item">
                                <span class="material-symbols-rounded">check_circle</span>
                                <span>Custom Reports</span>
                            </div>
                        </div>
                        <a href="reports.php" class="feature-link">
                            <span>View Analytics</span>
                            <span class="material-symbols-rounded">arrow_forward</span>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M3.33331 8H12.6666M12.6666 8L8 3.33334M12.6666 8L8 12.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-overview-section">
            <div class="container">
                <div class="section-header-new">
                    <span class="section-label">System Overview</span>
                    <h2 class="section-title-new">Real-Time Dashboard</h2>
                    <p class="section-subtitle-new">
                        Monitor your gym's performance and activity in real-time
                    </p>
                </div>

                <div class="dashboard-overview-grid">
                    <div class="stats-row">
                        <div class="mini-stat-card">
                            <span class="material-symbols-rounded mini-stat-icon">percent</span>
                            <div class="mini-stat-content">
                                <div class="mini-stat-value"><?php echo round(($active_members / max($total_members, 1)) * 100); ?>%</div>
                                <div class="mini-stat-label">Active Rate</div>
                            </div>
                        </div>
                        <div class="mini-stat-card">
                            <span class="material-symbols-rounded mini-stat-icon">trending_up</span>
                            <div class="mini-stat-content">
                                <div class="mini-stat-value"><?php echo $today_checkins > 0 ? round(($today_checkins / max($active_members, 1)) * 100) : 0; ?>%</div>
                                <div class="mini-stat-label">Daily Active</div>
                            </div>
                        </div>
                        <div class="mini-stat-card">
                            <span class="material-symbols-rounded mini-stat-icon">person_off</span>
                            <div class="mini-stat-content">
                                <div class="mini-stat-value"><?php echo $total_members - $active_members; ?></div>
                                <div class="mini-stat-label">Inactive Members</div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-cards-grid">
                        <div class="modern-dashboard-card">
                            <div class="modern-card-header">
                                <div class="card-header-left">
                                    <h3 class="card-title-modern">System Status</h3>
                                </div>
                                <div class="status-badge status-badge-active">All Systems Operational</div>
                            </div>
                            <div class="modern-card-content">
                                <div class="status-items">
                                    <div class="status-item-modern">
                                        <div class="status-icon-wrapper">
                                            <div class="status-indicator active"></div>
                                        </div>
                                        <div class="status-details">
                                            <div class="status-name">Database Connection</div>
                                            <div class="status-value">Connected & Synced</div>
                                        </div>
                                    </div>
                                    <div class="status-item-modern">
                                        <div class="status-icon-wrapper">
                                            <div class="status-indicator active"></div>
                                        </div>
                                        <div class="status-details">
                                            <div class="status-name">Member System</div>
                                            <div class="status-value">Fully Operational</div>
                                        </div>
                                    </div>
                                    <div class="status-item-modern">
                                        <div class="status-icon-wrapper">
                                            <div class="status-indicator active"></div>
                                        </div>
                                        <div class="status-details">
                                            <div class="status-name">Tracking System</div>
                                            <div class="status-value">Active & Recording</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modern-dashboard-card">
                            <div class="modern-card-header">
                                <div class="card-header-left">
                                    <h3 class="card-title-modern">Today's Activity</h3>
                                </div>
                                <div class="time-badge"><?php echo date('l, M j'); ?></div>
                            </div>
                            <div class="modern-card-content">
                                <div class="activity-summary">
                                    <div class="activity-main-stat">
                                        <div class="activity-number"><?php echo $today_checkins; ?></div>
                                        <div class="activity-label">Total Check-ins Today</div>
                                    </div>
                                    <div class="activity-progress">
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: <?php echo min(($today_checkins / max($active_members, 1)) * 100, 100); ?>%"></div>
                                        </div>
                                        <div class="progress-label">
                                            <span><?php echo min(round(($today_checkins / max($active_members, 1)) * 100), 100); ?>% of active members</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="attendance.php" class="card-action-link">
                                    <span>View Full Attendance</span>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M3.33331 8H12.6666M12.6666 8L8 3.33334M12.6666 8L8 12.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="modern-dashboard-card card-full-width">
                            <div class="modern-card-header">
                                <div class="card-header-left">
                                    <h3 class="card-title-modern">Recent Activity</h3>
                                </div>
                                <a href="attendance.php" class="view-all-link">View All</a>
                            </div>
                            <div class="modern-card-content">
                                <?php
                                $recent_activity = $conn->query("
                                    SELECT a.*, m.name 
                                    FROM attendance a 
                                    JOIN members m ON a.member_id = m.id 
                                    ORDER BY a.check_in_time DESC 
                                    LIMIT 5
                                ");
                                ?>
                                <div class="activity-timeline">
                                    <?php if ($recent_activity && $recent_activity->num_rows > 0): ?>
                                        <?php while($activity = $recent_activity->fetch_assoc()): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-marker <?php echo $activity['check_out_time'] ? 'success' : 'info'; ?>"></div>
                                                <div class="timeline-content">
                                                    <div class="timeline-header">
                                                        <span class="timeline-title"><?php echo htmlspecialchars($activity['name']); ?> <?php echo $activity['check_out_time'] ? 'checked out' : 'checked in'; ?></span>
                                                        <span class="timeline-time"><?php echo date('g:i A', strtotime($activity['check_in_time'])); ?></span>
                                                    </div>
                                                    <div class="timeline-description">
                                                        <?php 
                                                        if ($activity['check_out_time']) {
                                                            $duration = strtotime($activity['check_out_time']) - strtotime($activity['check_in_time']);
                                                            $hours = floor($duration / 3600);
                                                            $minutes = floor(($duration % 3600) / 60);
                                                            echo "Session duration: {$hours}h {$minutes}m";
                                                        } else {
                                                            echo "Currently at the gym";
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker primary"></div>
                                            <div class="timeline-content">
                                                <div class="timeline-header">
                                                    <span class="timeline-title">No recent activity</span>
                                                    <span class="timeline-time">Today</span>
                                                </div>
                                                <div class="timeline-description">Waiting for member check-ins</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
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

        function animateNumbers() {
            const counters = document.querySelectorAll('[data-target]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000;
                const steps = 60;
                const increment = target / steps;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, duration / steps);
            });
        }

        function initScrollAnimations() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.feature-card, .modern-dashboard-card, .mini-stat-card').forEach(el => {
                observer.observe(el);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                animateNumbers();
                initScrollAnimations();
            }, 300);
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>