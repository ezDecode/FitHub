-- =====================================================
-- FitHub Gym Management System - Database Schema
-- Version: 1.0.0
-- Database: MySQL 8.0+
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS fithub_gym;
USE fithub_gym;

-- =====================================================
-- TABLE: users
-- Purpose: Central authentication and role management
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password',
    role ENUM('admin', 'trainer', 'member') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: members
-- Purpose: Store member profile information
-- =====================================================
CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    address TEXT,
    emergency_contact VARCHAR(255) NOT NULL,
    emergency_phone VARCHAR(20) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    joined_date DATE NOT NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_phone (phone),
    INDEX idx_full_name (full_name),
    INDEX idx_joined_date (joined_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: trainers
-- Purpose: Store trainer profile information
-- =====================================================
CREATE TABLE IF NOT EXISTS trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    specialization VARCHAR(255) NOT NULL COMMENT 'e.g., Strength, Cardio, Yoga, CrossFit',
    experience_years INT NOT NULL DEFAULT 0,
    photo VARCHAR(255) DEFAULT NULL,
    hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_specialization (specialization)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: membership_plans
-- Purpose: Define available membership plans
-- =====================================================
CREATE TABLE IF NOT EXISTS membership_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    duration_months INT NOT NULL COMMENT 'Duration in months',
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    features JSON COMMENT 'Array of features as JSON',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_plan_name (plan_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: memberships
-- Purpose: Track member subscriptions to plans
-- =====================================================
CREATE TABLE IF NOT EXISTS memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE RESTRICT,
    INDEX idx_member_id (member_id),
    INDEX idx_plan_id (plan_id),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: payments
-- Purpose: Record all payment transactions
-- =====================================================
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    membership_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'card', 'upi', 'online') NOT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    status ENUM('completed', 'pending', 'failed') NOT NULL DEFAULT 'completed',
    receipt_number VARCHAR(50) UNIQUE COMMENT 'Format: REC-YYYYMMDD-XXX',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (membership_id) REFERENCES memberships(id) ON DELETE CASCADE,
    INDEX idx_membership_id (membership_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_method (payment_method),
    INDEX idx_status (status),
    INDEX idx_receipt_number (receipt_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: attendance
-- Purpose: Track member check-ins and check-outs
-- =====================================================
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    check_in_time DATETIME NOT NULL,
    check_out_time DATETIME DEFAULT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_date (date),
    INDEX idx_check_in_time (check_in_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: workout_plans
-- Purpose: Store member workout plans created by trainers
-- =====================================================
CREATE TABLE IF NOT EXISTS workout_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    plan_details JSON NOT NULL COMMENT 'JSON structure with exercises, sets, reps, etc.',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE RESTRICT,
    INDEX idx_member_id (member_id),
    INDEX idx_trainer_id (trainer_id),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: trainer_assignments
-- Purpose: Track which trainers are assigned to which members
-- =====================================================
CREATE TABLE IF NOT EXISTS trainer_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    trainer_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    status ENUM('active', 'completed') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE CASCADE,
    INDEX idx_member_id (member_id),
    INDEX idx_trainer_id (trainer_id),
    INDEX idx_status (status),
    INDEX idx_assigned_date (assigned_date),
    UNIQUE KEY unique_active_assignment (member_id, trainer_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert Admin User (password: Admin@123)
INSERT INTO users (email, password, role) VALUES 
('admin@fithub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Trainer Users (password: Trainer@123)
INSERT INTO users (email, password, role) VALUES 
('john.trainer@fithub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer'),
('sarah.trainer@fithub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer');

-- Insert Sample Member Users (password: Member@123)
INSERT INTO users (email, password, role) VALUES 
('alice.member@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member'),
('bob.member@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member'),
('carol.member@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member'),
('david.member@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member');

-- Insert Trainers
INSERT INTO trainers (user_id, full_name, phone, specialization, experience_years, hourly_rate, status) VALUES 
(2, 'John Smith', '9876543210', 'Strength Training & CrossFit', 5, 50.00, 'active'),
(3, 'Sarah Williams', '9876543211', 'Yoga & Cardio', 3, 40.00, 'active');

-- Insert Members
INSERT INTO members (user_id, full_name, phone, date_of_birth, gender, address, emergency_contact, emergency_phone, joined_date, status) VALUES 
(4, 'Alice Johnson', '9876543220', '1995-03-15', 'female', '123 Main Street, City', 'Bob Johnson', '9876543230', '2024-01-10', 'active'),
(5, 'Bob Martinez', '9876543221', '1988-07-22', 'male', '456 Oak Avenue, City', 'Maria Martinez', '9876543231', '2024-01-15', 'active'),
(6, 'Carol Davis', '9876543222', '1992-11-08', 'female', '789 Pine Road, City', 'John Davis', '9876543232', '2024-02-01', 'active'),
(7, 'David Brown', '9876543223', '1990-05-30', 'male', '321 Elm Street, City', 'Emma Brown', '9876543233', '2024-02-10', 'active');

-- Insert Membership Plans
INSERT INTO membership_plans (plan_name, duration_months, price, description, features, status) VALUES 
('Basic Monthly', 1, 999.00, 'Perfect for beginners', '["Gym Access", "Locker Facility", "Basic Equipment"]', 'active'),
('Standard Quarterly', 3, 2499.00, 'Great value for regular gym-goers', '["Gym Access", "Locker Facility", "All Equipment", "Group Classes"]', 'active'),
('Premium Half-Yearly', 6, 4499.00, 'Best value with personal training', '["Gym Access", "Locker Facility", "All Equipment", "Group Classes", "2 PT Sessions/month", "Diet Plan"]', 'active'),
('Elite Annual', 12, 7999.00, 'Ultimate fitness package', '["Gym Access", "Locker Facility", "All Equipment", "Unlimited Group Classes", "4 PT Sessions/month", "Personalized Diet Plan", "Spa Access"]', 'active');

-- Insert Memberships
INSERT INTO memberships (member_id, plan_id, start_date, end_date, status) VALUES 
(1, 3, '2024-01-10', '2024-07-10', 'active'),
(2, 2, '2024-01-15', '2024-04-15', 'active'),
(3, 4, '2024-02-01', '2025-02-01', 'active'),
(4, 1, '2024-02-10', '2024-03-10', 'active');

-- Insert Payments
INSERT INTO payments (membership_id, amount, payment_date, payment_method, transaction_id, status, receipt_number) VALUES 
(1, 4499.00, '2024-01-10', 'card', 'TXN20240110001', 'completed', 'REC-20240110-001'),
(2, 2499.00, '2024-01-15', 'upi', 'UPI20240115001', 'completed', 'REC-20240115-001'),
(3, 7999.00, '2024-02-01', 'online', 'ONL20240201001', 'completed', 'REC-20240201-001'),
(4, 999.00, '2024-02-10', 'cash', NULL, 'completed', 'REC-20240210-001');

-- Insert Sample Attendance (Last 7 days for member 1)
INSERT INTO attendance (member_id, check_in_time, check_out_time, date) VALUES 
(1, '2024-10-19 06:30:00', '2024-10-19 08:00:00', '2024-10-19'),
(1, '2024-10-20 06:45:00', '2024-10-20 08:15:00', '2024-10-20'),
(1, '2024-10-22 07:00:00', '2024-10-22 08:30:00', '2024-10-22'),
(1, '2024-10-23 06:30:00', '2024-10-23 08:00:00', '2024-10-23'),
(2, '2024-10-19 18:00:00', '2024-10-19 19:30:00', '2024-10-19'),
(2, '2024-10-21 18:15:00', '2024-10-21 19:45:00', '2024-10-21'),
(2, '2024-10-23 18:00:00', '2024-10-23 19:30:00', '2024-10-23'),
(3, '2024-10-20 07:00:00', '2024-10-20 08:30:00', '2024-10-20'),
(3, '2024-10-22 07:00:00', '2024-10-22 08:30:00', '2024-10-22'),
(3, '2024-10-24 07:00:00', '2024-10-24 08:30:00', '2024-10-24');

-- Insert Trainer Assignments
INSERT INTO trainer_assignments (member_id, trainer_id, assigned_date, status) VALUES 
(1, 1, '2024-01-10', 'active'),
(2, 1, '2024-01-15', 'active'),
(3, 2, '2024-02-01', 'active'),
(4, 2, '2024-02-10', 'active');

-- Insert Sample Workout Plan
INSERT INTO workout_plans (member_id, trainer_id, plan_details, start_date, end_date, notes) VALUES 
(1, 1, '{
  "plan_name": "Beginner Strength Training",
  "weeks": 4,
  "days": [
    {
      "day": "Monday",
      "focus": "Upper Body",
      "exercises": [
        {"name": "Bench Press", "sets": 3, "reps": 10, "rest": "90s"},
        {"name": "Dumbbell Rows", "sets": 3, "reps": 12, "rest": "60s"},
        {"name": "Shoulder Press", "sets": 3, "reps": 10, "rest": "90s"},
        {"name": "Bicep Curls", "sets": 3, "reps": 12, "rest": "60s"}
      ]
    },
    {
      "day": "Wednesday",
      "focus": "Lower Body",
      "exercises": [
        {"name": "Squats", "sets": 4, "reps": 10, "rest": "120s"},
        {"name": "Leg Press", "sets": 3, "reps": 12, "rest": "90s"},
        {"name": "Lunges", "sets": 3, "reps": 10, "rest": "60s"},
        {"name": "Leg Curls", "sets": 3, "reps": 12, "rest": "60s"}
      ]
    },
    {
      "day": "Friday",
      "focus": "Full Body",
      "exercises": [
        {"name": "Deadlifts", "sets": 3, "reps": 8, "rest": "120s"},
        {"name": "Pull-ups", "sets": 3, "reps": 8, "rest": "90s"},
        {"name": "Push-ups", "sets": 3, "reps": 15, "rest": "60s"},
        {"name": "Plank", "sets": 3, "reps": "60s", "rest": "60s"}
      ]
    }
  ]
}', '2024-01-10', '2024-02-10', 'Focus on form over weight. Progressive overload each week.');

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- View: Active Members with Membership Details
CREATE OR REPLACE VIEW view_active_members AS
SELECT 
    m.id,
    m.full_name,
    m.phone,
    m.email,
    mem.status as membership_status,
    mp.plan_name,
    mem.start_date,
    mem.end_date,
    DATEDIFF(mem.end_date, CURDATE()) as days_remaining
FROM members m
INNER JOIN users u ON m.user_id = u.id
INNER JOIN memberships mem ON m.id = mem.member_id
INNER JOIN membership_plans mp ON mem.plan_id = mp.id
WHERE m.status = 'active' AND mem.status = 'active';

-- View: Monthly Revenue Report
CREATE OR REPLACE VIEW view_monthly_revenue AS
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    COUNT(*) as total_payments,
    SUM(amount) as total_revenue,
    payment_method,
    status
FROM payments
GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), payment_method, status
ORDER BY month DESC;

-- View: Trainer Workload
CREATE OR REPLACE VIEW view_trainer_workload AS
SELECT 
    t.id,
    t.full_name as trainer_name,
    t.specialization,
    COUNT(ta.member_id) as assigned_members,
    t.status
FROM trainers t
LEFT JOIN trainer_assignments ta ON t.id = ta.trainer_id AND ta.status = 'active'
GROUP BY t.id, t.full_name, t.specialization, t.status;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure: Check if membership is expiring soon (within 7 days)
DELIMITER //
CREATE PROCEDURE sp_check_expiring_memberships()
BEGIN
    SELECT 
        m.id,
        m.full_name,
        u.email,
        m.phone,
        mp.plan_name,
        mem.end_date,
        DATEDIFF(mem.end_date, CURDATE()) as days_remaining
    FROM members m
    INNER JOIN users u ON m.user_id = u.id
    INNER JOIN memberships mem ON m.id = mem.member_id
    INNER JOIN membership_plans mp ON mem.plan_id = mp.id
    WHERE mem.status = 'active' 
    AND DATEDIFF(mem.end_date, CURDATE()) BETWEEN 0 AND 7
    ORDER BY days_remaining ASC;
END //
DELIMITER ;

-- Procedure: Get member attendance statistics
DELIMITER //
CREATE PROCEDURE sp_member_attendance_stats(IN p_member_id INT, IN p_start_date DATE, IN p_end_date DATE)
BEGIN
    SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT DATE(check_in_time)) as unique_days,
        MIN(check_in_time) as first_visit,
        MAX(check_in_time) as last_visit,
        AVG(TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time)) as avg_duration_minutes
    FROM attendance
    WHERE member_id = p_member_id 
    AND date BETWEEN p_start_date AND p_end_date
    AND check_out_time IS NOT NULL;
END //
DELIMITER ;

-- Procedure: Generate next receipt number
DELIMITER //
CREATE PROCEDURE sp_generate_receipt_number(OUT receipt_num VARCHAR(50))
BEGIN
    DECLARE today_date VARCHAR(8);
    DECLARE sequence_num INT;
    
    SET today_date = DATE_FORMAT(CURDATE(), '%Y%m%d');
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(receipt_number, -3) AS UNSIGNED)), 0) + 1
    INTO sequence_num
    FROM payments
    WHERE receipt_number LIKE CONCAT('REC-', today_date, '-%');
    
    SET receipt_num = CONCAT('REC-', today_date, '-', LPAD(sequence_num, 3, '0'));
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Auto-update membership status based on end_date
DELIMITER //
CREATE TRIGGER trg_update_membership_status
BEFORE UPDATE ON memberships
FOR EACH ROW
BEGIN
    IF NEW.end_date < CURDATE() AND NEW.status = 'active' THEN
        SET NEW.status = 'expired';
    END IF;
END //
DELIMITER ;

-- Trigger: Prevent duplicate attendance on same day
DELIMITER //
CREATE TRIGGER trg_check_duplicate_attendance
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE attendance_count INT;
    
    SELECT COUNT(*) INTO attendance_count
    FROM attendance
    WHERE member_id = NEW.member_id 
    AND date = NEW.date;
    
    IF attendance_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Member already has attendance record for this date';
    END IF;
END //
DELIMITER ;

DELIMITER ;

-- =====================================================
-- PERFORMANCE OPTIMIZATION
-- =====================================================

-- Additional composite indexes for complex queries
CREATE INDEX idx_member_status_joined ON members(status, joined_date);
CREATE INDEX idx_membership_dates ON memberships(start_date, end_date, status);
CREATE INDEX idx_payment_date_status ON payments(payment_date, status);
CREATE INDEX idx_attendance_member_date ON attendance(member_id, date);

-- =====================================================
-- DATABASE SETUP COMPLETE
-- =====================================================

SELECT 'FitHub Gym Management System Database Created Successfully!' as Status;
SELECT 'Default Admin Login: admin@fithub.com | Password: Admin@123' as AdminCredentials;
SELECT 'Sample Trainer Login: john.trainer@fithub.com | Password: Trainer@123' as TrainerCredentials;
SELECT 'Sample Member Login: alice.member@email.com | Password: Member@123' as MemberCredentials;
