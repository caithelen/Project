<?php
session_start();

require_once 'config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

require_once 'header.php';

// Get user's bookings
$userId = $_SESSION['user']['user_id'];
$bookings = [];

try {
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.status,
            oi.order_item_id,
            t.destination,
            t.description,
            t.cost,
            t.departure_date,
            t.return_date,
            br.requirement_type,
            br.requirement_value
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN trips t ON oi.trip_id = t.trip_id
        LEFT JOIN booking_requirements br ON oi.order_item_id = br.booking_id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group results by order
    $bookings = [];
    foreach ($results as $row) {
        $orderId = $row['order_id'];
        if (!isset($bookings[$orderId])) {
            $bookings[$orderId] = [
                'order_id' => $orderId,
                'order_date' => $row['order_date'],
                'total_amount' => $row['total_amount'],
                'status' => $row['status'],
                'items' => []
            ];
        }

        $itemId = $row['order_item_id'];
        if (!isset($bookings[$orderId]['items'][$itemId])) {
            $bookings[$orderId]['items'][$itemId] = [
                'destination' => $row['destination'],
                'description' => $row['description'],
                'cost' => $row['cost'],
                'departure_date' => $row['departure_date'],
                'return_date' => $row['return_date'],
                'requirements' => []
            ];
        }

        if ($row['requirement_type']) {
            $bookings[$orderId]['items'][$itemId]['requirements'][] = [
                'type' => $row['requirement_type'],
                'value' => $row['requirement_value']
            ];
        }
    }
} catch (PDOException $e) {
    error_log('Error fetching bookings: ' . $e->getMessage());
    $error = "An error occurred while fetching your bookings.";
}
?>

<div class="container">
    <h2>My Bookings</h2>

        <div id="bookings-container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">You don't have any bookings yet. <a href="shop.php">Browse our trips</a></div>
    <?php else: ?>
        <?php foreach ($bookings as $order): ?>
            <div class="booking-order">
                <div class="order-header">
                    <h3>Order #<?= htmlspecialchars($order['order_id']) ?></h3>
                    <div class="order-details">
                        <span class="order-date">Ordered: <?= date('F j, Y', strtotime($order['order_date'])) ?></span>
                        <span class="order-status" id="booking-status-<?= $order['order_id'] ?>">Status: <?= htmlspecialchars(ucfirst($order['status'])) ?></span>
                        <span class="order-total">Total: $<?= number_format($order['total_amount'], 2) ?></span>
                        <span class="location" id="booking-location-<?= $order['order_id'] ?>"></span>
                    </div>
                </div>

                <div class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="booking-item">
                            <h4><?= htmlspecialchars($item['destination']) ?></h4>
                            <p class="description"><?= htmlspecialchars($item['description']) ?></p>
                            <div class="trip-dates">
                                <span>Departure: <?= date('F j, Y', strtotime($item['departure_date'])) ?></span>
                                <span>Return: <?= date('F j, Y', strtotime($item['return_date'])) ?></span>
                            </div>
                            <div class="cost">Cost: $<?= number_format($item['cost'], 2) ?></div>
                            
                            <?php if (!empty($item['requirements'])): ?>
                                <div class="requirements">
                                    <h5>Special Requirements:</h5>
                                    <ul>
                                        <?php foreach ($item['requirements'] as $req): ?>
                                            <li>
                                                <strong><?= htmlspecialchars(ucfirst($req['type'])) ?>:</strong>
                                                <?= htmlspecialchars($req['value']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<script>
function updateBookingStatuses() {
    fetch('booking_status_ajax.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(booking => {
                const statusElement = document.querySelector(`#booking-status-${booking.order_id}`);
                if (statusElement) {
                    statusElement.textContent = booking.status;
                    statusElement.className = `status-badge status-${booking.status.replace(' ', '-')}`;
                }

                const locationElement = document.querySelector(`#booking-location-${booking.order_id}`);
                if (locationElement && booking.current_location) {
                    locationElement.textContent = booking.current_location;
                }
            });
        })
        .catch(error => console.error('Error updating bookings:', error));
}

// Update every 30 seconds
setInterval(updateBookingStatuses, 30000);

// Initial update
updateBookingStatuses();
</script>

<style>
.booking-order {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 20px;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.9em;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background-color: #d4edda;
    color: #155724;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
}

.location {
    font-style: italic;
    color: #666;
    margin-left: 10px;
}

.order-header {
    border-bottom: 1px solid #eee;
    margin-bottom: 15px;
    padding-bottom: 15px;
}

.order-header h3 {
    color: #2e7d32;
    margin: 0 0 10px 0;
}

.order-details {
    display: flex;
    gap: 20px;
    color: #666;
    font-size: 0.9em;
}

.order-status {
    font-weight: bold;
}

.booking-item {
    background: #f9f9f9;
    border-radius: 4px;
    margin-bottom: 15px;
    padding: 15px;
}

.booking-item h4 {
    color: #1565c0;
    margin: 0 0 10px 0;
}

.trip-dates {
    display: flex;
    gap: 20px;
    margin: 10px 0;
    color: #666;
}

.requirements {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.requirements h5 {
    color: #2e7d32;
    margin: 0 0 10px 0;
}

.requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirements li {
    margin-bottom: 5px;
}

.cost {
    font-weight: bold;
    color: #2e7d32;
    margin-top: 10px;
}
</style>
