<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Drop and recreate order_items table
    $pdo->exec("DROP TABLE IF EXISTS order_items");
    $pdo->exec("CREATE TABLE order_items (
        order_item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        trip_id INT NOT NULL,
        quantity INT DEFAULT 1,
        price_per_unit DECIMAL(10,2) DEFAULT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(order_id),
        FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Drop and recreate payments table
    $pdo->exec("DROP TABLE IF EXISTS payments");
    $pdo->exec("CREATE TABLE payments (
        payment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
        payment_method VARCHAR(50) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Commit transaction
    $pdo->commit();

    echo "<h2>Database Update Status</h2>";
    echo "<p style='color: green;'>✓ Tables updated successfully!</p>";
    echo "<ul>";
    echo "<li>Fixed order_items table structure</li>";
    echo "<li>Recreated payments table with correct structure</li>";
    echo "</ul>";

} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<h2>Database Update Error</h2>";
    echo "<p style='color: red;'>✗ Error updating tables: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
