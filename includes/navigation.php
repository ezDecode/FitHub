<?php
// Navigation component with role-based filtering
// Expects session_start() and role_check.php to be included
?>
<nav class="navbar">
    <div class="container">
        <a href="/FitHub/index.php" class="navbar-brand">
            <span class="material-symbols-rounded brand-icon">fitness_center</span>
            <span class="brand-text">FitHub</span>
        </a>
        <button class="navbar-toggle" id="navbarToggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="navbar-menu" id="navbarMenu">
            <li><a href="/FitHub/index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Dashboard</a></li>
            
            <?php if (isStaff()): // Admin and Staff only ?>
            <li><a href="/FitHub/staff/members.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php') ? 'class="active"' : ''; ?>>Members</a></li>
            <li><a href="/FitHub/staff/attendance.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance.php') ? 'class="active"' : ''; ?>>Attendance</a></li>
            <li><a href="/FitHub/staff/reports.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'class="active"' : ''; ?>>Analytics</a></li>
            <?php endif; ?>
            
            <?php if (isMember()): // Member only ?>
            <li><a href="/FitHub/member/profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?>>My Profile</a></li>
            <li><a href="/FitHub/member/attendance.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'attendance.php' && strpos($_SERVER['PHP_SELF'], 'member') !== false) ? 'class="active"' : ''; ?>>My Attendance</a></li>
            <?php endif; ?>
            
            <?php if (isAdmin()): // Admin only ?>
            <li><a href="/FitHub/admin/user-management.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'user-management.php') ? 'class="active"' : ''; ?>>Users</a></li>
            <?php endif; ?>
            
            <li><a href="/FitHub/logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>
</nav>
