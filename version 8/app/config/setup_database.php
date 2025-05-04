<?php
require_once 'Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Create database if not exists
    $db->exec("CREATE DATABASE IF NOT EXISTS euro_tours");
    $db->exec("USE euro_tours");
    
    // Create customers table
    $db->exec("CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create tour_trips table
    $db->exec("CREATE TABLE IF NOT EXISTS tour_trips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        destination VARCHAR(255) NOT NULL,
        description TEXT,
        duration INT NOT NULL,
        departure_date DATETIME NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        max_participants INT NOT NULL DEFAULT 30,
        booked INT NOT NULL DEFAULT 0,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create bookings table
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        trip_id INT NOT NULL,
        booking_date DATETIME NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed',
        payment_status ENUM('pending', 'completed', 'refunded') NOT NULL DEFAULT 'pending',
        confirmation_code VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (trip_id) REFERENCES tour_trips(id),
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    )");

    // Insert sample trip data
    $db->exec("INSERT INTO tour_trips (title, destination, description, duration, departure_date, price, max_participants) 
               VALUES 
               ('Paris Adventure', 'Paris', 'Experience the magic of Paris', 5, '2025-06-01 09:00:00', 999.99, 30),
               ('Rome Explorer', 'Rome', 'Discover ancient Rome', 7, '2025-06-15 10:00:00', 1299.99, 25),
               ('Amsterdam Tour', 'Amsterdam', 'Explore beautiful Amsterdam', 4, '2025-07-01 08:30:00', 799.99, 20)
               ON DUPLICATE KEY UPDATE id=id");

    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
