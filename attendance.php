<?php
require_once 'config.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_in'])) {
    $member_id = intval($_POST['member_id']);
    $today = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Check if member already checked in today
    $check_query = $conn->prepare("SELECT id FROM attendance WHERE member_id = ? AND date = ?");
    $check_query->bind_param("is", $member_id, $today);
    $check_query->execute();
    $result = $check_query->get_result();
    
    if ($result->num_rows > 0) {
        $error_message = show_error("Member has already checked in today!");
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (member_id, date, check_in) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $member_id, $today, $current_time);
        
        if ($stmt->execute()) {
            $success_message = show_success("Check-in successful at " . $current_time);
        } else {
            $error_message = show_error("Error during check-in: " . $stmt->error);
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
        $success_message = show_success("Check-out successful at " . $current_time);
    } else {
        $error_message = show_error("Error during check-out: " . $stmt->error);
    }
    $stmt->close();
}

// Get all active members for dropdown
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
                        <a class="nav-link" href="members.php"><i class="bi bi-people"></i> Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="attendance.php"><i class="bi bi-calendar-check"></i> Attendance</a>
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
            <!-- Check-in Form -->
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Member Check-in</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message) echo $success_message; ?>
                        <?php if ($error_message) echo $error_message; ?>
                        
                        <form method="POST" action="attendance.php">
                            <div class="mb-3">
                                <label class="form-label">Select Member *</label>
                                <select class="form-select" name="member_id" id="memberSelect" required>
                                    <option value="">Choose a member...</option>
                                    <?php while ($member = $members_result->fetch_assoc()): ?>
                                        <option value="<?php echo $member['id']; ?>">
                                            <?php echo escape_html($member['name']) . ' (' . escape_html($member['email']) . ')'; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">Only active members are shown</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="text" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Current Time</label>
                                <input type="text" class="form-control" id="currentTime" readonly>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="check_in" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Check In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card shadow mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-graph-up"></i> Today's Stats</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Check-ins:</span>
                            <span class="badge bg-primary"><?php echo $attendance_result->num_rows; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Active Now:</span>
                            <span class="badge bg-success">
                                <?php 
                                $conn->data_seek(0);
                                $active_count = 0;
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
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Today's Attendance</h5>
                        <span class="badge bg-light text-dark"><?php echo date('F d, Y'); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
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
                                                    <small class="text-muted"><?php echo escape_html($attendance['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo escape_html($attendance['membership_type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="time-badge badge bg-success">
                                                        <i class="bi bi-arrow-right-circle"></i> 
                                                        <?php echo date('g:i A', strtotime($attendance['check_in'])); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($attendance['check_out']): ?>
                                                        <span class="time-badge badge bg-danger">
                                                            <i class="bi bi-arrow-left-circle"></i> 
                                                            <?php echo date('g:i A', strtotime($attendance['check_out'])); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">In Gym</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$attendance['check_out']): ?>
                                                        <form method="POST" action="attendance.php" style="display: inline;">
                                                            <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">
                                                            <button type="submit" name="check_out" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-box-arrow-right"></i> Check Out
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">Completed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="bi bi-inbox"></i> No check-ins yet today. Start checking in members!
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
