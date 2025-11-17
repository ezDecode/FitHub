<?php
// Role helper functions - expects `session_start()` to be called by the including file

function checkRole($required_role) {
    if (!isset($_SESSION['role'])) {
        return false;
    }

    $user_role = $_SESSION['role'];

    // If admin, always allowed
    if ($user_role === 'admin') {
        return true;
    }

    // Allow passing either string or array of allowed roles
    if (is_array($required_role)) {
        return in_array($user_role, $required_role, true);
    }

    return $user_role === $required_role;
}

function requireRole($required_role) {
    if (!checkRole($required_role)) {
        header('Location: /FitHub/access-denied.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['staff', 'admin'], true);
}

function isMember() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'member';
}

function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getCurrentUserName() {
    if (isset($_SESSION['full_name']) && $_SESSION['full_name']) return $_SESSION['full_name'];
    if (isset($_SESSION['username']) && $_SESSION['username']) return $_SESSION['username'];
    return 'Guest';
}

function getCurrentUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

function getRoleDisplayName() {
    $role = getCurrentUserRole();

    if ($role === 'admin') return 'Administrator';
    if ($role === 'staff') return 'Staff';
    if ($role === 'member') return 'Member';

    return 'Unknown';
}

?>
