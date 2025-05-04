<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if we have trip data
if (!isset($_POST['trip_id']) || !isset($_POST['cost'])) {
    $_SESSION['error_message'] = 'Missing trip information';
    header('Location: shop.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Generate confirmation code
    $confirmationCode = strtoupper(substr(uniqid(), -6));
    
    // Validate required fields
    $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'card_number', 'card_holder', 'expiry', 'cvv'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception('Missing required field: ' . $field);
        }
    }

    // Create booking record
    $stmt = $pdo->prepare("
        INSERT INTO bookings (
            user_id, 
            trip_id, 
            booking_date,
            status,
            total_amount,
            confirmation_code,
            first_name,
            last_name,
            email,
            phone,
            address,
            city
        ) VALUES (?, ?, NOW(), 'confirmed', ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['user']['user_id'],
        $_POST['trip_id'],
        $_POST['cost'],
        $confirmationCode,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city']
    ]);
    
    $bookingId = $pdo->lastInsertId();
    
    // Update available seats
    $stmt = $pdo->prepare("
        UPDATE trips 
        SET available_seats = available_seats - 1 
        WHERE trip_id = ? AND available_seats > 0
    ");
    
    $result = $stmt->execute([$_POST['trip_id']]);
    
    if ($stmt->rowCount() === 0) {
        // No seats were updated, meaning no seats available
        throw new Exception('No seats available for this trip');
    }
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['success_message'] = 'Booking confirmed! Your confirmation code is: ' . $confirmationCode;
    header('Location: my_bookings.php');
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $_SESSION['error_message'] = 'Booking failed: ' . $e->getMessage();
    header('Location: shop.php');
}
