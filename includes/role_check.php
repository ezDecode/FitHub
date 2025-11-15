<?php
// ========================================
// ROLE-BASED ACCESS CONTROL
// ========================================
// Functions to check user permissions

// ========================================
// CHECK IF USER HAS REQUIRED ROLE
// ========================================
function checkRole($required_role) {
    // Get user's role from session
    $user_role = $_SESSION['user_role'];
    
    // Admin can access everything
    if ($user_role === 'admin') {
        return true;
    }
    
    // Staff can access staff and member areas
    if ($user_role === 'staff' && ($required_role === 'staff' || $required_role === 'member')) {
        return true;
    }
    
    // Member can only access member areas
    if ($user_role === 'member' && $required_role === 'member') {
        return true;
    }
    
    // Access denied
    return false;
}

// ========================================
// REQUIRE SPECIFIC ROLE (redirect if not authorized)
// ========================================
function requireRole($required_role) {
    if (!checkRole($required_role)) {
        // User doesn't have permission
        // Redirect to dashboard with error
        header('Location: index.php?error=access_denied');
        exit;
    }
}

// ========================================
// CHECK IF USER IS ADMIN
// ========================================
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// ========================================
// CHECK IF USER IS STAFF (or higher)
// ========================================
function isStaff() {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    $role = $_SESSION['user_role'];
    return $role === 'staff' || $role === 'admin';
}

// ========================================
// CHECK IF USER IS MEMBER
// ========================================
function isMember() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'member';
}

// ========================================
// GET CURRENT USER ID
// ========================================
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// ========================================
// GET CURRENT USER NAME
// ========================================
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest';
}

// ========================================
// GET CURRENT USER ROLE
// ========================================
function getCurrentUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

// ========================================
// GET USER ROLE DISPLAY NAME
// ========================================
function getRoleDisplayName() {
    $role = getCurrentUserRole();
    
    if ($role === 'admin') return 'Administrator';
    if ($role === 'staff') return 'Staff';
    if ($role === 'member') return 'Member';
    
    return 'Unknown';
}
?>
