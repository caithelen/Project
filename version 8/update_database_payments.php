<?php
require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Create payments table
    $sql = "
        CREATE TABLE IF NOT EXISTS payments (
            payment_id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
            payment_method VARCHAR(50) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(order_id)
        )
    ";
    $pdo->exec($sql);

    echo "<h2>Database Update Status</h2>";
    echo "<p style='color: green;'>✓ Payments table created successfully!</p>";

} catch (Exception $e) {
    echo "<h2>Database Update Error</h2>";
    echo "<p style='color: red;'>✗ Error creating payments table: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
