<?php
require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Start transaction
    $pdo->beginTransaction();

    // Check and add columns if they don't exist
    $columns = [
        'first_name' => 'VARCHAR(50) NULL',
        'last_name' => 'VARCHAR(50) NULL',
        'email' => 'VARCHAR(100) NULL',
        'phone' => 'VARCHAR(20) NULL',
        'address' => 'VARCHAR(255) NULL',
        'city' => 'VARCHAR(100) NULL',
        'postal_code' => 'VARCHAR(20) NULL',
        'discount_amount' => 'DECIMAL(10, 2) DEFAULT 0',
        'final_amount' => 'DECIMAL(10, 2) NULL'
    ];

    // Get existing columns
    $existingColumns = [];
    $columnsResult = $pdo->query("SHOW COLUMNS FROM orders");
    while ($row = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }

    // Add missing columns
    foreach ($columns as $columnName => $columnDef) {
        if (!in_array($columnName, $existingColumns)) {
            $sql = "ALTER TABLE orders ADD COLUMN {$columnName} {$columnDef}";
            $pdo->exec($sql);
        }
    }

    // Commit the changes
    $pdo->commit();

    echo "<h2>Database Update Status</h2>";
    echo "<p style='color: green;'>✓ Database updated successfully!</p>";
    echo "<p>You can now return to the checkout page.</p>";

} catch (Exception $e) {
    // Rollback the transaction if something failed
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<h2>Database Update Error</h2>";
    echo "<p style='color: red;'>✗ Error updating database: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
