<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Users table created/verified successfully.\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
