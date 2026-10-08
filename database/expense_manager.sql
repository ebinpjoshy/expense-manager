-- ========================================================
-- Personal Expense Management System
-- Database Schema and Sample Data
-- Database: expense_manager
-- Compatible with MySQL 5.7+ / 8.0+ / MariaDB (XAMPP)
-- ========================================================

-- For shared hosting (InfinityFree/cPanel), select your created database in phpMyAdmin, then import:
-- CREATE DATABASE IF NOT EXISTS `expense_manager` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `expense_manager`;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `budgets`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(15) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `category_id` INT NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `type` ENUM('income', 'expense') NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `transactions`
-- --------------------------------------------------------
CREATE TABLE `transactions` (
  `transaction_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `type` ENUM('income', 'expense') NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `transaction_date` DATE NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `idx_transactions_user` (`user_id`),
  KEY `idx_transactions_date` (`transaction_date`),
  KEY `idx_transactions_type` (`type`),
  CONSTRAINT `fk_transactions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transactions_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `budgets`
-- --------------------------------------------------------
CREATE TABLE `budgets` (
  `budget_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `month` INT NOT NULL,
  `year` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`budget_id`),
  UNIQUE KEY `uq_user_cat_month_year` (`user_id`, `category_id`, `month`, `year`),
  CONSTRAINT `fk_budgets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_budgets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Default Categories
-- --------------------------------------------------------
INSERT INTO `categories` (`category_id`, `category_name`, `type`) VALUES
-- Income categories
(1, 'Salary', 'income'),
(2, 'Scholarship', 'income'),
(3, 'Freelancing', 'income'),
(4, 'Pocket Money', 'income'),
(5, 'Business', 'income'),
(6, 'Other Income', 'income'),
-- Expense categories
(7, 'Food', 'expense'),
(8, 'Transport', 'expense'),
(9, 'Education', 'expense'),
(10, 'Shopping', 'expense'),
(11, 'Bills', 'expense'),
(12, 'Entertainment', 'expense'),
(13, 'Health', 'expense'),
(14, 'Rent', 'expense'),
(15, 'Travel', 'expense'),
(16, 'Other Expense', 'expense');

-- --------------------------------------------------------
-- Sample Demo User (For College Testing / Viva Demo)
-- Email: demo@example.com
-- Password: Password@123
-- --------------------------------------------------------
INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'Rahul Sharma', 'demo@example.com', '9876543210', '$2y$10$0ay62XtwZuIZ6lxV7wCi4ONMn0UvqyTXhasiAlJZrCPpJ3I64jKQ2', NOW());

-- --------------------------------------------------------
-- Sample Budgets for Demo User (Current Month & Year)
-- --------------------------------------------------------
INSERT INTO `budgets` (`user_id`, `category_id`, `amount`, `month`, `year`) VALUES
(1, 7, 5000.00, MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE())),   -- Food budget
(1, 8, 2000.00, MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE())),   -- Transport budget
(1, 10, 3000.00, MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE())),  -- Shopping budget
(1, 11, 2500.00, MONTH(CURRENT_DATE()), YEAR(CURRENT_DATE()));  -- Bills budget

-- --------------------------------------------------------
-- Sample Transactions for Demo User
-- --------------------------------------------------------
INSERT INTO `transactions` (`user_id`, `category_id`, `amount`, `type`, `payment_method`, `transaction_date`, `description`) VALUES
-- Income
(1, 1, 35000.00, 'income', 'Bank', DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY), 'Monthly Job Salary'),
(1, 3, 5000.00, 'income', 'GPay', DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY), 'Freelance Web Project'),
-- Expenses (spread across current week and days for chart demonstration)
(1, 7, 450.00, 'expense', 'GPay', CURRENT_DATE(), 'College Canteen Lunch'),
(1, 8, 120.00, 'expense', 'Cash', CURRENT_DATE(), 'Metro recharge'),
(1, 7, 650.00, 'expense', 'PhonePe', DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY), 'Dinner with friends'),
(1, 10, 2200.00, 'expense', 'Card', DATE_SUB(CURRENT_DATE(), INTERVAL 2 DAY), 'New Shoes from Mall'),
(1, 8, 300.00, 'expense', 'Cash', DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY), 'Auto Rickshaw & Fuel'),
(1, 11, 1400.00, 'expense', 'GPay', DATE_SUB(CURRENT_DATE(), INTERVAL 4 DAY), 'Broadband WiFi Bill'),
(1, 7, 850.00, 'expense', 'GPay', DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY), 'Supermarket Grocery'),
(1, 12, 500.00, 'expense', 'PhonePe', DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY), 'Movie Tickets'),
(1, 9, 1200.00, 'expense', 'Bank', DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY), 'Programming Reference Book'),
(1, 14, 8000.00, 'expense', 'Bank', DATE_SUB(CURRENT_DATE(), INTERVAL 15 DAY), 'Hostel/Apartment Rent'),
(1, 13, 750.00, 'expense', 'Card', DATE_SUB(CURRENT_DATE(), INTERVAL 18 DAY), 'Pharmacy Medicines');
