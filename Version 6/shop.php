<?php
include 'trips.php'; // Handles DB connection & query
include 'TourTrip.php'; // Your new OOP Trip class

$tripObjects = [];

// Convert each trip from the DB into a TourTrip object
foreach ($trips as $data) {
    $tripObj = new TourTrip(
        $data['destination'],
        $data['description'],
        $data['cost'],
        $data['image']
    );
    
    // Optional: apply discounts if needed
    // if ($tripObj->getDestination() === 'Paris') {
    //     $tripObj->applyDiscount(10); // Example: 10% discount on Paris trips
    // }

    $tripObjects[] = $tripObj;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Euro Tour</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<header>
        <div class="header-left">
            <div class="menu-icon">&#9776;</div>
            <div class="logo">EuroTours</div>
        </div>
        <div class="header-right">
            <div class="cart-icon">&#128722;</div>
            <button class="login-btn">Login/Signup</button>
            <input type="text" class="search-bar" placeholder="Search">
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav>
        <a href="homepage.html">Home</a>
        <a href="Product.html">Product</a>
        <a href="shop.php">Shop</a>
        <a href="UserLogin.html">Login/Register</a>
        <a href="RealTimeUpdates.html">RealTime Updates</a>
        <a href="BookingManagment.html">Booking Management</a>
        <a href="Schedule.html">Schedule</a>
        <a href="CustomerSupport.html">Customer Support</a>
    </nav>

    <div class="container">
        <h1>Shop Our Trips</h1>

        <!-- Search Bar -->
        <div class="search-container">
            <form action="shop.php" method="GET">
                <input type="text" name="query" placeholder="Enter destination or keyword..." value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>">
                <button type="submit">Search</button>
            </form>

            <?php if (isset($_GET['query']) && $_GET['query'] != ''): ?>
                <p>Showing results for: <strong><?= htmlspecialchars($_GET['query']); ?></strong></p>
            <?php endif; ?>
        </div>

        <!-- Trip Cards (OOP-based) -->
        <div class="product-list">
            <?php if (!empty($tripObjects)): ?>
                <?php foreach ($tripObjects as $trip): ?>
                    <div class="product">
                        <?php if (!empty($trip->getImage())): ?>
                            <img src="<?= htmlspecialchars($trip->getImage()); ?>" alt="<?= htmlspecialchars($trip->getDestination()); ?>" style="width:100%; height: 150px; object-fit: cover; border-radius: 5px;">
                        <?php endif; ?>
                        <p><strong><?= htmlspecialchars($trip->getDestination()); ?></strong></p>
                        <p><?= htmlspecialchars($trip->getDescription()); ?></p>
                        <p>Price: $<?= number_format($trip->getCost(), 2); ?></p>
                        <a href="booking.php?destination=<?= urlencode($trip->getDestination()); ?>" class="btn">Book Now</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No trips found for your search.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
    </footer>
</body>

</html>
