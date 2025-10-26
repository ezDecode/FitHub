# FitHub Gym Management System

**Version:** 1.0.0  
**Status:** Phase 1 Complete ✅

A comprehensive web-based gym management system to handle member registrations, membership plans, attendance tracking, trainer management, payment processing, and administrative operations.

## 🚀 Technology Stack

### Frontend
- HTML5
- CSS3 (Custom Design System)
- JavaScript (ES6+)
- Chart.js
- SweetAlert2
- Flatpickr
- Font Awesome
- jsPDF

### Backend
- PHP 7.4+
- MySQL 8.0+
- PDO (PHP Data Objects)

### Server
- Apache/Nginx with PHP support

## 🎨 Design System

### Color Palette
- **Primary:** #FF6B35 (Orange)
- **Secondary:** #004E89 (Blue)
- **Accent:** #F7931E (Light Orange)
- **Success:** #2ECC71 (Green)
- **Warning:** #F39C12 (Yellow)
- **Danger:** #E74C3C (Red)
- **Dark:** #1A1A2E (Almost Black)
- **Light Gray:** #F5F5F5
- **Medium Gray:** #95A5A6

### Typography
- **Font Family:** Inter Tight
- **Weights:** 300, 400, 500, 600, 700

## 📦 Project Structure

```
fithub-gym/
├── api/                        # API endpoints
│   ├── login.php
│   └── logout.php
├── assets/                     # Static assets
│   ├── css/
│   │   ├── style.css          # Main stylesheet
│   │   └── dashboard.css      # Dashboard layout
│   └── js/
│       └── main.js            # Main JavaScript utilities
├── config/                     # Configuration files
│   ├── config.php             # App configuration
│   └── database.php           # Database connection
├── database/                   # Database files
│   └── fithub_schema.sql      # Complete database schema
├── includes/                   # PHP includes
│   ├── auth.php               # Authentication logic
│   ├── functions.php          # Helper functions
│   └── session.php            # Session management
├── uploads/                    # File uploads
│   ├── members/
│   └── trainers/
├── logs/                       # Application logs
├── .env.example               # Environment config template
├── index.php                  # Login page
├── prd.json                   # Product requirements
├── tasks.json                 # Project tasks tracker
└── README.md                  # This file
```

## 🗄️ Database Schema

The system includes 10 comprehensive tables:

1. **users** - Central authentication and role management
2. **members** - Member profile information
3. **trainers** - Trainer profile information
4. **membership_plans** - Available membership plans
5. **memberships** - Member subscriptions
6. **payments** - Payment transactions
7. **attendance** - Check-in/check-out records
8. **workout_plans** - Personalized workout plans (JSON)
9. **trainer_assignments** - Trainer-member assignments

### Additional Features:
- Views for common queries
- Stored procedures for complex operations
- Triggers for data integrity
- Comprehensive indexes for performance
- Sample data for testing

## 🔐 User Roles & Permissions

### Admin
- Full system access
- Manage members, trainers, plans
- Process payments
- View analytics
- System configuration

### Trainer
- View assigned members
- Mark attendance
- Create/update workout plans
- View schedule

### Member
- View membership status
- Check attendance history
- View payment history
- View workout plans
- Update profile

## 🚦 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional)

### Step 1: Database Setup
```bash
# Import the database schema
mysql -u root -p < database/fithub_schema.sql
```

### Step 2: Configuration
```bash
# Copy environment config
cp .env.example .env

# Edit .env with your database credentials
nano .env
```

### Step 3: Permissions
```bash
# Set proper permissions
chmod -R 755 assets/
chmod -R 755 uploads/
chmod -R 755 logs/
```

### Step 4: Access the Application
```
http://localhost/fithub-gym/
```

## 🔑 Default Login Credentials

### Administrator
- **Email:** admin@fithub.com
- **Password:** Admin@123

### Trainer
- **Email:** john.trainer@fithub.com
- **Password:** Trainer@123

### Member
- **Email:** alice.member@email.com
- **Password:** Member@123

## 📋 Completed Features (Phase 1) ✅

### ✅ Database Setup (DB-001)
- [x] Complete MySQL schema with 10 tables
- [x] Foreign keys and relationships
- [x] Indexes for performance
- [x] Views and stored procedures
- [x] Sample test data

### ✅ Configuration Setup (CFG-001)
- [x] Database connection with PDO
- [x] Application configuration
- [x] Environment-based settings
- [x] .env template file

### ✅ Authentication System (AUTH-001)
- [x] Session management
- [x] Login/logout functionality
- [x] Password hashing (bcrypt)
- [x] Role-based redirects
- [x] Session timeout (30 min)
- [x] CSRF token protection

### ✅ UI Framework & Design System (UI-001)
- [x] CSS variables for colors
- [x] Inter Tight font integration
- [x] CSS reset and utilities
- [x] Responsive navigation sidebar
- [x] Card components
- [x] Button components
- [x] Form components
- [x] Responsive grid system
- [x] Mobile hamburger menu
- [x] Responsive breakpoints

### ✅ Helper Functions & Utilities (HELPER-001)
- [x] PHP utility functions
- [x] Input sanitization
- [x] Validation helpers
- [x] JavaScript utilities
- [x] AJAX functions

## 🎯 Next Steps (Phase 2)

- [ ] Admin Dashboard with KPIs
- [ ] Member Management (CRUD)
- [ ] Membership Plans Management
- [ ] Payment Processing
- [ ] Attendance Tracking

## 🔒 Security Features

- **Password Hashing:** bcrypt with cost factor 10
- **SQL Injection Prevention:** Prepared statements (PDO)
- **XSS Prevention:** htmlspecialchars() on all outputs
- **CSRF Protection:** Token-based form protection
- **Session Security:** 30-minute timeout, secure cookies
- **Access Control:** Role-based on all pages
- **File Upload Validation:** Type and size validation

## 📱 Responsive Design

- **Mobile:** 320px - 767px
- **Tablet:** 768px - 1023px
- **Desktop:** 1024px+

## 🛠️ Development Guidelines

### Code Style
- Follow PSR-2 coding standards for PHP
- Use ES6+ features for JavaScript
- Keep functions small and focused
- Comment complex logic

### Security
- Always sanitize user input
- Use prepared statements for SQL
- Validate on both client and server side
- Keep dependencies updated

### Testing
- Test all user workflows
- Verify role-based access control
- Test on multiple devices/browsers
- Validate all forms

## 📝 API Endpoints

### Authentication
- `POST /api/login.php` - User login
- `GET /api/logout.php` - User logout

## 📊 Database Backup

```bash
# Backup database
mysqldump -u root -p fithub_gym > backup_$(date +%Y%m%d).sql
```

## 🐛 Troubleshooting

### Database Connection Issues
1. Check database credentials in .env
2. Verify MySQL service is running
3. Check database exists: `SHOW DATABASES;`

### Permission Issues
```bash
chmod -R 755 uploads/
chmod -R 755 logs/
```

### Session Issues
- Clear browser cookies
- Check session.save_path in php.ini
- Verify session directory permissions

## 📞 Support

For issues or questions, please check the documentation or contact the development team.

## 📄 License

Copyright © 2024 FitHub Gym Management System. All rights reserved.

## 🙏 Acknowledgments

- Font Awesome for icons
- Google Fonts for Inter Tight font
- Chart.js for data visualization
- SweetAlert2 for beautiful alerts
- Flatpickr for date picker

---

**Last Updated:** October 26, 2024  
**Phase:** 1 of 7 Complete (14%)  
**Estimated Completion:** 9 weeks remaining
