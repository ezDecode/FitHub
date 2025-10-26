-- Simple Gym Management System Database
-- Created: 2025-10-26
-- Version: 1.0.0

-- Create the database
CREATE DATABASE IF NOT EXISTS gym_system;
USE gym_system;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS members;

-- Create members table
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    join_date DATE NOT NULL,
    membership_type VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'active' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME NOT NULL,
    check_out TIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_member_date (member_id, date),
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing
INSERT INTO members (name, email, phone, join_date, membership_type, status) VALUES
('John Doe', 'john.doe@example.com', '1234567890', '2025-01-15', 'Monthly', 'active'),
('Jane Smith', 'jane.smith@example.com', '0987654321', '2025-02-20', 'Quarterly', 'active'),
('Mike Johnson', 'mike.j@example.com', '5551234567', '2025-03-10', 'Yearly', 'active'),
('Sarah Williams', 'sarah.w@example.com', '5559876543', '2025-04-05', 'Monthly', 'active'),
('David Brown', 'david.b@example.com', '5556789012', '2025-05-12', 'Quarterly', 'inactive');

-- Insert sample attendance data
INSERT INTO attendance (member_id, date, check_in, check_out) VALUES
(1, '2025-10-25', '08:30:00', '10:00:00'),
(2, '2025-10-25', '09:00:00', '10:30:00'),
(1, '2025-10-26', '07:45:00', NULL),
(3, '2025-10-26', '08:15:00', NULL);

-- Show success message
SELECT 'Database setup completed successfully!' AS Status;
