<?php
session_start();
require_once 'includes/role_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - FitHub</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3.3">
</head>
<body>
    <div class="centered-container" style="min-height: 80vh; display:flex; align-items:center; justify-content:center;">
        <div style="text-align:center; max-width:640px; padding:2rem;">
            <h1 style="font-size:2rem; margin-bottom:0.5rem;">Access Denied</h1>
            <p style="color:var(--gray-400); margin-bottom:1.25rem;">You do not have permission to view this page.</p>
            <div style="display:flex; gap:0.5rem; justify-content:center;">
                <a href="/FitHub/index.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="/FitHub/logout.php" class="btn">Sign Out</a>
            </div>
            <p style="margin-top:1rem; color:var(--gray-400);">Logged in as: <strong><?php echo htmlspecialchars(getCurrentUserName()); ?></strong> (<?php echo htmlspecialchars(getRoleDisplayName()); ?>)</p>
        </div>
    </div>
</body>
</html>
