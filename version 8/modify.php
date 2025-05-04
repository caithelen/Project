<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=euro", "root", "Ehw2019!");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $bookingID = $_POST['booking_id'];
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ? AND customer_id = ?");
    $stmt->execute([$bookingID, $_SESSION['user_id']]);
}

// Get user's bookings
$stmt = $pdo->prepare("
    SELECT b.booking_id, t.destination, t.description, b.booking_date, b.status 
    FROM bookings b
    JOIN trips t ON b.trip_id = t.trip_id
    WHERE b.customer_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - EuroTours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>My Bookings</h2>
    <?php if ($bookings): ?>
        <table border="1" width="100%">
            <tr>
                <th>Booking ID</th>
                <th>Destination</th>
                <th>Description</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['booking_id']; ?></td>
                    <td><?= htmlspecialchars($booking['destination']); ?></td>
                    <td><?= htmlspecialchars($booking['description']); ?></td>
                    <td><?= $booking['booking_date']; ?></td>
                    <td><?= $booking['status']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                            <button type="submit" name="cancel">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You have no bookings yet.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>
</body>
</html>
