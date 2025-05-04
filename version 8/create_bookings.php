<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Create bookings table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS bookings (
            booking_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sql);

    echo "<h2>Database Update Status</h2>";
    echo "<p style='color: green;'>✓ Bookings table created successfully!</p>";

} catch (Exception $e) {
    echo "<h2>Database Update Error</h2>";
    echo "<p style='color: red;'>✗ Error creating bookings table: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
