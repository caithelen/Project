-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

-- Add date_of_birth and is_student columns to users table if they don't exist
SET @exist_dob := (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'date_of_birth');
SET @sqlstmt_dob := IF(@exist_dob = 0, 'ALTER TABLE users ADD COLUMN date_of_birth DATE NULL', 'SELECT "Column date_of_birth already exists"');
PREPARE stmt FROM @sqlstmt_dob;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @exist_student := (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'is_student');
SET @sqlstmt_student := IF(@exist_student = 0, 'ALTER TABLE users ADD COLUMN is_student TINYINT(1) DEFAULT 0', 'SELECT "Column is_student already exists"');
PREPARE stmt FROM @sqlstmt_student;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add current_location column to trips table if it doesn't exist
SET @exist_location := (SELECT COUNT(1) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'trips' AND column_name = 'current_location');
SET @sqlstmt_location := IF(@exist_location = 0, 'ALTER TABLE trips ADD COLUMN current_location VARCHAR(255) NULL', 'SELECT "Column current_location already exists"');
PREPARE stmt FROM @sqlstmt_location;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create remember_tokens table if not exists
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT FK_remember_tokens_users FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    CONSTRAINT UQ_remember_tokens_token UNIQUE (token)
) ENGINE=InnoDB;

-- Add index for faster token lookups if it doesn't exist
SET @exist := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'remember_tokens' AND index_name = 'idx_token_expires');
SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE remember_tokens ADD INDEX idx_token_expires (token, expires)', 'SELECT "Index already exists"');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create booking_requirements table if not exists
CREATE TABLE IF NOT EXISTS booking_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    requirement_type ENUM('dietary', 'accessibility', 'room', 'other') NOT NULL,
    requirement_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT FK_booking_requirements_bookings FOREIGN KEY (booking_id) REFERENCES order_items(order_item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Add index for faster lookups if it doesn't exist
SET @exist := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'booking_requirements' AND index_name = 'idx_booking_requirements');
SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE booking_requirements ADD INDEX idx_booking_requirements (booking_id, requirement_type)', 'SELECT "Index already exists"');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create orders table if not exists
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT FK_orders_users FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Add index for faster lookups
SET @exist := (SELECT COUNT(1) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'orders' AND index_name = 'idx_user_date');
SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE orders ADD INDEX idx_user_date (user_id, order_date)', 'SELECT "Index already exists"');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
