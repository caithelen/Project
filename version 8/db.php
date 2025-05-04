<?php
$host = 'localhost';
$dbname = 'euro'; // Replace with your actual database name if different
$username = 'root'; // Default for Laragon
$password = 'Ehw2019!';     // Default is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
