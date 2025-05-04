<?php
require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Check trips
    $stmt = $pdo->query("SELECT * FROM trips WHERE available_seats > 0 LIMIT 1");
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($trip) {
        echo "Found available trip:\n";
        echo "Trip ID: " . $trip['trip_id'] . "\n";
        echo "Destination: " . $trip['destination'] . "\n";
        echo "Available Seats: " . $trip['available_seats'] . "\n";
        echo "Cost: €" . $trip['cost'] . "\n";
    } else {
        echo "No available trips found\n";
    }
    
    // Check if we have a test user
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "\nFound test user:\n";
        echo "User ID: " . $user['user_id'] . "\n";
        echo "Email: " . $user['email'] . "\n";
    } else {
        echo "\nNo users found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
