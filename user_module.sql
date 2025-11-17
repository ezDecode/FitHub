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

INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@fithub.com', 'admin');

INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', 'staff@fithub.com', 'staff');

INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`) VALUES
('member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'member@fithub.com', 'member');

ALTER TABLE `members` ADD COLUMN `user_id` INT(11) NULL AFTER `id`;
ALTER TABLE `members` ADD INDEX `user_id_index` (`user_id`);
ALTER TABLE `members` ADD CONSTRAINT `members_user_fk` 
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;
