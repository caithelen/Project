<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation - EuroTours</title>
    <link rel="stylesheet" href="style.css"> <!-- ✅ Link to your stylesheet -->
</head>
<body>

<?php include 'header.php'; ?>

<?php
$host = 'localhost';
$db = 'euro';
$user = 'root';
$pass = 'Ehw2019!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die("<div class='container'>Database connection failed: " . $e->getMessage() . "</div>");
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || empty($_POST['departure_date'])) {
    echo "<div class='container'><p>Your cart was empty or no departure date was selected.</p></div>";
    exit;
}

$customer_id = $_SESSION['user_id'] ?? null;
if (!$customer_id) {
    echo "<div class='container'><p>You must be logged in to book. <a href='login.php'>Login</a></p></div>";
    exit;
}

$departure_date = $_POST['departure_date'];
$booking_date = date('Y-m-d');
$status = 'confirmed';

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, trip_id, booking_date, status, departure_date) VALUES (?, ?, ?, ?, ?)");

    foreach ($_SESSION['cart'] as $trip) {
        $stmt->execute([
            $customer_id,
            $trip['trip_id'],
            $booking_date,
            $status,
            $departure_date
        ]);
    }

    $pdo->commit();
    $_SESSION['cart'] = [];

    echo "<div class='container'><h2>Booking Confirmed!</h2><p>Your trip(s) have been successfully booked. Thank you for choosing EuroTours!</p></div>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<div class='container'><p>Error processing booking: " . $e->getMessage() . "</p></div>";
}
?>

<footer>
  <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>

</body>
</html>

