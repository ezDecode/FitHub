<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit;
}

// Variables to show messages to user
$message = '';
$message_type = '';

// Check if form was submitted (user clicked a button)
if ($_POST) {
    
    // ADD NEW MEMBER
    if (isset($_POST['add_member'])) {
        // Step 1: Get all data from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $join_date = $_POST['join_date'];
        $membership_type = $_POST['membership_type'];
        $status = $_POST['status'];
        
        // Step 2: Check if this email is already used by someone else
        $check_email = $conn->query("SELECT id FROM members WHERE email='$email'");
        
        // Step 3: If email exists, show error
        if ($check_email->num_rows > 0) {
            $message = "A member with this email already exists.";
            $message_type = "danger";
        } 
        // Step 4: If email is new, add the member
        else {
            $add_member_query = "INSERT INTO members (name, email, phone, join_date, membership_type, status) 
                                 VALUES ('$name', '$email', '$phone', '$join_date', '$membership_type', '$status')";
            
            // Try to add the member
            if ($conn->query($add_member_query)) {
                $message = "Member added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding member.";
                $message_type = "danger";
            }
        }
    }
    
    // UPDATE EXISTING MEMBER
    if (isset($_POST['update_member'])) {
        // Step 1: Get the member ID and all new data from form
        $member_id = $_POST['member_id'];
        $new_name = $_POST['name'];
        $new_email = $_POST['email'];
        $new_phone = $_POST['phone'];
        $new_join_date = $_POST['join_date'];
        $new_membership_type = $_POST['membership_type'];
        $new_status = $_POST['status'];
        
        // Step 2: Check if new email is already used by a DIFFERENT member
        $check_email = $conn->query("SELECT id FROM members WHERE email='$new_email' AND id != $member_id");
        
        // Step 3: If email is taken by someone else, show error
        if ($check_email->num_rows > 0) {
            $message = "Email already in use by another member.";
            $message_type = "danger";
        } 
        // Step 4: If email is OK, update the member
        else {
            $update_query = "UPDATE members 
                            SET name='$new_name', 
                                email='$new_email', 
                                phone='$new_phone', 
                                join_date='$new_join_date', 
                                membership_type='$new_membership_type', 
                                status='$new_status' 
                            WHERE id=$member_id";
            
            // Try to update
            if ($conn->query($update_query)) {
                $message = "Member updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating member.";
                $message_type = "danger";
            }
        }
    }
    
    // DELETE MEMBER
    if (isset($_POST['delete_member'])) {
        // Step 1: Get the member ID to delete
        $member_id = $_POST['member_id'];
        
        // Step 2: First delete all attendance records for this member
        // (We must delete attendance first because it depends on the member)
        $delete_attendance = $conn->query("DELETE FROM attendance WHERE member_id=$member_id");
        
        // Step 3: Now delete the member
        $delete_member = $conn->query("DELETE FROM members WHERE id=$member_id");
        
        // Step 4: Check if deletion worked
        if ($delete_member) {
            $message = "Member deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting member.";
            $message_type = "danger";
        }
    }
}

// CHECK IF WE ARE EDITING A MEMBER
// If URL has ?edit=5, we are editing member with ID 5
$edit_member = null;
if (isset($_GET['edit'])) {
    $member_id = $_GET['edit'];
    
    // Get this member's data from database
    $get_member = $conn->query("SELECT * FROM members WHERE id=$member_id");
    $edit_member = $get_member->fetch_assoc();
}

// GET ALL MEMBERS FROM DATABASE
// This will show in the members list below
$all_members_query = $conn->query("SELECT * FROM members ORDER BY name");

// Put all members into an array
$members = [];
while ($one_member = $all_members_query->fetch_assoc()) {
    $members[] = $one_member;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - FitHub Gym Management</title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.3">
    <link rel="stylesheet" href="assets/css/style.css?v=3.3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
    <!-- Modern Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="material-symbols-rounded brand-icon">fitness_center</span>
                <span class="brand-text">FitHub</span>
            </a>
            <button class="navbar-toggle" id="navbarToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="navbar-menu" id="navbarMenu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="members.php" class="active">Members</a></li>
                <li><a href="attendance.php">Attendance</a></li>
                <li><a href="reports.php">Analytics</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Quick Actions Bar -->
            <div class="quick-actions-bar">
                <div class="quick-actions-left">
                    <h2 class="page-section-title">Member Management</h2>
                    <p class="page-section-subtitle">Manage your gym members efficiently</p>
                </div>
                <button type="button" class="btn btn-primary" id="toggleFormBtn" onclick="toggleMemberForm()">
                    <span class="material-symbols-rounded"><?php echo $edit_member ? 'close' : 'person_add'; ?></span>
                    <span><?php echo $edit_member ? 'Cancel' : 'Add New Member'; ?></span>
                </button>
            </div>

            <!-- Add/Edit Member Form -->
            <div class="form-container member-form-wrapper" id="memberFormContainer" style="display: none;">
                <div class="form-header">
                    <div class="form-header-left">
                        <span class="material-symbols-rounded form-header-icon"><?php echo $edit_member ? 'edit' : 'person_add'; ?></span>
                        <h2 class="form-header-title"><?php echo $edit_member ? 'Edit Member' : 'Add New Member'; ?></h2>
                    </div>
                    <button type="button" class="form-close-btn" onclick="toggleMemberForm()">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                
                <form method="POST" class="member-form">
                    <?php if ($edit_member): ?>
                        <input type="hidden" name="member_id" value="<?php echo $edit_member['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo $edit_member ? $edit_member['name'] : ''; ?>" 
                                   placeholder="Enter member's full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo $edit_member ? $edit_member['email'] : ''; ?>" 
                                   placeholder="member@example.com" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo $edit_member ? $edit_member['phone'] : ''; ?>" 
                                   placeholder="10-digit phone number" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="join_date" class="form-label">Join Date *</label>
                            <input type="date" id="join_date" name="join_date" class="form-control" 
                                   value="<?php echo $edit_member ? $edit_member['join_date'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="membership_type" class="form-label">Membership Type *</label>
                            <select id="membership_type" name="membership_type" class="form-control" required>
                                <option value="">Select membership type</option>
                                <option value="Monthly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                <option value="Quarterly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Quarterly') ? 'selected' : ''; ?>>Quarterly</option>
                                <option value="Yearly" <?php echo ($edit_member && $edit_member['membership_type'] == 'Yearly') ? 'selected' : ''; ?>>Yearly</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo (!$edit_member || $edit_member['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_member && $edit_member['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="<?php echo $edit_member ? 'update_member' : 'add_member'; ?>" class="btn btn-primary">
                            <span><?php echo $edit_member ? ' Update Member' : ' Add Member'; ?></span>
                        </button>
                        <?php if ($edit_member): ?>
                            <a href="members.php" class="btn btn-secondary">
                                <span>Cancel</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Members Directory -->
            <div class="members-directory">
                <div class="directory-header">
                    <div class="directory-title-wrapper">
                        <span class="material-symbols-rounded directory-title-icon">groups</span>
                        <div>
                            <h2 class="directory-title">Members Directory</h2>
                            <p class="directory-subtitle">All registered gym members</p>
                        </div>
                    </div>
                    <div class="directory-stats">
                        <span class="directory-stat-badge"><?php echo count($members); ?> Total</span>
                        <?php 
                            $active_count = count(array_filter($members, function($m) { return $m['status'] == 'active'; })); 
                        ?>
                        <span class="directory-stat-badge directory-stat-active"><?php echo $active_count; ?> Active</span>
                    </div>
                </div>
                
                <?php if (count($members) > 0): ?>
                    <div class="members-grid">
                        <?php foreach ($members as $member): ?>
                            <div class="member-card">
                                <div class="member-card-header">
                                    <div class="member-avatar">
                                        <span class="material-symbols-rounded">person</span>
                                    </div>
                                    <div class="member-card-badges">
                                        <span class="badge badge-<?php echo $member['status'] == 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($member['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="member-card-body">
                                    <h3 class="member-card-name"><?php echo htmlspecialchars($member['name']); ?></h3>
                                    <div class="member-card-info">
                                        <div class="member-card-item">
                                            <span class="material-symbols-rounded member-card-icon">mail</span>
                                            <span class="member-card-text"><?php echo htmlspecialchars($member['email']); ?></span>
                                        </div>
                                        <div class="member-card-item">
                                            <span class="material-symbols-rounded member-card-icon">phone</span>
                                            <span class="member-card-text"><?php echo htmlspecialchars($member['phone']); ?></span>
                                        </div>
                                        <div class="member-card-item">
                                            <span class="material-symbols-rounded member-card-icon">calendar_today</span>
                                            <span class="member-card-text">Joined <?php echo date('M j, Y', strtotime($member['join_date'])); ?></span>
                                        </div>
                                        <div class="member-card-item">
                                            <span class="material-symbols-rounded member-card-icon">stars</span>
                                            <span class="member-card-text"><?php echo htmlspecialchars($member['membership_type']); ?> Plan</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="member-card-actions">
                                    <a href="?edit=<?php echo $member['id']; ?>" class="btn btn-sm btn-secondary">
                                        <span class="material-symbols-rounded">edit</span>
                                        <span>Edit</span>
                                    </a>
                                    <form method="POST" style="display: inline;" data-confirm="Are you sure you want to delete this member?" data-member-name="<?php echo htmlspecialchars($member['name']); ?>">
                                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" name="delete_member" class="btn btn-sm btn-danger">
                                            <span class="material-symbols-rounded">delete</span>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <span class="material-symbols-rounded empty-state-icon">group_off</span>
                        <h3 class="empty-state-title">No Members Found</h3>
                        <p class="empty-state-description">Start building your gym community by adding your first member.</p>
                        <button type="button" class="btn btn-primary" onclick="toggleMemberForm()">
                            <span class="material-symbols-rounded">person_add</span>
                            <span>Add First Member</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
    <script>
        // Mobile navigation toggle
        document.getElementById('navbarToggle').addEventListener('click', function() {
            const menu = document.getElementById('navbarMenu');
            menu.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.navbar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('navbarMenu').classList.remove('active');
            });
        });

        // Show toast notifications for PHP messages
        <?php if ($message): ?>
            showToast('<?php echo addslashes($message); ?>', '<?php echo $message_type === 'success' ? 'success' : 'error'; ?>', '<?php echo $message_type === 'success' ? 'Success' : 'Error'; ?>');
        <?php endif; ?>

        // Toggle member form visibility
        function toggleMemberForm() {
            const formContainer = document.getElementById('memberFormContainer');
            const toggleBtn = document.getElementById('toggleFormBtn');
            
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                toggleBtn.innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
                toggleBtn.classList.remove('btn-primary');
                toggleBtn.classList.add('btn-secondary');
            } else {
                // If canceling edit, redirect to clean page
                <?php if ($edit_member): ?>
                    window.location.href = 'members.php';
                <?php else: ?>
                    formContainer.style.display = 'none';
                    toggleBtn.innerHTML = '<span class="material-symbols-rounded">person_add</span><span>Add New Member</span>';
                    toggleBtn.classList.remove('btn-secondary');
                    toggleBtn.classList.add('btn-primary');
                <?php endif; ?>
            }
        }

        // Show form if editing
        <?php if ($edit_member): ?>
            document.getElementById('memberFormContainer').style.display = 'block';
            document.getElementById('toggleFormBtn').innerHTML = '<span class="material-symbols-rounded">close</span><span>Cancel</span>';
            document.getElementById('toggleFormBtn').classList.remove('btn-primary');
            document.getElementById('toggleFormBtn').classList.add('btn-secondary');
        <?php endif; ?>

        // Form validation
        const memberForm = document.querySelector('.member-form');
        if (memberForm) {
            memberForm.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                
                if (!name || !email || !phone) {
                    e.preventDefault();
                    showToast('Please fill in all required fields.', 'error', 'Validation Error');
                    return false;
                }
                
                // Basic email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    showToast('Please enter a valid email address.', 'error', 'Validation Error');
                    return false;
                }
                
                // Basic phone validation
                const phoneRegex = /^\d{10}$/;
                if (!phoneRegex.test(phone.replace(/\D/g, ''))) {
                    e.preventDefault();
                    showToast('Please enter a valid 10-digit phone number.', 'error', 'Validation Error');
                    return false;
                }
            });
        }
    </script>
</body>
</html>