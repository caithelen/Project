<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    $_SESSION['error_message'] = 'Please login to cancel your booking.';
    header('Location: login.php');
    exit;
}

// Check if order ID is provided
if (!isset($_POST['order_id'])) {
    $_SESSION['error_message'] = 'Invalid request.';
    header('Location: my_bookings.php');
    exit;
}

$order_id = (int)$_POST['order_id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Verify order belongs to user
    $stmt = $pdo->prepare("
        SELECT status 
        FROM eurotours_orders 
        WHERE order_id = ? AND user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user']['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        throw new Exception('Order not found.');
    }
    
    if ($order['status'] === 'cancelled') {
        throw new Exception('Order is already cancelled.');
    }
    
    // Get order items to restore trip seats
    $stmt = $pdo->prepare("
        SELECT oi.trip_id
        FROM order_items oi
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update order status
    $stmt = $pdo->prepare("
        UPDATE eurotours_orders 
        SET status = 'cancelled'
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id]);
    
    // Restore available seats for each trip
    $stmt = $pdo->prepare("
        UPDATE trips 
        SET available_seats = available_seats + 1 
        WHERE trip_id = ?
    ");
    
    foreach ($items as $item) {
        $stmt->execute([$item['trip_id']]);
    }
    
    $pdo->commit();
    
    $_SESSION['success_message'] = 'Your booking has been cancelled successfully.';
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error_message'] = 'Failed to cancel booking: ' . $e->getMessage();
}

header('Location: my_bookings.php');
exit;
