<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}



// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = (int)$_GET['order_id'];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Get order details
    $stmt = $pdo->prepare("
        SELECT o.*
        FROM eurotours_orders o
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    
    $stmt->execute([$order_id, $_SESSION['user']['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: index.php');
        exit;
    }
    
    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, t.destination, t.description
        FROM order_items oi
        JOIN trips t ON oi.trip_id = t.trip_id
        WHERE oi.order_id = ?
    ");
    
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log('Confirmation error: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .confirmation-box {
            background: #e8f5e9;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .confirmation-box h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }
        
        .confirmation-box .check-icon {
            color: #2e7d32;
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .order-details {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .order-details h3 {
            color: #2e7d32;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8f5e9;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .detail-label {
            font-weight: 500;
            color: #555;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
        }
        
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .total-row {
            font-weight: bold;
            color: #2e7d32;
        }
        
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-view-bookings {
            display: inline-block;
            background: #1565c0;
            color: white;
            padding: 12px 25px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn-view-bookings:hover {
            background: #0d47a1;
        }
        
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 10px;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .items-table {
                font-size: 14px;
            }
            
            .items-table th,
            .items-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="confirmation-container">
        <div class="confirmation-box">
            <div class="check-icon">✓</div>
            <h2>Order Confirmed!</h2>
            <p>Thank you for your order. Your booking has been successfully processed.</p>
            <p>Order #<?= htmlspecialchars($order_id) ?></p>
        </div>

        <div class="order-details">
            <h3>Order Details</h3>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span><?= date('F j, Y', strtotime($order['order_date'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span><?= htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span><?= htmlspecialchars($order['billing_email']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span><?= htmlspecialchars($order['billing_phone']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span>
                    <?= htmlspecialchars($order['billing_address']) ?><br>
                    <?= htmlspecialchars($order['billing_city']) ?>, <?= htmlspecialchars($order['billing_postal_code']) ?>
                </span>
            </div>
        </div>

        <div class="order-details">
            <h3>Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Destination</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['destination']) ?></td>
                        <td><?= htmlspecialchars($item['description']) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td>€<?= number_format($item['price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td>€<?= number_format($order['total_amount'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="actions">
            <a href="my_bookings.php" class="btn-view-bookings">View My Bookings</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
