-- Drop database if exists and create new one
DROP DATABASE IF EXISTS eurotours;
CREATE DATABASE eurotours;
USE eurotours;

-- Create users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth DATE NULL,
    is_student TINYINT(1) DEFAULT 0,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create trips table
CREATE TABLE trips (
    trip_id INT AUTO_INCREMENT PRIMARY KEY,
    destination VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    cost DECIMAL(10, 2) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE NOT NULL,
    available_seats INT NOT NULL,
    current_location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create orders table
CREATE TABLE orders (
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

-- Create order_items table
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    trip_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_per_unit DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
) ENGINE=InnoDB;

-- Create booking_requirements table
CREATE TABLE booking_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    requirement_type ENUM('dietary', 'accessibility', 'room', 'other') NOT NULL,
    requirement_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES order_items(order_item_id)
) ENGINE=InnoDB;

-- Create remember_tokens table
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY token_unique (token)
) ENGINE=InnoDB;

-- Insert sample users (password is 'password' for all users)
INSERT INTO users (username, password, email, first_name, last_name, role, date_of_birth, is_student) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@eurotours.com', 'Admin', 'User', 'admin', '1990-01-01', 0),
('john_doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'john@example.com', 'John', 'Doe', 'user', '1995-05-15', 1),
('jane_smith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jane@example.com', 'Jane', 'Smith', 'user', '1988-08-20', 0);

-- Insert sample trips
INSERT INTO trips (destination, description, image_url, cost, departure_date, return_date, available_seats, current_location) VALUES
('Paris, France', 'Experience the city of love with our comprehensive Paris tour. Visit the Eiffel Tower, Louvre Museum, and enjoy authentic French cuisine.', '/images/paris.jpg', 599.99, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 37 DAY), 20, 'Paris'),
('Rome, Italy', 'Explore ancient history in the eternal city. Visit the Colosseum, Roman Forum, and Vatican Museums.', '/images/rome.jpg', 699.99, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 52 DAY), 15, 'Rome'),
('Barcelona, Spain', 'Discover Catalan culture, amazing architecture, and Mediterranean beaches.', '/images/barcelona.jpg', 549.99, DATE_ADD(CURRENT_DATE, INTERVAL 60 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 67 DAY), 25, 'Barcelona'),
('Amsterdam, Netherlands', 'Experience Dutch culture, beautiful canals, and historic museums.', '/images/amsterdam.jpg', 499.99, DATE_ADD(CURRENT_DATE, INTERVAL 20 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 27 DAY), 18, 'Amsterdam'),
('Prague, Czech Republic', 'Explore the city of hundred spires, medieval architecture, and rich history.', '/images/prague.jpg', 449.99, DATE_ADD(CURRENT_DATE, INTERVAL 75 DAY), DATE_ADD(CURRENT_DATE, INTERVAL 82 DAY), 22, 'Prague');

-- Insert sample orders
INSERT INTO orders (user_id, total_amount, status, payment_status) VALUES
(2, 599.99, 'confirmed', 'paid'),
(3, 699.99, 'confirmed', 'paid'),
(2, 549.99, 'pending', 'pending');

-- Insert sample order items
INSERT INTO order_items (order_id, trip_id, quantity, price_per_unit) VALUES
(1, 1, 1, 599.99),
(2, 2, 1, 699.99),
(3, 3, 1, 549.99);

-- Insert sample booking requirements
INSERT INTO booking_requirements (booking_id, requirement_type, requirement_value) VALUES
(1, 'dietary', 'Vegetarian meals required'),
(1, 'accessibility', 'Wheelchair access needed'),
(2, 'room', 'Non-smoking room preferred'),
(3, 'other', 'Early check-in requested');
