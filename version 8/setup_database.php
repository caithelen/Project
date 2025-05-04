<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Create tour_trips table
    $sql = "CREATE TABLE IF NOT EXISTS tour_trips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        destination VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        departure_date DATETIME NOT NULL,
        duration INT NOT NULL,
        max_participants INT NOT NULL,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    echo "tour_trips table created or already exists\n";

    // Check if tour_trips table is empty
    $result = $db->query("SELECT COUNT(*) as count FROM tour_trips")->fetch();
    if ($result['count'] == 0) {
        // Insert sample data
        $sql = "INSERT INTO tour_trips (title, description, destination, price, departure_date, duration, max_participants, image) VALUES
            ('Paris Adventure', 'Explore the City of Light with our expert guides. Visit the Eiffel Tower, Louvre Museum, and cruise along the Seine.', 'Paris, France', 999.99, DATE_ADD(NOW(), INTERVAL 30 DAY), 7, 20, 'paris.jpg'),
            ('Rome Explorer', 'Discover Ancient Rome and Vatican City. Visit the Colosseum, Roman Forum, and Sistine Chapel.', 'Rome, Italy', 1299.99, DATE_ADD(NOW(), INTERVAL 45 DAY), 10, 15, 'rome.jpg'),
            ('Amsterdam Tour', 'Experience Dutch culture, art, and history. Visit museums, take canal tours, and explore the city by bike.', 'Amsterdam, Netherlands', 899.99, DATE_ADD(NOW(), INTERVAL 60 DAY), 5, 25, 'amsterdam.jpg')";
        $db->exec($sql);
        echo "Sample tour trips inserted\n";
    }

    // Create bookings table
    $sql = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        customer_id INT NOT NULL,
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
        FOREIGN KEY (trip_id) REFERENCES tour_trips(id)
    )";
    $db->exec($sql);
    echo "bookings table created or already exists\n";

    // Create customers table
    $sql = "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    echo "customers table created or already exists\n";

    echo "Database setup completed successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
