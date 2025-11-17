<?php
session_start();
require_once '../config.php';
require_once '../includes/role_check.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../login.php');
    exit;
}

// Admin only
requireRole('admin');

$message = '';
$message_type = '';

// Handle user creation
if ($_POST && isset($_POST['create_user'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (!empty($username) && !empty($password) && !empty($full_name) && !empty($email) && !empty($role)) {
        // Check if username or email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "Username or email already exists.";
            $message_type = "danger";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, phone, role, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $insert_stmt->bind_param("ssssss", $username, $hashed_password, $full_name, $email, $phone, $role);
            
            if ($insert_stmt->execute()) {
                $message = "User created successfully!";
                $message_type = "success";
            } else {
                $message = "Error creating user.";
                $message_type = "danger";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    } else {
        $message = "Please fill in all required fields.";
        $message_type = "danger";
    }
}

// Handle user update
if ($_POST && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    
    $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("sssssi", $full_name, $email, $phone, $role, $status, $user_id);
    
    if ($update_stmt->execute()) {
        $message = "User updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating user.";
        $message_type = "danger";
    }
    $update_stmt->close();
}

// Handle password reset
if ($_POST && isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['new_password'];
    
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $reset_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $reset_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($reset_stmt->execute()) {
            $message = "Password reset successfully!";
            $message_type = "success";
        } else {
            $message = "Error resetting password.";
            $message_type = "danger";
        }
        $reset_stmt->close();
    }
}

// Handle user deletion
if ($_POST && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Prevent self-deletion
    if ($user_id == getCurrentUserId()) {
        $message = "You cannot delete your own account.";
        $message_type = "danger";
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_stmt->bind_param("i", $user_id);
        
        if ($delete_stmt->execute()) {
            $message = "User deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting user.";
            $message_type = "danger";
        }
        $delete_stmt->close();
    }
}

// Get all users
$users_result = $conn->query("SELECT * FROM users ORDER BY role, username");
$users = [];
while ($user = $users_result->fetch_assoc()) {
    $users[] = $user;
}

// Get edit user
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    $edit_user = $edit_result->fetch_assoc();
    $edit_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - FitHub</title>
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
                    <h2 class="page-section-title">User Management</h2>
                    <p class="page-section-subtitle">Manage system users and access control</p>
                </div>
                <button type="button" class="btn btn-primary" id="toggleFormBtn" onclick="toggleUserForm()">
                    <span class="material-symbols-rounded"><?php echo $edit_user ? 'close' : 'person_add'; ?></span>
                    <span><?php echo $edit_user ? 'Cancel' : 'Add New User'; ?></span>
                </button>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
            <?php endif; ?>

            <div class="form-container" id="userFormContainer" style="display: none;">
                <div class="form-header">
                    <div class="form-header-left">
                        <span class="material-symbols-rounded form-header-icon"><?php echo $edit_user ? 'edit' : 'person_add'; ?></span>
                        <h2 class="form-header-title"><?php echo $edit_user ? 'Edit User' : 'Create New User'; ?></h2>
                    </div>
                    <button type="button" class="form-close-btn" onclick="toggleUserForm()">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                
                <form method="POST" class="member-form">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <?php if (!$edit_user): ?>
                        <div class="form-group">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['full_name']) : ''; ?>" 
                                   placeholder="Enter full name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>" 
                                   placeholder="user@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo $edit_user ? htmlspecialchars($edit_user['phone']) : ''; ?>" 
                                   placeholder="Phone number">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role" class="form-label">Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Select role</option>
                                <option value="admin" <?php echo ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                                <option value="staff" <?php echo ($edit_user && $edit_user['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                                <option value="member" <?php echo ($edit_user && $edit_user['role'] == 'member') ? 'selected' : ''; ?>>Member</option>
                            </select>
                        </div>
                        
                        <?php if ($edit_user): ?>
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo ($edit_user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <?php else: ?>
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   placeholder="Enter password" required>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $edit_user ? 'update_user' : 'create_user'; ?>" class="btn btn-primary">
                            <span class="material-symbols-rounded"><?php echo $edit_user ? 'save' : 'person_add'; ?></span>
                            <span><?php echo $edit_user ? 'Update User' : 'Create User'; ?></span>
                        </button>
                        <?php if ($edit_user): ?>
                        <a href="/FitHub/admin/user-management.php" class="btn btn-secondary">
                            <span>Cancel</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="members-directory">
                <div class="directory-header">
                    <div class="directory-title-wrapper">
                        <span class="material-symbols-rounded directory-title-icon">group</span>
                        <div>
                            <h2 class="directory-title">System Users</h2>
                            <p class="directory-subtitle">All registered system users</p>
                        </div>
                    </div>
                    <div class="directory-stats">
                        <span class="directory-stat-badge"><?php echo count($users); ?> Total</span>
                    </div>
                </div>
                
                <div class="table-container" style="background: rgba(255,255,255,0.05); border-radius: 1rem; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: rgba(255,255,255,0.05);">
                            <tr>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Username</th>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Full Name</th>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Email</th>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Role</th>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Status</th>
                                <th style="padding: 1rem; text-align: left; color: var(--gray-300);">Last Login</th>
                                <th style="padding: 1rem; text-align: right; color: var(--gray-300);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr style="border-top: 1px solid rgba(255,255,255,0.1);">
                                <td style="padding: 1rem; color: var(--white);"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td style="padding: 1rem; color: var(--gray-300);"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td style="padding: 1rem; color: var(--gray-300);"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'staff' ? 'warning' : 'info'); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge badge-<?php echo $user['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: var(--gray-400);">
                                    <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <a href="?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-secondary">
                                            <span class="material-symbols-rounded">edit</span>
                                        </a>
                                        <?php if ($user['id'] != getCurrentUserId()): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                                <span class="material-symbols-rounded">delete</span>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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

        function toggleUserForm() {
            const formContainer = document.getElementById('userFormContainer');
            const toggleBtn = document.getElementById('toggleFormBtn');
            
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                toggleBtn.innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
                toggleBtn.classList.remove('btn-primary');
                toggleBtn.classList.add('btn-secondary');
            } else {
                <?php if ($edit_user): ?>
                    window.location.href = '/FitHub/admin/user-management.php';
                <?php else: ?>
                    formContainer.style.display = 'none';
                    toggleBtn.innerHTML = '<span class="material-symbols-rounded">person_add</span><span>Add New User</span>';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-primary');
                <?php endif; ?>
            }
        }

        <?php if ($edit_user): ?>
            document.getElementById('userFormContainer').style.display = 'block';
            document.getElementById('toggleFormBtn').innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
            document.getElementById('toggleFormBtn').classList.remove('btn-primary');
            document.getElementById('toggleFormBtn').classList.add('btn-secondary');
        <?php endif; ?>

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
