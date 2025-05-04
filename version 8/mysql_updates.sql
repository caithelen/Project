USE euro;

-- Add new columns to users table
ALTER TABLE users
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL,
ADD COLUMN IF NOT EXISTS is_student TINYINT(1) DEFAULT 0;

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id),
    CONSTRAINT fk_orders_users FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB;

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT,
    order_id INT NOT NULL,
    trip_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_per_unit DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (order_item_id),
    CONSTRAINT fk_order_items_orders FOREIGN KEY (order_id) REFERENCES orders(order_id),
    CONSTRAINT fk_order_items_trips FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
) ENGINE=InnoDB;

-- Create booking_requirements table
CREATE TABLE IF NOT EXISTS booking_requirements (
    id INT AUTO_INCREMENT,
    booking_id INT NOT NULL,
    requirement_type ENUM('dietary', 'accessibility', 'room', 'other') NOT NULL,
    requirement_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_booking_requirements_items FOREIGN KEY (booking_id) REFERENCES order_items(order_item_id)
) ENGINE=InnoDB;

-- Add current_location to trips table
ALTER TABLE trips
ADD COLUMN IF NOT EXISTS current_location VARCHAR(255) NULL;
