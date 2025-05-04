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
    
    // Get user's booked trips with simulated updates
    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            t.destination,
            t.departure_date,
            t.return_date,
            t.available_seats,
            t.description,
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
    $updates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Updates - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .updates-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .update-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .update-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .update-destination {
            font-size: 20px;
            font-weight: bold;
            color: #2e4a2e;
        }
        .update-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .update-alert {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .update-alert.weather {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }
        .update-alert.event {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }
        .update-time {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .no-updates {
            text-align: center;
            padding: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="updates-container">
        <h1>Live Updates</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (empty($updates)): ?>
            <div class="no-updates">
                <h2>No updates available</h2>
                <p>You haven't booked any trips yet. <a href="shop.php">Browse our trips</a> to get started!</p>
            </div>
        <?php else: ?>
            <?php foreach ($updates as $update): ?>
                <div class="update-card">
                    <div class="update-header">
                        <div class="update-destination">
                            <?= htmlspecialchars($update['destination']) ?>
                        </div>
                        <div>
                            Order #<?= htmlspecialchars($update['order_id']) ?>
                            <span class="trip-status status-<?= strtolower($update['status']) ?>">
                                <?= ucfirst($update['status']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="update-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?= ceil((strtotime($update['departure_date']) - time()) / (60*60*24)) ?></div>
                            <div class="stat-label">Days Until Departure</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= ceil((strtotime($update['return_date']) - strtotime($update['departure_date'])) / (60*60*24)) ?></div>
                            <div class="stat-label">Trip Duration</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">€<?= number_format($update['price'], 2) ?></div>
                            <div class="stat-label">Trip Price</div>
                        </div>
                    </div>

                    <?php
                    // Simulate random updates based on the destination
                    $updates = [
                        ['type' => 'weather', 'chance' => 0.7],
                        ['type' => 'event', 'chance' => 0.5],
                        ['type' => 'alert', 'chance' => 0.3]
                    ];

                    foreach ($updates as $type) {
                        if (mt_rand() / mt_getrandmax() <= $type['chance']) {
                            switch ($type['type']) {
                                case 'weather':
                                    $temps = range(15, 30);
                                    $temp = $temps[array_rand($temps)];
                                    $conditions = ['Sunny', 'Partly Cloudy', 'Clear Skies', 'Light Rain'];
                                    $condition = $conditions[array_rand($conditions)];
                                    echo "<div class='update-alert weather'>";
                                    echo "<strong>Weather Update:</strong> ";
                                    echo "Expected {$condition} with temperatures around {$temp}°C during your stay.";
                                    echo "<div class='update-time'>Updated " . date('g:i A') . "</div>";
                                    echo "</div>";
                                    break;

                                case 'event':
                                    $events = [
                                        'Local food festival happening during your stay!',
                                        'Cultural exhibition opening at the city museum.',
                                        'Traditional music performance in the main square.',
                                        'Night market with local artisans.',
                                        'Guided historical walking tour available.'
                                    ];
                                    echo "<div class='update-alert event'>";
                                    echo "<strong>Local Event:</strong> ";
                                    echo $events[array_rand($events)];
                                    echo "<div class='update-time'>Updated " . date('g:i A') . "</div>";
                                    echo "</div>";
                                    break;

                                case 'alert':
                                    $alerts = [
                                        'Remember to bring comfortable walking shoes!',
                                        'Don\'t forget to check your passport expiration date.',
                                        'Local currency exchange rates are favorable today.',
                                        'Popular attractions might require advance booking.',
                                        'Public transportation passes are recommended.'
                                    ];
                                    echo "<div class='update-alert'>";
                                    echo "<strong>Travel Tip:</strong> ";
                                    echo $alerts[array_rand($alerts)];
                                    echo "<div class='update-time'>Updated " . date('g:i A') . "</div>";
                                    echo "</div>";
                                    break;
                            }
                        }
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
