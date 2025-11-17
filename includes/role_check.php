<?php
function checkRole($required_role) {
    $user_role = $_SESSION['user_role'];
    
    if ($user_role === 'admin') {
        return true;
    }
    
    if ($user_role === 'staff' && ($required_role === 'staff' || $required_role === 'member')) {
        return true;
    }
    
    if ($user_role === 'member' && $required_role === 'member') {
        return true;
    }
    
    return false;
}

function requireRole($required_role) {
    if (!checkRole($required_role)) {
        header('Location: index.php?error=access_denied');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isStaff() {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    $role = $_SESSION['user_role'];
    return $role === 'staff' || $role === 'admin';
}

function isMember() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'member';
}

function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
}

function getCurrentUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

function getRoleDisplayName() {
    $role = getCurrentUserRole();
    
    if ($role === 'admin') return 'Administrator';
    if ($role === 'staff') return 'Staff';
    if ($role === 'member') return 'Member';
    
    return 'Unknown';
}
?>
