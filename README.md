# FitHub - Gym Management System

A comprehensive gym management system with role-based access control.

## 📁 Project Structure

```
FitHub/
├── admin/                      # Admin-only pages
│   └── user-management.php    # User account management
├── member/                     # Member-only pages
│   ├── profile.php            # Member profile and settings
│   └── attendance.php         # Personal attendance history
├── staff/                      # Staff/Admin operational pages
│   ├── members.php            # Member management (CRUD)
│   ├── attendance.php         # Attendance tracking
│   └── reports.php            # Analytics & reports
├── includes/                   # Shared components
│   ├── auth_check.php         # Authentication verification
│   ├── role_check.php         # Role-based access control helpers
│   ├── navigation.php         # Dynamic navigation menu
│   └── footer.php             # Footer component
├── assets/                     # Static assets
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript files
│   └── images/                # Images and media
├── sql/                        # Database scripts
│   ├── database.sql           # Main database schema
│   └── user_module.sql        # User authentication tables
├── index.php                   # Dashboard (role-adaptive)
├── login.php                   # Login page
├── logout.php                  # Logout handler
├── access-denied.php           # Access denied error page
├── error.php                   # General error page
├── config.php                  # Database configuration
└── README.md                   # Documentation

```

## 🔐 Role-Based Access Control

### Admin
**Full system access:**
- ✅ User management (create/edit/delete users)
- ✅ Member management (full CRUD)
- ✅ Attendance management (full access)
- ✅ Analytics & reports (full access)
- ✅ System configuration

### Staff
**Operational access:**
- ✅ Member management (add/edit, cannot delete)
- ✅ Attendance tracking (check-in/check-out)
- ✅ Reports (read-only, limited date ranges)
- ❌ User management
- ❌ System configuration

### Member
**Self-service only:**
- ✅ View/edit own profile
- ✅ View personal attendance history
- ✅ View personal statistics
- ❌ Cannot access other members' data
- ❌ Cannot perform check-in/check-out themselves

## 🚀 Setup Instructions

### 1. Database Setup

**Option A: Using MySQL Command Line**
```bash
# Navigate to project directory
cd C:\xampp\htdocs\FitHub

# Create database and import schema
mysql -u root -p
```

```sql
CREATE DATABASE gym_system;
USE gym_system;
SOURCE sql/database.sql;
SOURCE sql/user_module.sql;
EXIT;
```

**Option B: Using phpMyAdmin**
1. Open `http://localhost/phpmyadmin`
2. Create new database: `gym_system`
3. Import `sql/database.sql`
4. Import `sql/user_module.sql`

### 2. Configuration
Update `config.php` with your database credentials:
```php
<?php
$servername = "localhost";
$username = "root";
$password = "";  // Your MySQL password
$dbname = "gym_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
```

### 3. Access the Application
```
http://localhost/FitHub/
or
http://localhost/FitHub/login.php
```

### 4. Default Credentials
```
Admin:  admin / password
Staff:  staff1 / password
Member: member1 / password
```

**⚠️ Important:** Change these passwords immediately in production!

## 📝 Key Features

### Authentication & Authorization
- Secure password hashing (bcrypt)
- Session-based authentication
- Role-based page protection
- Dynamic navigation based on user role

**Code Examples:**

```php
// Login authentication (login.php)
$stmt = $conn->prepare("SELECT id, username, password, full_name, role, status 
                        FROM users WHERE username = ? AND status = 'active'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
    }
}
```

### Member Management
- Add/edit/delete members
- Track membership types (Monthly/Quarterly/Yearly)
- Member status management (active/inactive)
- Email and phone validation

**Code Examples:**

```php
// Add new member (staff/members.php)
$stmt = $conn->prepare("INSERT INTO members (name, email, phone, join_date, membership_type, status) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $email, $phone, $join_date, $membership_type, $status);
$stmt->execute();

// Update member (admin only for full access)
if (isAdmin()) {
    $stmt = $conn->prepare("UPDATE members 
                           SET name=?, email=?, phone=?, membership_type=?, status=? 
                           WHERE id=?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $membership_type, $status, $member_id);
    $stmt->execute();
}

// Delete member (admin only)
if (isAdmin()) {
    $stmt = $conn->prepare("DELETE FROM members WHERE id=?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
}
```

### Attendance Tracking
- Real-time check-in/check-out
- Live session duration tracking
- Today's attendance overview
- Historical attendance records

**Code Examples:**

```php
// Check-in member (staff/attendance.php)
$stmt = $conn->prepare("INSERT INTO attendance (member_id, check_in_time, date) 
                        VALUES (?, NOW(), CURDATE())");
$stmt->bind_param("i", $member_id);
$stmt->execute();

// Check-out member
$stmt = $conn->prepare("UPDATE attendance 
                        SET check_out_time = NOW() 
                        WHERE id = ? AND check_out_time IS NULL");
$stmt->bind_param("i", $attendance_id);
$stmt->execute();

// Get today's attendance
$result = $conn->query("SELECT a.*, m.name, m.membership_type 
                        FROM attendance a 
                        JOIN members m ON a.member_id = m.id 
                        WHERE a.date = CURDATE() 
                        ORDER BY a.check_in_time DESC");

// Calculate session duration
$duration_minutes = (strtotime($check_out_time) - strtotime($check_in_time)) / 60;
$hours = floor($duration_minutes / 60);
$minutes = floor($duration_minutes % 60);
```

### Analytics & Reports
- Date range filtering
- Daily check-in trends
- Top member statistics
- Average daily attendance
- Visual charts and graphs

### User Management (Admin Only)
- Create staff and member accounts
- Assign roles and permissions
- Reset user passwords
- Activate/deactivate accounts

**Code Examples:**

```php
// Create new user (admin/user-management.php)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, phone, role, status) 
                        VALUES (?, ?, ?, ?, ?, ?, 'active')");
$stmt->bind_param("ssssss", $username, $hashed_password, $full_name, $email, $phone, $role);
$stmt->execute();

// Update user
$stmt = $conn->prepare("UPDATE users 
                        SET full_name=?, email=?, phone=?, role=?, status=? 
                        WHERE id=?");
$stmt->bind_param("sssssi", $full_name, $email, $phone, $role, $status, $user_id);
$stmt->execute();

// Reset password
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $new_password_hash, $user_id);
$stmt->execute();

// Delete user (prevent self-deletion)
if ($user_id != getCurrentUserId()) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
```

## 🔧 Technical Details

### Security Features
- Prepared statements (SQL injection protection)
- Password hashing with `password_hash()`
- Session management
- Role-based access control (RBAC)
- XSS protection with `htmlspecialchars()`

**Security Implementation Examples:**

```php
// SQL Injection Protection - Always use prepared statements
$stmt = $conn->prepare("SELECT * FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Password Hashing
$hashed = password_hash($password, PASSWORD_DEFAULT);
$verified = password_verify($input_password, $stored_hash);

// XSS Protection
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// Session Security
session_start();
session_regenerate_id(true); // Prevent session fixation
```

### Database Schema

**Users Table:**
```sql
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin','staff','member') DEFAULT 'member',
    status ENUM('active','inactive') DEFAULT 'active',
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);
```

**Members Table:**
```sql
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) NOT NULL,
    join_date DATE NOT NULL,
    membership_type ENUM('Monthly','Quarterly','Yearly') NOT NULL,
    status ENUM('active','inactive','suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**Attendance Table:**
```sql
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    member_id INT NOT NULL,
    date DATE NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME NULL,
    duration_minutes INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member_date (member_id, date)
);
```

### Helper Functions
Located in `includes/role_check.php`:

```php
// Check user roles
isAdmin()           // Returns true if user is admin
isStaff()           // Returns true if user is admin or staff
isMember()          // Returns true if user is member

// Enforce role requirements (redirects if unauthorized)
requireRole('admin')              // Admin only
requireRole(['admin', 'staff'])   // Admin or Staff

// Get current user information
getCurrentUserId()        // Returns user ID
getCurrentUserName()      // Returns full name or username
getCurrentUserRole()      // Returns role (admin/staff/member)
getRoleDisplayName()      // Returns formatted role name
```

**Usage Examples:**

```php
// Protect a page - admin only
requireRole('admin');

// Protect a page - admin or staff
requireRole(['admin', 'staff']);

// Conditional display based on role
<?php if (isAdmin()): ?>
    <button>Delete User</button>
<?php endif; ?>

// Get user info
$userId = getCurrentUserId();
$userName = getCurrentUserName();
$userRole = getCurrentUserRole();
```

## 📱 Pages & Access Control

| Page | Admin | Staff | Member | Path |
|------|-------|-------|--------|------|
| Dashboard | ✅ Full | ✅ Full | ✅ Personalized | `/index.php` |
| Members | ✅ Full CRUD | ✅ Add/Edit only | ❌ | `/staff/members.php` |
| Attendance | ✅ Full access | ✅ Check-in/out | ❌ | `/staff/attendance.php` |
| Reports | ✅ Full access | ✅ Read-only | ❌ | `/staff/reports.php` |
| My Profile | ✅ | ✅ | ✅ View/Edit own | `/member/profile.php` |
| My Attendance | ✅ | ✅ | ✅ View own | `/member/attendance.php` |
| User Management | ✅ | ❌ | ❌ | `/admin/user-management.php` |

## 🎨 Design Features

- Modern, responsive UI
- Dark theme with glassmorphism effects
- Material Symbols icons
- Smooth animations and transitions
- Mobile-friendly navigation
- Real-time updates
- Interactive charts (Chart.js)

## 🔄 Future Enhancements

- [ ] Payment tracking and invoicing
- [ ] Membership expiry notifications
- [ ] Email notifications
- [ ] Workout plan management
- [ ] Trainer assignment system
- [ ] Equipment management
- [ ] QR code check-in
- [ ] Mobile app integration
- [ ] Advanced reporting (PDF exports)
- [ ] Multi-gym support

## 📄 License

This project is part of a gym management system demonstration.

## 👨‍💻 Development

Built with:
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- JavaScript (ES6+)
- Chart.js for analytics

### Common Development Tasks

**Creating a New Protected Page:**
```php
<?php
session_start();
require_once '../config.php';
require_once '../includes/role_check.php';

// Check authentication
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../login.php');
    exit;
}

// Require specific role
requireRole(['admin', 'staff']); // or requireRole('admin')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Page Title - FitHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navigation.php'; ?>
    
    <main class="main-content">
        <!-- Your content here -->
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
```

**Adding Custom SQL Queries:**
```php
// Get members with attendance count
$query = "SELECT m.*, COUNT(a.id) as visit_count 
          FROM members m 
          LEFT JOIN attendance a ON m.id = a.member_id 
          GROUP BY m.id 
          ORDER BY visit_count DESC";
$result = $conn->query($query);

// Get monthly revenue (if payment system implemented)
$query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, 
          SUM(amount) as total 
          FROM payments 
          GROUP BY month 
          ORDER BY month DESC";

// Get member activity report
$query = "SELECT m.name, 
          COUNT(a.id) as visits,
          AVG(TIMESTAMPDIFF(MINUTE, a.check_in_time, a.check_out_time)) as avg_duration
          FROM members m
          LEFT JOIN attendance a ON m.id = a.member_id
          WHERE a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
          GROUP BY m.id";
```

**JavaScript Integration:**
```javascript
// Auto-refresh live data
setInterval(() => {
    fetch('/FitHub/api/get-live-attendance.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('live-count').textContent = data.count;
        });
}, 30000); // Refresh every 30 seconds

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Please enter a valid email address');
    }
});
```

### API Endpoints (Optional Extension)

**Create REST API endpoints:**
```php
// api/get-stats.php
<?php
session_start();
require_once '../config.php';
require_once '../includes/role_check.php';

if (!isStaff()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stats = [
    'total_members' => $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'],
    'active_members' => $conn->query("SELECT COUNT(*) as count FROM members WHERE status='active'")->fetch_assoc()['count'],
    'today_checkins' => $conn->query("SELECT COUNT(*) as count FROM attendance WHERE date=CURDATE()")->fetch_assoc()['count']
];

header('Content-Type: application/json');
echo json_encode($stats);
?>
```

### Debugging Tips

```php
// Enable error reporting in development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug queries
echo "<pre>";
print_r($result->fetch_assoc());
echo "</pre>";

// Check session variables
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Log errors to file
error_log("Error message: " . $error, 3, "errors.log");
```

---