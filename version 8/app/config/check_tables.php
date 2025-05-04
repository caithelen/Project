<?php
require_once __DIR__ . '/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Check if tour_trips table exists
    $sql = "SHOW TABLES LIKE 'tour_trips'";
    $result = $db->query($sql)->fetch();

    if (!$result) {
        // Create tour_trips table
        $sql = "CREATE TABLE tour_trips (
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
        echo "Created tour_trips table\n";

        // Insert sample data
        $sql = "INSERT INTO tour_trips (title, description, destination, price, departure_date, duration, max_participants, image) VALUES
            ('Paris Adventure', 'Explore the City of Light', 'Paris, France', 999.99, DATE_ADD(NOW(), INTERVAL 30 DAY), 7, 20, 'paris.jpg'),
            ('Rome Explorer', 'Discover Ancient Rome', 'Rome, Italy', 1299.99, DATE_ADD(NOW(), INTERVAL 45 DAY), 10, 15, 'rome.jpg'),
            ('Amsterdam Tour', 'Experience Dutch Culture', 'Amsterdam, Netherlands', 899.99, DATE_ADD(NOW(), INTERVAL 60 DAY), 5, 25, 'amsterdam.jpg')";
        $db->exec($sql);
        echo "Inserted sample tour trips\n";
    }

    // Check if bookings table exists
    $sql = "SHOW TABLES LIKE 'bookings'";
    $result = $db->query($sql)->fetch();

    if (!$result) {
        // Create bookings table
        $sql = "CREATE TABLE bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            customer_id INT NOT NULL,
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            FOREIGN KEY (trip_id) REFERENCES tour_trips(id)
        )";
        $db->exec($sql);
        echo "Created bookings table\n";
    }

    echo "Database setup completed successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
