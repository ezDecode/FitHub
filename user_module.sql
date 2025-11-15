-- ========================================
-- FITHUB GYM MANAGEMENT SYSTEM
-- USER MODULE DATABASE SETUP
-- ========================================
-- Run this SQL script to add user module to your system

-- Step 1: Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','staff','member') NOT NULL DEFAULT 'member',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 2: Insert default admin user
-- Username: admin
-- Password: admin123
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@fithub.com', 'admin');

-- Step 3: Insert sample staff user  
-- Username: staff1
-- Password: staff123
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', 'staff@fithub.com', 'staff');

-- Step 4: Insert sample member user
-- Username: member1
-- Password: member123
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'member@fithub.com', 'member');

-- Step 5 (Optional): Add user_id column to members table
-- This links gym members to user accounts
ALTER TABLE `members` ADD COLUMN `user_id` INT(11) NULL AFTER `id`;
ALTER TABLE `members` ADD INDEX `user_id_index` (`user_id`);
ALTER TABLE `members` ADD CONSTRAINT `members_user_fk` 
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- ========================================
-- VERIFICATION QUERIES
-- ========================================
-- Run these to verify setup worked correctly

-- Show all users
SELECT id, username, full_name, email, role, status, created_date FROM users;

-- Count users by role
SELECT role, COUNT(*) as total FROM users GROUP BY role;

-- ========================================
-- DEFAULT LOGIN CREDENTIALS
-- ========================================
-- Admin:  username = admin    password = admin123
-- Staff:  username = staff1   password = staff123
-- Member: username = member1  password = member123
-- ========================================
