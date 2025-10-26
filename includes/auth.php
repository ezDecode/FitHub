<?php
/**
 * FitHub Gym Management System
 * Authentication Logic
 * 
 * Handles user authentication, password verification, and user management
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Load required files
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/includes/session.php';

/**
 * Authenticate user with email and password
 * @param string $email User email
 * @param string $password User password
 * @return array Result array with success status and message
 */
function authenticateUser($email, $password) {
    global $db;
    
    try {
        // Validate input
        if (empty($email) || empty($password)) {
            logAuthAttempt($email, 'failed', 'Empty credentials');
            return [
                'success' => false,
                'message' => 'Email and password are required'
            ];
        }
        
        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            logAuthAttempt($email, 'failed', 'Invalid email format');
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }
        
        // Query user from database
        $query = "SELECT u.id, u.email, u.password, u.role, 
                  CASE 
                    WHEN u.role = 'member' THEN m.full_name
                    WHEN u.role = 'trainer' THEN t.full_name
                    ELSE 'Administrator'
                  END as full_name,
                  CASE 
                    WHEN u.role = 'member' THEN m.status
                    WHEN u.role = 'trainer' THEN t.status
                    ELSE 'active'
                  END as status
                  FROM users u
                  LEFT JOIN members m ON u.id = m.user_id AND u.role = 'member'
                  LEFT JOIN trainers t ON u.id = t.user_id AND u.role = 'trainer'
                  WHERE u.email = :email
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if user exists
        if (!$user) {
            logAuthAttempt($email, 'failed', 'User not found');
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            logAuthAttempt($email, 'failed', 'Invalid password');
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Check if user is active (for members and trainers)
        if (isset($user['status']) && $user['status'] !== 'active') {
            logAuthAttempt($email, 'failed', 'Account inactive/suspended');
            return [
                'success' => false,
                'message' => 'Your account is inactive. Please contact administrator.'
            ];
        }
        
        // Set login session
        setLoginSession($user['id'], $user['email'], $user['role'], $user['full_name']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'name' => $user['full_name']
            ]
        ];
        
    } catch (PDOException $e) {
        error_log("Authentication Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred during authentication. Please try again.'
        ];
    }
}

/**
 * Logout user and destroy session
 * @return array Result array with success status
 */
function logoutUser() {
    $email = getCurrentUserEmail();
    
    destroySession();
    
    // Log logout
    if (defined('LOG_AUTH_ATTEMPTS') && LOG_AUTH_ATTEMPTS) {
        logAuthAttempt($email, 'success', 'User logged out');
    }
    
    return [
        'success' => true,
        'message' => 'Logged out successfully'
    ];
}

/**
 * Create new user account
 * @param string $email User email
 * @param string $password User password
 * @param string $role User role
 * @return array Result array with success status and user ID
 */
function createUser($email, $password, $role = 'member') {
    global $db;
    
    try {
        // Validate input
        if (empty($email) || empty($password) || empty($role)) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }
        
        // Sanitize email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }
        
        // Validate role
        $validRoles = [ROLE_ADMIN, ROLE_TRAINER, ROLE_MEMBER];
        if (!in_array($role, $validRoles)) {
            return [
                'success' => false,
                'message' => 'Invalid role'
            ];
        }
        
        // Validate password strength
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            return [
                'success' => false,
                'message' => $passwordValidation['message']
            ];
        }
        
        // Check if email already exists
        $checkQuery = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return [
                'success' => false,
                'message' => 'Email already registered'
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        
        // Insert user
        $insertQuery = "INSERT INTO users (email, password, role, created_at) 
                       VALUES (:email, :password, :role, NOW())";
        $stmt = $db->prepare($insertQuery);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $userId = $db->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $userId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create user'
            ];
        }
        
    } catch (PDOException $e) {
        error_log("Create User Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while creating user'
        ];
    }
}

/**
 * Validate password strength
 * @param string $password Password to validate
 * @return array Validation result with valid status and message
 */
function validatePassword($password) {
    // Check minimum length
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return [
            'valid' => false,
            'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long'
        ];
    }
    
    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one uppercase letter'
        ];
    }
    
    // Check for at least one number
    if (!preg_match('/[0-9]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one number'
        ];
    }
    
    // Check for at least one special character
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one special character'
        ];
    }
    
    return [
        'valid' => true,
        'message' => 'Password is valid'
    ];
}

/**
 * Change user password
 * @param int $userId User ID
 * @param string $oldPassword Current password
 * @param string $newPassword New password
 * @return array Result array with success status
 */
function changePassword($userId, $oldPassword, $newPassword) {
    global $db;
    
    try {
        // Get current password hash
        $query = "SELECT password FROM users WHERE id = :user_id LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }
        
        // Verify old password
        if (!password_verify($oldPassword, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }
        
        // Validate new password
        $passwordValidation = validatePassword($newPassword);
        if (!$passwordValidation['valid']) {
            return [
                'success' => false,
                'message' => $passwordValidation['message']
            ];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        
        // Update password
        $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() 
                       WHERE id = :user_id";
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to change password'
            ];
        }
        
    } catch (PDOException $e) {
        error_log("Change Password Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while changing password'
        ];
    }
}

/**
 * Reset user password (admin function)
 * @param int $userId User ID
 * @param string $newPassword New password
 * @return array Result array with success status
 */
function resetPassword($userId, $newPassword) {
    global $db;
    
    try {
        // Validate new password
        $passwordValidation = validatePassword($newPassword);
        if (!$passwordValidation['valid']) {
            return [
                'success' => false,
                'message' => $passwordValidation['message']
            ];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
        
        // Update password
        $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() 
                       WHERE id = :user_id";
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Password reset successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to reset password'
            ];
        }
        
    } catch (PDOException $e) {
        error_log("Reset Password Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'An error occurred while resetting password'
        ];
    }
}

/**
 * Get user by ID
 * @param int $userId User ID
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    global $db;
    
    try {
        $query = "SELECT u.id, u.email, u.role, u.created_at,
                  CASE 
                    WHEN u.role = 'member' THEN m.full_name
                    WHEN u.role = 'trainer' THEN t.full_name
                    ELSE 'Administrator'
                  END as full_name
                  FROM users u
                  LEFT JOIN members m ON u.id = m.user_id AND u.role = 'member'
                  LEFT JOIN trainers t ON u.id = t.user_id AND u.role = 'trainer'
                  WHERE u.id = :user_id
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Get User Error: " . $e->getMessage());
        return null;
    }
}
?>
