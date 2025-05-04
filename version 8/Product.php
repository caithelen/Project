<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EuroTours - Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Available Travel Products</h2>
    <p>Discover our exclusive trip packages, travel gear, and comfort upgrades.</p>

    <div class="product">
        <h3>City-to-City Pass</h3>
        <p>Unlimited travel across 5 cities in 10 days.</p>
        <p>Price: €199</p>
    </div>

    <div class="product">
        <h3>Luxury Seat Upgrade</h3>
        <p>Extra legroom, USB charging, and recliner seats.</p>
        <p>Price: €20 per trip</p>
    </div>

    <div class="product">
        <h3>EuroTours Travel Kit</h3>
        <p>Includes neck pillow, eye mask, and water bottle.</p>
        <p>Price: €15</p>
    </div>

    <?php if (isset($_SESSION['username'])): ?>
        <p style="color: green; font-weight: bold;">Thanks for being a valued EuroTours traveler, <?= htmlspecialchars($_SESSION['username']); ?>!</p>
    <?php else: ?>
        <p><a href="login.php">Login</a> to view exclusive member discounts!</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>
</body>
</html>
