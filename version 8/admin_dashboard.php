<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="admin-info">
        <h2>Admin Information</h2>
        <p>Username: <?= htmlspecialchars($_SESSION['user']['username']); ?></p>
        <p>Role: <?= htmlspecialchars($_SESSION['user']['role']); ?></p>
        <p>Department: <?= htmlspecialchars($_SESSION['user']['department'] ?? 'N/A'); ?></p>
        <p>Access Level: <?= htmlspecialchars($_SESSION['user']['access_level'] ?? 1); ?></p>
    </div>

    <div class="admin-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="admin/users.php" class="btn">Manage Users</a>
            <a href="admin/bookings.php" class="btn">View All Bookings</a>
            <a href="admin/trips.php" class="btn">Manage Trips</a>
            <a href="admin/reports.php" class="btn">View Reports</a>
            <?php if (($_SESSION['user']['access_level'] ?? 1) >= 2): ?>
                <a href="admin/admins.php" class="btn">Manage Admins</a>
                <a href="admin/settings.php" class="btn">System Settings</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="recent-activity">
        <h2>Recent Activity</h2>
        <p>Coming soon...</p>
    </div>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>

</body>
</html>
