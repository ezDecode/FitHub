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
$message = '';
$message_type = '';

// Get member profile linked to this user
$member_query = $conn->prepare("SELECT * FROM members WHERE user_id = ?");
$member_query->bind_param("i", $user_id);
$member_query->execute();
$member_result = $member_query->get_result();
$member = $member_result->fetch_assoc();

// Handle profile update
if ($_POST && isset($_POST['update_profile'])) {
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if ($member) {
        $update_query = $conn->prepare("UPDATE members SET phone = ?, email = ? WHERE user_id = ?");
        $update_query->bind_param("ssi", $phone, $email, $user_id);
        
        if ($update_query->execute()) {
            $message = "Profile updated successfully!";
            $message_type = "success";
            
            // Refresh member data
            $member_query->execute();
            $member_result = $member_query->get_result();
            $member = $member_result->fetch_assoc();
        } else {
            $message = "Error updating profile.";
            $message_type = "danger";
        }
        $update_query->close();
    }
}

// Get attendance statistics
$stats_query = $conn->prepare("SELECT 
    COUNT(*) as total_visits,
    COUNT(CASE WHEN date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 END) as visits_last_30_days,
    COUNT(CASE WHEN date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as visits_last_7_days
FROM attendance WHERE member_id = ?");
$stats_query->bind_param("i", $member['id'] ?? 0);
$stats_query->execute();
$stats = $stats_query->get_result()->fetch_assoc();
$stats_query->close();

$member_query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FitHub</title>
    <link rel="stylesheet" href="../assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="../assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
    <?php include '../includes/navigation.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="quick-actions-bar">
                <div class="quick-actions-left">
                    <h2 class="page-section-title">My Profile</h2>
                    <p class="page-section-subtitle">View and manage your membership information</p>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php endif; ?>

            <?php if ($member): ?>
            <div class="profile-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                <div class="form-container">
                    <div class="form-header">
                        <div class="form-header-left">
                            <span class="material-symbols-rounded form-header-icon">person</span>
                            <h2 class="form-header-title">Profile Information</h2>
                        </div>
                    </div>
                    <form method="POST" class="member-form">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" class="form-control" value="<?php echo htmlspecialchars($member['name']); ?>" disabled>
                            <small style="color: var(--gray-400); font-size: 0.875rem;">Contact administrator to change</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($member['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($member['phone']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="membership_type" class="form-label">Membership Type</label>
                            <input type="text" id="membership_type" class="form-control" 
                                   value="<?php echo htmlspecialchars($member['membership_type']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="join_date" class="form-label">Member Since</label>
                            <input type="text" id="join_date" class="form-control" 
                                   value="<?php echo date('F j, Y', strtotime($member['join_date'])); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" id="status" class="form-control" 
                                   value="<?php echo ucfirst($member['status']); ?>" disabled>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <span class="material-symbols-rounded">save</span>
                                <span>Update Profile</span>
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="form-container" style="margin-bottom: 2rem;">
                        <div class="form-header">
                            <div class="form-header-left">
                                <span class="material-symbols-rounded form-header-icon">insights</span>
                                <h2 class="form-header-title">Activity Statistics</h2>
                            </div>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div class="stats-grid" style="display: grid; gap: 1rem;">
                                <div class="stat-item" style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span class="material-symbols-rounded" style="color: var(--primary); font-size: 2rem;">fitness_center</span>
                                        <div>
                                            <div style="font-size: 1.5rem; font-weight: 600;"><?php echo $stats['total_visits']; ?></div>
                                            <div style="color: var(--gray-400); font-size: 0.875rem;">Total Visits</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-item" style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span class="material-symbols-rounded" style="color: var(--success); font-size: 2rem;">calendar_month</span>
                                        <div>
                                            <div style="font-size: 1.5rem; font-weight: 600;"><?php echo $stats['visits_last_30_days']; ?></div>
                                            <div style="color: var(--gray-400); font-size: 0.875rem;">Last 30 Days</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="stat-item" style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <span class="material-symbols-rounded" style="color: var(--warning); font-size: 2rem;">event</span>
                                        <div>
                                            <div style="font-size: 1.5rem; font-weight: 600;"><?php echo $stats['visits_last_7_days']; ?></div>
                                            <div style="color: var(--gray-400); font-size: 0.875rem;">Last 7 Days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-container">
                        <div class="form-header">
                            <div class="form-header-left">
                                <span class="material-symbols-rounded form-header-icon">info</span>
                                <h2 class="form-header-title">Account Details</h2>
                            </div>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: grid; gap: 1rem; color: var(--gray-300);">
                                <div>
                                    <div style="color: var(--gray-400); font-size: 0.875rem;">Username</div>
                                    <div style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                                </div>
                                <div>
                                    <div style="color: var(--gray-400); font-size: 0.875rem;">Full Name</div>
                                    <div style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                                </div>
                                <div>
                                    <div style="color: var(--gray-400); font-size: 0.875rem;">Role</div>
                                    <div style="font-weight: 500;"><?php echo getRoleDisplayName(); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <span class="material-symbols-rounded empty-state-icon">person_off</span>
                <h3 class="empty-state-title">No Member Profile Found</h3>
                <p class="empty-state-description">Your account is not linked to a member profile yet. Please contact the administrator.</p>
            </div>
            <?php endif; ?>
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

        <?php if ($message): ?>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>
