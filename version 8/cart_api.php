<?php
session_start();
header('Content-Type: application/json');

require_once 'TourTrip.php';

$host = 'localhost';
$db = 'euro';
$user = 'root';
$pass = 'Ehw2019!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => '', 'data' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'add':
            if (isset($data['trip_id'])) {
                $stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ?");
                $stmt->execute([$data['trip_id']]);
                $trip = $stmt->fetch();
                
                if ($trip) {
                    $_SESSION['cart'][] = [
                        'trip_id' => $trip['id'],
                        'destination' => $trip['destination'],
                        'cost' => $trip['cost'],
                        'description' => $trip['description']
                    ];
                    $response['success'] = true;
                    $response['message'] = 'Trip added to cart';
                }
            }
            break;
            
        case 'remove':
            if (isset($data['trip_id'])) {
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['trip_id'] == $data['trip_id']) {
                        unset($_SESSION['cart'][$key]);
                        break;
                    }
                }
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                $response['success'] = true;
                $response['message'] = 'Trip removed from cart';
            }
            break;
            
        case 'update':
            if (isset($data['trip_id']) && isset($data['quantity'])) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['trip_id'] == $data['trip_id']) {
                        $item['quantity'] = max(1, min(10, (int)$data['quantity']));
                        break;
                    }
                }
                $response['success'] = true;
                $response['message'] = 'Quantity updated';
            }
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            $response['success'] = true;
            $response['message'] = 'Cart cleared';
            break;
    }
}

// Calculate cart totals and prepare response data
$total = 0;
$formattedItems = [];

foreach ($_SESSION['cart'] as $item) {
    $quantity = $item['quantity'] ?? 1;
    $itemTotal = $item['cost'] * $quantity;
    $total += $itemTotal;
    
    $formattedItems[] = [
        'trip_id' => $item['trip_id'],
        'destination' => $item['destination'],
        'cost' => $item['cost'],
        'quantity' => $quantity,
        'total' => $itemTotal
    ];
}

// Return cart data with response
$response['data'] = [
    'items' => $formattedItems,
    'total' => $total,
    'itemCount' => count($_SESSION['cart'])
];

echo json_encode($response);
