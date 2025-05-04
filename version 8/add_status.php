<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Add status column if it doesn't exist
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'eurotours_orders' 
        AND COLUMN_NAME = 'status'
    ");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE eurotours_orders ADD COLUMN status VARCHAR(20) DEFAULT 'confirmed' AFTER total_amount");
        echo "Added status column successfully\n";
    } else {
        echo "status column already exists\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
