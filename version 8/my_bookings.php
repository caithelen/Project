<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user's bookings
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Get orders with items
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            COALESCE(o.status, 'confirmed') as status,
            o.total_amount,
            o.billing_first_name,
            o.billing_last_name,
            o.billing_email,
            o.billing_phone,
            GROUP_CONCAT(t.destination) as destinations
        FROM eurotours_orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN trips t ON oi.trip_id = t.trip_id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");

    $stmt->execute([$_SESSION['user']['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get order items for each order
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            t.destination,
            t.description,
            t.departure_date,
            t.return_date
        FROM order_items oi
        JOIN trips t ON oi.trip_id = t.trip_id
        WHERE oi.order_id = ?
    ");

} catch (Exception $e) {
    $error = "Error retrieving bookings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bookings-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .booking-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .booking-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .status-confirmed {
            background: #4CAF50;
            color: white;
        }
        .status-pending {
            background: #FFC107;
            color: black;
        }
        .status-confirmed {
            background: #4CAF50;
            color: white;
        }
        .status-cancelled {
            background: #F44336;
            color: white;
        }
        .cancelled-date {
            color: #F44336;
            font-size: 14px;
            margin-top: 5px;
        }
        .booking-header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cancel-form {
            margin: 0;
        }
        .cancel-btn {
            background: #ff5252;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .cancel-btn:hover {
            background: #d32f2f;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }
        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .detail-group {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
        }
        .no-bookings {
            text-align: center;
            padding: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="bookings-container">
        <h1>My Bookings</h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['success_message']);
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">You have no bookings yet.</div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <h3>Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                            <p>Ordered on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>

                        </div>
                        <div class="booking-header-right">
                            <span class="booking-status status-<?php echo strtolower($order['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                            <?php if ($order['status'] !== 'cancelled'): ?>
                                <form action="cancel_booking.php" method="POST" class="cancel-form" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                    <button type="submit" class="cancel-btn">Cancel Booking</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-group">
                            <div class="detail-label">Destinations</div>
                            <div class="detail-value"><?php echo htmlspecialchars($order['destinations']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">Total Amount</div>
                            <div class="detail-value">€<?php echo number_format($order['total_amount'], 2); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">Billing Details</div>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']); ?><br>
                                <?php echo htmlspecialchars($order['billing_email']); ?><br>
                                <?php echo htmlspecialchars($order['billing_phone']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (empty($bookings)): ?>
            <div class="no-bookings">
                <h2>No bookings found</h2>
                <p>You haven't made any bookings yet. <a href="shop.php">Browse our trips</a> to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <h2>Booking #<?php echo htmlspecialchars($booking['booking_id']); ?></h2>
                        <span class="booking-status status-<?php echo strtolower(htmlspecialchars($booking['booking_status'])); ?>">
                            <?php echo htmlspecialchars($booking['booking_status']); ?>
                        </span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-group">
                            <div class="detail-label">Destination</div>
                            <div class="detail-value"><?php echo htmlspecialchars($booking['destination']); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Travel Dates</div>
                            <div class="detail-value">
                                <?php 
                                    echo date('M d, Y', strtotime($booking['departure_date'])) . ' - ' . 
                                         date('M d, Y', strtotime($booking['return_date']));
                                ?>
                            </div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Booking Date</div>
                            <div class="detail-value"><?php echo date('M d, Y H:i', strtotime($booking['booking_date'])); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Total Amount</div>
                            <div class="detail-value">€<?php echo number_format($booking['total_amount'], 2); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Confirmation Code</div>
                            <div class="detail-value"><?php echo htmlspecialchars($booking['confirmation_code']); ?></div>
                        </div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Payment Method</div>
                            <div class="detail-value"><?php echo htmlspecialchars($booking['payment_method']); ?></div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Booking Date</div>
                            <div class="detail-value"><?php echo date('M d, Y H:i', strtotime($booking['booking_date'])); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
