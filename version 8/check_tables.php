<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    echo "<h2>Table Structures</h2>";

    // Check orders table
    $result = $pdo->query("SHOW CREATE TABLE orders");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Orders Table:</h3>";
    echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";

    // Check order_items table
    $result = $pdo->query("SHOW CREATE TABLE order_items");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Order Items Table:</h3>";
    echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";

    // Check payments table
    $result = $pdo->query("SHOW CREATE TABLE payments");
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Payments Table:</h3>";
    echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";

} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
