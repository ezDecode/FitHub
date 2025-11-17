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
```sql
-- Run these SQL files in order:
1. sql/database.sql       # Creates gym_system database
2. sql/user_module.sql    # Creates users table and demo accounts
```

### 2. Configuration
Update `config.php` with your database credentials:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_system";
```

### 3. Default Credentials
```
Admin:  admin / password
Staff:  staff1 / password
Member: member1 / password
```

## 📝 Key Features

### Authentication & Authorization
- Secure password hashing (bcrypt)
- Session-based authentication
- Role-based page protection
- Dynamic navigation based on user role

### Member Management
- Add/edit/delete members
- Track membership types (Monthly/Quarterly/Yearly)
- Member status management (active/inactive)
- Email and phone validation

### Attendance Tracking
- Real-time check-in/check-out
- Live session duration tracking
- Today's attendance overview
- Historical attendance records

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

## 🔧 Technical Details

### Security Features
- Prepared statements (SQL injection protection)
- Password hashing with `password_hash()`
- Session management
- Role-based access control (RBAC)
- XSS protection with `htmlspecialchars()`

### Database Schema
- `users` - User accounts and authentication
- `members` - Member profiles and membership data
- `attendance` - Check-in/check-out records
- Foreign key relationships with cascading deletes

### Helper Functions
Located in `includes/role_check.php`:
- `isAdmin()` - Check if user is admin
- `isStaff()` - Check if user is admin or staff
- `isMember()` - Check if user is member
- `requireRole($role)` - Enforce role requirement
- `getCurrentUserId()` - Get logged-in user ID
- `getCurrentUserName()` - Get logged-in user name
- `getRoleDisplayName()` - Get formatted role name

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

---

**Last Updated:** November 17, 2025
