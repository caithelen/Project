<?php
require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Start transaction
    $pdo->beginTransaction();

    // Add new columns to orders table
    $sql = "
        ALTER TABLE orders
        ADD COLUMN IF NOT EXISTS first_name VARCHAR(50) NULL,
        ADD COLUMN IF NOT EXISTS last_name VARCHAR(50) NULL,
        ADD COLUMN IF NOT EXISTS email VARCHAR(100) NULL,
        ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL,
        ADD COLUMN IF NOT EXISTS address VARCHAR(255) NULL,
        ADD COLUMN IF NOT EXISTS city VARCHAR(100) NULL,
        ADD COLUMN IF NOT EXISTS postal_code VARCHAR(20) NULL,
        ADD COLUMN IF NOT EXISTS discount_amount DECIMAL(10, 2) DEFAULT 0,
        ADD COLUMN IF NOT EXISTS final_amount DECIMAL(10, 2) NULL
    ";

    $pdo->exec($sql);

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
    $pdo->exec("CREATE INDEX idx_users_email ON users(email)");
    $pdo->exec("CREATE INDEX idx_users_role ON users(role)");
    echo "Created indexes for better performance...<br>";

    // Create a default admin user
    $adminUsername = 'admin';
    $adminEmail = 'admin@eurotours.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, department, access_level) VALUES (?, ?, ?, 'admin', 'System', 2)");
    $stmt->execute([$adminUsername, $adminEmail, $adminPassword]);
    echo "Created default admin user (username: admin, password: admin123)...<br>";

    echo "<br>Database update completed successfully!";

} catch(PDOException $e) {
    die("Database update failed: " . $e->getMessage());
}
?>
