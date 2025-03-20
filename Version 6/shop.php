<?php include 'trips.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Euro Tour</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Keeping previous product-list and product styles */
    </style>
</head>

<body>
<header>
        <div class="header-left">
            <div class="menu-icon">&#9776;</div>  <!-- Unicode for menu icon -->
            <div class="logo">EuroTours</div>
        </div>
        <div class="header-right">
            <div class="cart-icon">&#128722;</div> <!-- Unicode for cart icon -->
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
    <!-- Header Section -->
    <!-- ... your existing header/nav ... -->

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

        <!-- Dynamic Trip Listing -->
        <div class="product-list">
            <?php if (!empty($trips)): ?>
                <?php foreach ($trips as $trip): ?>
                   <div class="product">
    <?php if (!empty($trip['image'])): ?>
        <img src="<?= htmlspecialchars($trip['image']); ?>" alt="<?= htmlspecialchars($trip['destination']); ?>" style="width:100%; height: 150px; object-fit: cover; border-radius: 5px;">
    <?php endif; ?>
    <p><strong><?= htmlspecialchars($trip['destination']); ?></strong></p>
    <p><?= htmlspecialchars($trip['description']); ?></p>
    <p>Price: $<?= number_format($trip['cost'], 2); ?></p>
    <a href="booking.php?trip_id=<?= $trip['trip_id']; ?>" class="btn">Book Now</a>
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
