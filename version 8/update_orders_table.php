<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Add discount_amount and final_amount columns
    $sql = "ALTER TABLE orders 
            ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(10,2) DEFAULT 0.00,
            ADD COLUMN IF NOT EXISTS final_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00";
    
    $pdo->exec($sql);
    echo "Successfully updated orders table structure.\n";
    
} catch (Exception $e) {
    die("Error updating database: " . $e->getMessage() . "\n");
}
?>
