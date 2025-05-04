<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Add cancelled_at column if it doesn't exist
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'eurotours' 
        AND TABLE_NAME = 'eurotours_orders' 
        AND COLUMN_NAME = 'cancelled_at'
    ");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE eurotours_orders ADD COLUMN cancelled_at DATETIME DEFAULT NULL AFTER status");
        echo "Added cancelled_at column successfully\n";
    } else {
        echo "cancelled_at column already exists\n";
    }
    
    // Add status column if it doesn't exist
    $stmt = $pdo->prepare("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'eurotours' 
        AND TABLE_NAME = 'eurotours_orders' 
        AND COLUMN_NAME = 'status'
    ");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE eurotours_orders ADD COLUMN status VARCHAR(20) DEFAULT 'pending' AFTER total_amount");
        $pdo->exec("UPDATE eurotours_orders SET status = 'confirmed' WHERE status IS NULL");
        echo "Added status column successfully\n";
    } else {
        echo "status column already exists\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
