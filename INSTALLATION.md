# 🚀 Installation Guide - Simple Gym Management System

This guide will walk you through setting up the Gym Management System on your local machine.

## 📋 Prerequisites

Before you begin, make sure you have:
- **XAMPP** (Windows/Mac) or **LAMP** (Linux) or **WAMP** (Windows)
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- A web browser (Chrome, Firefox, Safari, etc.)

## 🔧 Step-by-Step Installation

### Step 1: Download/Install XAMPP

1. **Download XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Install XAMPP** following the installation wizard
3. **Default installation paths:**
   - Windows: `C:\xampp`
   - Mac: `/Applications/XAMPP`
   - Linux: `/opt/lampp`

### Step 2: Copy Project Files

1. **Locate your htdocs folder:**
   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

2. **Create project folder:**
   ```bash
   mkdir htdocs/gym-system
   ```

3. **Copy all project files** to the `gym-system` folder:
   - config.php
   - index.php
   - members.php
   - attendance.php
   - reports.php
   - database.sql

### Step 3: Start XAMPP Services

1. **Open XAMPP Control Panel**
2. **Start these services:**
   - Apache (web server)
   - MySQL (database)
3. **Verify status:** Both should show "Running" in green

### Step 4: Create Database

**Option A: Using phpMyAdmin (Recommended for beginners)**

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** in the left sidebar
3. Create a database named: `gym_system`
4. Click on **"Import"** tab
5. Choose the `database.sql` file from the project
6. Click **"Go"** to import

**Option B: Using SQL Command**

1. Open `http://localhost/phpmyadmin`
2. Click on **"SQL"** tab
3. Copy and paste the contents of `database.sql`
4. Click **"Go"**

### Step 5: Configure Database Connection (If Needed)

The default configuration should work for most XAMPP installations. However, if you need to change it:

1. Open `config.php` in a text editor
2. Modify these lines if needed:
   ```php
   define('DB_HOST', 'localhost');  // Usually 'localhost'
   define('DB_NAME', 'gym_system'); // Database name
   define('DB_USER', 'root');       // Default XAMPP username
   define('DB_PASS', '');           // Default XAMPP password (empty)
   ```

### Step 6: Access the Application

1. **Open your web browser**
2. **Navigate to:** `http://localhost/gym-system/`
3. **You should see the homepage!** 🎉

## ✅ Verify Installation

### Check if everything works:

1. **Homepage**: `http://localhost/gym-system/`
   - Should display dashboard with statistics

2. **Members Page**: `http://localhost/gym-system/members.php`
   - Should show sample members from database
   - Try adding a new member

3. **Attendance Page**: `http://localhost/gym-system/attendance.php`
   - Should show check-in form
   - Try checking in a member

4. **Reports Page**: `http://localhost/gym-system/reports.php`
   - Should display attendance statistics

## 🐛 Troubleshooting

### Issue 1: "Can't connect to MySQL"

**Solution:**
- Make sure MySQL is running in XAMPP Control Panel
- Check database credentials in `config.php`
- Verify database `gym_system` exists in phpMyAdmin

### Issue 2: "404 Not Found"

**Solution:**
- Verify Apache is running in XAMPP
- Check the URL: `http://localhost/gym-system/` (not `/gym_system/`)
- Ensure files are in the correct htdocs folder

### Issue 3: Blank Page or PHP Errors

**Solution:**
- Enable error display by adding this to the top of `config.php`:
  ```php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ```
- Check Apache error logs in XAMPP

### Issue 4: Database Tables Not Created

**Solution:**
- Re-import `database.sql` in phpMyAdmin
- Check SQL import log for errors
- Manually create database first, then import

### Issue 5: Bootstrap Not Loading (Page looks unstyled)

**Solution:**
- Check your internet connection (Bootstrap loads from CDN)
- Try refreshing the page (Ctrl+F5 or Cmd+Shift+R)

## 📱 Testing the System

After installation, test these features:

### 1. Add a Member
- Go to Members page
- Fill in the form:
  - Name: Test User
  - Email: test@example.com
  - Phone: 1234567890
  - Membership: Monthly
- Click "Add Member"
- Verify member appears in the list

### 2. Check-in a Member
- Go to Attendance page
- Select a member from dropdown
- Click "Check In"
- Verify check-in appears in today's list

### 3. Check-out a Member
- Find the checked-in member
- Click "Check Out" button
- Verify check-out time is recorded

### 4. View Reports
- Go to Reports page
- Verify statistics are displayed
- Try changing date filters

## 🔐 Security Notes

**Important:** This is a development system. For production use:

1. **Change database password:**
   ```sql
   SET PASSWORD FOR 'root'@'localhost' = PASSWORD('your_password');
   ```
   Update `config.php` with new password

2. **Disable error display:**
   ```php
   ini_set('display_errors', 0);
   ```

3. **Add input sanitization** for all user inputs

4. **Use HTTPS** instead of HTTP

5. **Add user authentication** to restrict access

## 📞 Need Help?

If you encounter any issues:

1. **Check XAMPP logs:**
   - Apache log: `xampp/apache/logs/error.log`
   - MySQL log: `xampp/mysql/data/mysql_error.log`

2. **Common resources:**
   - [PHP Documentation](https://www.php.net/docs.php)
   - [MySQL Documentation](https://dev.mysql.com/doc/)
   - [Bootstrap Documentation](https://getbootstrap.com/docs/)

3. **Verify PHP version:**
   ```php
   <?php phpinfo(); ?>
   ```

## ✨ Next Steps

After successful installation:

1. **Explore all features** to understand the system
2. **Add more members** for better testing
3. **Check in members** to see attendance tracking
4. **Review the code** to learn PHP and MySQL
5. **Customize as needed** for your requirements

## 🎓 Learning Resources

- **PHP Basics:** [W3Schools PHP Tutorial](https://www.w3schools.com/php/)
- **MySQL:** [W3Schools SQL Tutorial](https://www.w3schools.com/sql/)
- **Bootstrap:** [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)

---

**Congratulations! 🎉** You've successfully installed the Simple Gym Management System!

Start by exploring the Members page and adding your first gym member.
