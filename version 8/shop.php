<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/Database.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Sorry, there was a problem connecting to the database. Please try again later.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
    <h2>Available Trips</h2>

    <style>
    .trips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }

    .trip-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s;
    }

    .trip-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .trip-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .trip-details {
        padding: 15px;
    }

    .trip-details h3 {
        margin: 0 0 10px;
        color: #2e4a2e;
    }

    .trip-description {
        color: #666;
        margin-bottom: 15px;
    }

    .trip-times {
        color: #2e7d32;
        margin-bottom: 15px;
    }

    .trip-price {
        font-size: 1.2em;
        color: #1565c0;
        margin-bottom: 15px;
    }

    .trip-actions {
        display: flex;
        gap: 10px;
    }

    .add-to-cart-btn {
        background: #1565c0;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        flex: 1;
    }

    .add-to-cart-btn:hover {
        background: #0d47a1;
    }

    .book-now-btn {
        background: #2e7d32;
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        text-align: center;
        flex: 1;
    }

    .book-now-btn:hover {
        background: #1b5e20;
    }
    </style>
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['cart_message']); ?>
            <?php unset($_SESSION['cart_message']); ?>
        </div>
    <?php endif; ?>

    <div class="trips-grid">
    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM trips ORDER BY cost ASC");
        while ($trip = $stmt->fetch()) {
        ?>
        <div class="trip-card">
            <div class="trip-image">
                <?php
                    $seed = abs(crc32($trip['destination'])) % 1000; // Generate a consistent seed for each destination
                ?>
                <img src="https://picsum.photos/seed/<?= $seed ?>/800/600" 
                     alt="<?= htmlspecialchars($trip['destination']) ?>">
            </div>
            <div class="trip-details">
                <h3><?= htmlspecialchars($trip['destination']) ?></h3>
                <p class="trip-description"><?= htmlspecialchars($trip['description']) ?></p>
                <div class="trip-times">
                    <p>Collection: <?= isset($trip['collection_time']) && $trip['collection_time'] ? date('g:i A', strtotime($trip['collection_time'])) : 'TBA' ?></p>
                    <p>Arrival: <?= isset($trip['arrival_time']) && $trip['arrival_time'] ? date('g:i A', strtotime($trip['arrival_time'])) : 'TBA' ?></p>
                </div>
                <div class="trip-price">
                    <span class="price-label">Price:</span>
                    <span class="price-amount">€<?= number_format($trip['cost'], 2) ?></span>
                </div>
                <div class="trip-actions">
                    <form action="cart.php" method="post">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                        <input type="hidden" name="destination" value="<?= htmlspecialchars($trip['destination']) ?>">
                        <input type="hidden" name="cost" value="<?= $trip['cost'] ?>">
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                            <span class="cart-icon">🛒</span> Add to Cart
                        </button>
                    </form>
                    <a href="payment.php?trip_id=<?= $trip['trip_id'] ?>&cost=<?= $trip['cost'] ?>" class="book-now-btn" <?= $trip['available_seats'] > 0 ? '' : 'style="pointer-events: none; opacity: 0.5;"' ?>><?= $trip['available_seats'] > 0 ? 'Book Now' : 'Sold Out' ?></a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
</div>
<?php
} catch (PDOException $e) {
    error_log('Database query failed: ' . $e->getMessage());
    echo '<div class="error-message">Sorry, we could not load the trips at this time. Please try again later.</div>';
}
?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>




