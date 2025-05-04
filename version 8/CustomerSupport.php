<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - EuroTours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Customer Support</h2>
    <p>We’re here to help you with any questions or concerns about your trip.</p>

    <?php if (isset($_SESSION['username'])): ?>
        <p>Hi <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>, how can we assist you today?</p>
    <?php else: ?>
        <p>Please <a href="login.php">log in</a> to access personalized support.</p>
    <?php endif; ?>

    <form action="#" method="POST">
        <label for="email">Your Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="message">Your Message:</label><br>
        <textarea id="message" name="message" rows="6" required></textarea><br><br>

        <button type="submit">Send Message</button>
    </form>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>
</body>
</html>
