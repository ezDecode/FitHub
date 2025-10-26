# 🏋️ Simple Gym Management System

**A Beginner-Friendly Gym Management System**

This is a simplified gym management system designed for beginners to learn PHP, MySQL, and Bootstrap. It includes basic member management and attendance tracking.

## 🎯 What This System Does

- **Add Members**: Register new gym members with basic information
- **List Members**: View all registered members
- **Edit Members**: Update member information
- **Check-in System**: Simple attendance tracking
- **Basic Reports**: View attendance statistics

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, Basic JavaScript
- **Backend**: PHP 7.4+, MySQL 5.7+, MySQLi
- **Server**: XAMPP/WAMP/LAMP

## 📋 Features

### Phase 1: Basic Setup (Week 1)
- ✅ Database setup (2 simple tables)
- ✅ Basic configuration
- ✅ Homepage with navigation

### Phase 2: Member Management (Week 2)
- ✅ List all members
- ✅ Add new members
- ✅ Edit member information
- ✅ Delete members

### Phase 3: Attendance System (Week 3)
- ✅ Check-in system
- ✅ View today's attendance
- ✅ Basic attendance reports

## 🗄️ Database Structure

### Members Table
```sql
- id (Primary Key)
- name
- email
- phone
- join_date
- membership_type
- status
```

### Attendance Table
```sql
- id (Primary Key)
- member_id (Foreign Key)
- date
- check_in
- check_out
```

## 🚀 Quick Start

### Prerequisites
- XAMPP, WAMP, or LAMP server
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Download/Clone the project**
   ```bash
   # Place files in your web server directory
   # For XAMPP: C:\xampp\htdocs\gym-system\
   # For WAMP: C:\wamp64\www\gym-system\
   ```

2. **Start your server**
   - Start Apache and MySQL in XAMPP/WAMP

3. **Create Database**
   ```sql
   CREATE DATABASE gym_system;
   ```

4. **Import Database Schema**
   - Run the SQL file to create tables

5. **Configure Database Connection**
   - Update `config.php` with your database credentials

6. **Access the System**
   ```
   http://localhost/gym-system/
   ```

## 📁 Project Structure

```
gym-system/
├── index.php          # Homepage
├── members.php        # Member management
├── attendance.php     # Check-in system
├── reports.php        # Basic reports
├── config.php         # Database connection
├── functions.php      # Helper functions
├── prd.json          # Project requirements
├── tasks.json        # Task tracker
└── README.md         # This file
```

## 🎓 Learning Objectives

This project teaches:

- **PHP Basics**: Variables, functions, arrays, loops
- **MySQL**: Database design, queries, CRUD operations
- **Bootstrap**: Responsive design, components, forms
- **Web Development**: HTML structure, CSS styling
- **Project Management**: Task tracking, documentation

## 📚 What You'll Learn

### Week 1: Foundation
- Setting up development environment
- Creating database tables
- Basic PHP configuration
- Bootstrap layout

### Week 2: Member Management
- PHP forms and validation
- MySQL INSERT, SELECT, UPDATE, DELETE
- Bootstrap tables and forms
- Basic error handling

### Week 3: Attendance System
- Date/time handling in PHP
- Database relationships
- Simple reporting
- User interface design

## 🔧 Development Tips

### For Beginners
1. **Start Small**: Focus on one feature at a time
2. **Test Often**: Check your work after each step
3. **Read Errors**: PHP error messages help you learn
4. **Use Bootstrap**: Don't worry about custom CSS initially
5. **Ask Questions**: Use online resources and communities

### Code Organization
- Keep PHP logic separate from HTML
- Use meaningful variable names
- Comment your code
- Test with sample data

## 🐛 Common Issues

### Database Connection
```php
// Check your database credentials in config.php
$host = 'localhost';
$dbname = 'gym_system';
$username = 'root';
$password = '';
```

### File Permissions
- Make sure your web server can read PHP files
- Check that MySQL is running

### Bootstrap Not Loading
- Verify Bootstrap CDN link in HTML
- Check internet connection

## 📈 Next Steps

After completing this project, you can:

1. **Add Authentication**: Simple login system
2. **Improve UI**: Custom CSS styling
3. **Add Features**: Payment tracking, member photos
4. **Learn Advanced PHP**: OOP, MVC patterns
5. **Explore Frameworks**: Laravel, CodeIgniter

## 🤝 Getting Help

- **PHP Documentation**: https://www.php.net/docs.php
- **Bootstrap Documentation**: https://getbootstrap.com/docs/
- **MySQL Tutorial**: https://www.w3schools.com/sql/
- **Stack Overflow**: For specific coding questions

## 📝 Project Timeline

- **Week 1**: Basic setup and database
- **Week 2**: Member management features
- **Week 3**: Attendance system and reports

**Total Duration**: 3 weeks
**Difficulty Level**: Beginner
**Prerequisites**: Basic HTML/CSS knowledge

---

**Happy Coding! 🚀**

*This project is designed to be educational and beginner-friendly. Focus on learning the concepts rather than building a production system.*
