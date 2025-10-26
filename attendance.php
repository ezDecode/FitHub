<?php
require_once 'config.php';

$success_message = '';
$error_message = '';

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_in'])) {
    $member_id = intval($_POST['member_id']);
    $today = date('Y-m-d');
    $current_time = date('H:i:s');
    
    $check_query = $conn->prepare("SELECT id FROM attendance WHERE member_id = ? AND date = ?");
    $check_query->bind_param("is", $member_id, $today);
    $check_query->execute();
    $result = $check_query->get_result();
    
    if ($result->num_rows > 0) {
        $error_message = "Member has already checked in today!";
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, date, check_in) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $member_id, $today, $current_time);
        
        if ($stmt->execute()) {
            $success_message = "Check-in successful at " . $current_time;
        } else {
            $error_message = "Error during check-in";
        }
        $stmt->close();
    }
    $check_query->close();
}

// Handle check-out
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_out'])) {
    $attendance_id = intval($_POST['attendance_id']);
    $current_time = date('H:i:s');
    
    $stmt = $conn->prepare("UPDATE attendance SET check_out = ? WHERE id = ?");
    $stmt->bind_param("si", $current_time, $attendance_id);
    
    if ($stmt->execute()) {
        $success_message = "Check-out successful at " . $current_time;
    } else {
        $error_message = "Error during check-out";
    }
    $stmt->close();
}

// Get active members
$members_query = "SELECT id, name, email FROM members WHERE status = 'active' ORDER BY name ASC";
$members_result = $conn->query($members_query);

// Get today's attendance
$today = date('Y-m-d');
$attendance_query = "
    SELECT a.id, a.check_in, a.check_out, m.name, m.email, m.membership_type 
    FROM attendance a
    JOIN members m ON a.member_id = m.id
    WHERE a.date = '$today'
    ORDER BY a.check_in DESC
";
$attendance_result = $conn->query($attendance_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Gym System</title>
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
                <li><a href="members.php">👥 Members</a></li>
                <li><a href="attendance.php" class="active">📅 Attendance</a></li>
                <li><a href="reports.php">📊 Reports</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Check-in Form -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header">⏰ Member Check-in</div>
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
                        
                        <form method="POST" action="attendance.php">
                            <div class="form-group">
                                <label class="form-label">Select Member *</label>
                                <select class="form-select" name="member_id" required>
                                    <option value="">Choose a member...</option>
                                    <?php while ($member = $members_result->fetch_assoc()): ?>
                                        <option value="<?php echo $member['id']; ?>">
                                            <?php echo escape_html($member['name']) . ' (' . escape_html($member['email']) . ')'; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small style="color: #666;">Only active members are shown</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Date</label>
                                <input type="text" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Current Time</label>
                                <input type="text" class="form-control" id="currentTime" readonly>
                            </div>
                            
                            <button type="submit" name="check_in" class="btn btn-success btn-block btn-lg">
                                ✅ Check In
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-2">
                    <div class="card-header info">📊 Today's Stats</div>
                    <div class="card-body">
                        <div class="d-flex justify-between mb-1">
                            <span>Total Check-ins:</span>
                            <span class="badge badge-primary"><?php echo $attendance_result->num_rows; ?></span>
                        </div>
                        <div class="d-flex justify-between">
                            <span>Active Now:</span>
                            <span class="badge badge-success">
                                <?php 
                                $active_count = 0;
                                $attendance_result->data_seek(0);
                                while ($row = $attendance_result->fetch_assoc()) {
                                    if (empty($row['check_out'])) $active_count++;
                                }
                                echo $active_count;
                                $attendance_result->data_seek(0);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Attendance List -->
            <div class="col-8">
                <div class="card">
                    <div class="card-header success">
                        📋 Today's Attendance (<?php echo date('F d, Y'); ?>)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Membership</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($attendance_result->num_rows > 0): ?>
                                        <?php while ($attendance = $attendance_result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo escape_html($attendance['name']); ?></strong><br>
                                                    <small style="color: #666;"><?php echo escape_html($attendance['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo escape_html($attendance['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        ➡️ <?php echo date('g:i A', strtotime($attendance['check_in'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($attendance['check_out']): ?>
                                                        <span class="badge badge-danger">
                                                            ⬅️ <?php echo date('g:i A', strtotime($attendance['check_out'])); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">In Gym</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$attendance['check_out']): ?>
                                                        <form method="POST" action="attendance.php" style="display: inline;">
                                                            <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">
                                                            <button type="submit" name="check_out" class="btn btn-danger btn-sm">
                                                                ⬅️ Check Out
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span style="color: #999;">Completed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No check-ins yet today. Start checking in members!</td>
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
