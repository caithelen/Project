<?php
session_start();
require_once 'config/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();

    // Get user's booking statuses
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.status,
            t.current_location
                ELSE 'Completed'
            END as trip_status,
            CASE 
                WHEN t.departure_date > NOW() THEN NULL
                WHEN t.departure_date <= NOW() AND t.return_date >= NOW() THEN t.destination
                ELSE 'Trip Completed'
            END as current_location
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN trips t ON oi.trip_id = t.trip_id
        WHERE o.user_id = ?
        AND o.status != 'Cancelled'
        ORDER BY t.departure_date DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($bookings);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
