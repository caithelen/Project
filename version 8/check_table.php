<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Orders table columns:\n";
    print_r($columns);
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
