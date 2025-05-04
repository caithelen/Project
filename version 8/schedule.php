<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Get user's booked trips
    $stmt = $pdo->prepare("
        SELECT 
            t.*,
            o.status,
            o.order_id,
            oi.price
        FROM trips t
        JOIN order_items oi ON t.trip_id = oi.trip_id
        JOIN eurotours_orders o ON oi.order_id = o.order_id
        WHERE o.user_id = ? AND o.status != 'cancelled'
        ORDER BY t.departure_date ASC
    ");
    $stmt->execute([$_SESSION['user']['user_id']]);
    $bookedTrips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get upcoming trips (not booked by user)
    $stmt = $pdo->prepare("
        SELECT t.*
        FROM trips t
        WHERE t.departure_date > NOW()
        AND t.available_seats > 0
        AND t.trip_id NOT IN (
            SELECT oi.trip_id 
            FROM order_items oi
            JOIN eurotours_orders o ON oi.order_id = o.order_id
            WHERE o.user_id = ? AND o.status != 'cancelled'
        )
        ORDER BY t.departure_date ASC
        LIMIT 6
    ");
    $stmt->execute([$_SESSION['user']['user_id']]);
    $upcomingTrips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .schedule-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .schedule-section {
            margin-bottom: 40px;
        }
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .trip-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .trip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .trip-image {
            height: 200px;
            overflow: hidden;
            position: relative;
            background: #f0f0f0;
        }
        .trip-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .trip-content {
            padding: 20px;
        }
        .trip-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .trip-title {
            font-size: 20px;
            color: #2e4a2e;
            margin: 0;
        }
        .trip-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-confirmed {
            background: #4CAF50;
            color: white;
        }
        .status-pending {
            background: #FFC107;
            color: black;
        }
        .status-cancelled {
            background: #F44336;
            color: white;
        }
        .trip-info {
            display: grid;
            gap: 10px;
            color: #666;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-icon {
            color: #2e7d32;
        }
        .trip-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .trip-price {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
        }
        .section-title {
            color: #2e4a2e;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .no-trips {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            color: #666;
        }

        .trip-info p {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .trip-info .icon {
            font-size: 18px;
            color: #1565c0;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="schedule-container">
        <div class="schedule-section">
            <h2 class="section-title">Your Booked Trips</h2>
            
            <?php if (empty($bookedTrips)): ?>
                <div class="no-trips">
                    <h3>No booked trips</h3>
                    <p>You haven't booked any trips yet. Check out our upcoming trips below!</p>
                </div>
            <?php else: ?>
                <div class="schedule-grid">
                    <?php foreach ($bookedTrips as $trip): ?>
                        <div class="trip-card">
                            <div class="trip-image">
                                <img src="https://picsum.photos/seed/<?= urlencode($trip['destination']) ?>/800/600" 
                                     alt="<?= htmlspecialchars($trip['destination']) ?>">
                            </div>
                            <div class="trip-content">
                                <div class="trip-header">
                                    <h3 class="trip-title"><?= htmlspecialchars($trip['destination']) ?></h3>
                                    <span class="trip-status status-<?= strtolower($trip['status']) ?>">
                                        <?= ucfirst($trip['status']) ?>
                                    </span>
                                </div>
                                <div class="trip-info">
                                    <div class="info-item">
                                        <span class="info-icon">📅</span>
                                        <span>Departure: <?= date('M j, Y', strtotime($trip['departure_date'])) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon">🔄</span>
                                        <span>Return: <?= date('M j, Y', strtotime($trip['return_date'])) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon">🎫</span>
                                        <span>Order #<?= htmlspecialchars($trip['order_id']) ?></span>
                                    </div>
                                </div>
                                <div class="trip-footer">
                                    <div class="trip-price">
                                        €<?= number_format($trip['price'], 2) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="schedule-section">
            <h2 class="section-title">Upcoming Trips</h2>
            
            <?php if (empty($upcomingTrips)): ?>
                <div class="no-trips">
                    <h3>No upcoming trips</h3>
                    <p>Check back later for new trip schedules!</p>
                </div>
            <?php else: ?>
                <div class="schedule-grid">
                    <?php foreach ($upcomingTrips as $trip): ?>
                        <div class="trip-card">
                            <div class="trip-image">
                                <img src="https://picsum.photos/seed/<?= urlencode($trip['destination']) ?>/800/600" 
                                     alt="<?= htmlspecialchars($trip['destination']) ?>">
                            </div>
                            <div class="trip-content">
                                <div class="trip-header">
                                    <h3 class="trip-title"><?= htmlspecialchars($trip['destination']) ?></h3>
                                </div>
                                <div class="trip-info">
                                    <div class="info-item">
                                        <span class="info-icon">📅</span>
                                        <span>Departure: <?= date('M j, Y', strtotime($trip['departure_date'])) ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon">⏱️</span>
                                        <span>Duration: <?= $trip['duration'] ?? '7' ?> days</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-icon">💺</span>
                                        <span>Available Seats: <?= htmlspecialchars($trip['available_seats']) ?></span>
                                    </div>
                                </div>
                                <div class="trip-footer">
                                    <div class="trip-price">
                                        €<?= number_format($trip['cost'], 2) ?>
                                    </div>
                                    <form action="payment.php" method="post">
                                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                                        <input type="hidden" name="cost" value="<?= $trip['cost'] ?>">
                                        <button type="submit" class="book-now-btn">Book Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
