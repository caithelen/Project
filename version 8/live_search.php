<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $query = $_GET['q'] ?? '';
    
    if (strlen($query) >= 2) {
        $stmt = $pdo->prepare("
            SELECT 
                t.trip_id,
                t.destination,
                t.description,
                t.departure_date,
                t.return_date,
                t.cost,
                t.available_seats,
                COUNT(DISTINCT o.order_id) as booking_count
            FROM trips t
            LEFT JOIN order_items oi ON t.trip_id = oi.trip_id
            LEFT JOIN eurotours_orders o ON oi.order_id = o.order_id AND o.status != 'cancelled'
            WHERE 
                t.destination LIKE ? 
                OR t.description LIKE ?
                OR t.departure_date LIKE ?
            GROUP BY t.trip_id
            HAVING t.available_seats > 0
            ORDER BY t.departure_date ASC
            LIMIT 5
        ");
        
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dates and add popularity indicator
        foreach ($results as &$trip) {
            $trip['departure_date'] = date('M j, Y', strtotime($trip['departure_date']));
            $trip['return_date'] = date('M j, Y', strtotime($trip['return_date']));
            $trip['duration'] = ceil((strtotime($trip['return_date']) - strtotime($trip['departure_date'])) / (60*60*24));
            $trip['popularity'] = $trip['booking_count'] > 5 ? 'High Demand!' : '';
            unset($trip['booking_count']);
        }
        
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
    
} catch (Exception $e) {
    error_log('Search query failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}
