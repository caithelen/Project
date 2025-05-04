USE euro;

-- Add date_of_birth and is_student columns to users table if they don't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL,
ADD COLUMN IF NOT EXISTS is_student TINYINT(1) DEFAULT 0;

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
    FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Create order_items table if not exists
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    trip_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_per_unit DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
) ENGINE=InnoDB;

-- Create booking_requirements table if not exists
CREATE TABLE IF NOT EXISTS booking_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    requirement_type ENUM('dietary', 'accessibility', 'room', 'other') NOT NULL,
    requirement_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES order_items(order_item_id)
) ENGINE=InnoDB;

-- Create remember_tokens table if not exists
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY token_unique (token)
) ENGINE=InnoDB;

-- Add current_location column to trips table if it doesn't exist
ALTER TABLE trips
ADD COLUMN IF NOT EXISTS current_location VARCHAR(255) NULL;
