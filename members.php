<?php
require_once 'config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$edit_member = null;

// Handle form submission for Add/Edit
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
                // Check if email already exists
                $check_email = $conn->prepare("SELECT id FROM members WHERE email = ?");
                $check_email->bind_param("s", $email);
                $check_email->execute();
                $result = $check_email->get_result();
                
                if ($result->num_rows > 0) {
                    $error_message = show_error("Email already exists!");
                } else {
                    $stmt = $conn->prepare("INSERT INTO members (name, email, phone, join_date, membership_type, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $name, $email, $phone, $join_date, $membership_type, $status);
                    
                    if ($stmt->execute()) {
                        $success_message = show_success("Member added successfully!");
                    } else {
                        $error_message = show_error("Error adding member: " . $stmt->error);
                    }
                    $stmt->close();
                }
                $check_email->close();
            } elseif ($_POST['action'] == 'edit') {
                $id = $_POST['id'];
                $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, join_date = ?, membership_type = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssssi", $name, $email, $phone, $join_date, $membership_type, $status, $id);
                
                if ($stmt->execute()) {
                    $success_message = show_success("Member updated successfully!");
                } else {
                    $error_message = show_error("Error updating member: " . $stmt->error);
                }
                $stmt->close();
            }
        } else {
            $error_message = show_error(implode("<br>", $errors));
        }
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = show_success("Member deleted successfully!");
    } else {
        $error_message = show_error("Error deleting member: " . $stmt->error);
    }
    $stmt->close();
}

// Get member data for editing
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management - Gym System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-gear-wide-connected"></i> Gym Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="members.php"><i class="bi bi-people"></i> Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Add/Edit Member Form -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-person-plus"></i> 
                            <?php echo $edit_member ? 'Edit Member' : 'Add New Member'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message) echo $success_message; ?>
                        <?php if ($error_message) echo $error_message; ?>
                        
                        <form method="POST" action="members.php">
                            <input type="hidden" name="action" value="<?php echo $edit_member ? 'edit' : 'add'; ?>">
                            <?php if ($edit_member): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_member['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo $edit_member ? escape_html($edit_member['name']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo $edit_member ? escape_html($edit_member['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone (10 digits) *</label>
                                <input type="text" class="form-control" name="phone" 
                                       pattern="[0-9]{10}" maxlength="10"
                                       value="<?php echo $edit_member ? escape_html($edit_member['phone']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Join Date *</label>
                                <input type="date" class="form-control" name="join_date" 
                                       value="<?php echo $edit_member ? $edit_member['join_date'] : date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Membership Type *</label>
                                <select class="form-select" name="membership_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Monthly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="Quarterly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                                    <option value="Yearly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Yearly') ? 'selected' : ''; ?>>Yearly</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($edit_member && $edit_member['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($edit_member && $edit_member['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> <?php echo $edit_member ? 'Update' : 'Add'; ?> Member
                                </button>
                                <?php if ($edit_member): ?>
                                    <a href="members.php" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Members List</h5>
                        <span class="badge bg-light text-dark">Total: <?php echo $members_result->num_rows; ?></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
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
                                                    <span class="badge bg-info">
                                                        <?php echo escape_html($member['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($member['status'] == 'active'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="table-actions">
                                                    <a href="members.php?edit=<?php echo $member['id']; ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="members.php?delete=<?php echo $member['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this member?');" 
                                                       title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="bi bi-inbox"></i> No members found. Add your first member!
                                            </td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
</body>
</html>
