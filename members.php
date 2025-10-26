<?php
require_once 'config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$edit_member = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $membership_type = trim($_POST['membership_type']);
        $join_date = trim($_POST['join_date']);
        $status = isset($_POST['status']) ? $_POST['status'] : 'active';

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = "Name is required";
        if (empty($email) || !validate_email($email)) $errors[] = "Valid email is required";
        if (empty($phone) || !validate_phone($phone)) $errors[] = "Valid 10-digit phone number is required";
        if (empty($membership_type)) $errors[] = "Membership type is required";
        if (empty($join_date)) $errors[] = "Join date is required";

        if (empty($errors)) {
            if ($_POST['action'] == 'add') {
                $check_email = $conn->prepare("SELECT id FROM members WHERE email = ?");
                $check_email->bind_param("s", $email);
                $check_email->execute();
                $result = $check_email->get_result();
                
                if ($result->num_rows > 0) {
                    $error_message = "Email already exists!";
                } else {
                    $stmt = $conn->prepare("INSERT INTO members (name, email, phone, join_date, membership_type, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $name, $email, $phone, $join_date, $membership_type, $status);
                    
                    if ($stmt->execute()) {
                        $success_message = "Member added successfully!";
                    } else {
                        $error_message = "Error adding member";
                    }
                    $stmt->close();
                }
                $check_email->close();
            } elseif ($_POST['action'] == 'edit') {
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, join_date = ?, membership_type = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $name, $email, $phone, $join_date, $membership_type, $status, $id);
                
                if ($stmt->execute()) {
                    $success_message = "Member updated successfully!";
                } else {
                    $error_message = "Error updating member";
                }
                $stmt->close();
            }
        } else {
            $error_message = implode("<br>", $errors);
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Member deleted successfully!";
    } else {
        $error_message = "Error deleting member";
    }
    $stmt->close();
}

// Get member for editing
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_member = $result->fetch_assoc();
    $stmt->close();
}

// Get all members
$members_query = "SELECT * FROM members ORDER BY id DESC";
$members_result = $conn->query($members_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Manage gym members - Add, edit, and view member information">
    <meta name="theme-color" content="#667eea">
    <title>Members Management - Gym System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">🏋️ Gym Management System</a>
            <button class="navbar-toggle" onclick="toggleMenu()">☰</button>
            <ul class="navbar-menu" id="navMenu">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="members.php" class="active">👥 Members</a></li>
                <li><a href="attendance.php">📅 Attendance</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Add/Edit Member Form -->
            <div class="col-12 col-4">
                <div class="card">
                    <div class="card-header">
                        <?php echo $edit_member ? '✏️ Edit Member' : '➕ Add New Member'; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <?php echo escape_html($success_message); ?>
                                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <?php echo escape_html($error_message); ?>
                                <button class="alert-close" onclick="this.parentElement.style.display='none'">×</button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="members.php">
                            <input type="hidden" name="action" value="<?php echo $edit_member ? 'edit' : 'add'; ?>">
                            <?php if ($edit_member): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_member['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo $edit_member ? escape_html($edit_member['name']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $edit_member ? escape_html($edit_member['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Phone (10 digits) *</label>
                                <input type="text" class="form-control" name="phone" 
                                       pattern="[0-9]{10}" maxlength="10"
                                       value="<?php echo $edit_member ? escape_html($edit_member['phone']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Join Date *</label>
                                <input type="date" class="form-control" name="join_date" 
                                       value="<?php echo $edit_member ? $edit_member['join_date'] : date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Membership Type *</label>
                                <select class="form-select" name="membership_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Monthly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="Quarterly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                                    <option value="Yearly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Yearly') ? 'selected' : ''; ?>>Yearly</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($edit_member && $edit_member['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($edit_member && $edit_member['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <?php echo $edit_member ? '💾 Update Member' : '➕ Add Member'; ?>
                            </button>
                            
                            <?php if ($edit_member): ?>
                                <a href="members.php" class="btn btn-secondary btn-block mt-1">❌ Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="col-12 col-8">
                <div class="card">
                    <div class="card-header success">
                        👥 Members List (Total: <?php echo $members_result->num_rows; ?>)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Membership</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($members_result->num_rows > 0): ?>
                                        <?php while ($member = $members_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $member['id']; ?></td>
                                                <td><?php echo escape_html($member['name']); ?></td>
                                                <td><?php echo escape_html($member['email']); ?></td>
                                                <td><?php echo escape_html($member['phone']); ?></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo escape_html($member['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($member['status'] == 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="members.php?edit=<?php echo $member['id']; ?>" 
                                                       class="btn btn-warning btn-sm">✏️ Edit</a>
                                                    <a href="members.php?delete=<?php echo $member['id']; ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Delete this member?');">🗑️ Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No members found. Add your first member!</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2025 Simple Gym Management System | Built with Pure HTML, CSS, PHP & MySQL</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>
