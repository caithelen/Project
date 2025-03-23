<?php
$servername = "localhost";
$username = "root";
$password = "Ehw2019!";
$dbname = "euro";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Handle booking cancellation
if (isset($_POST['cancel'])) {
    $bookingID = $_POST['bookingID'];
    $deleteSQL = "DELETE FROM bookings WHERE BookingID = ?";
    $stmt = mysqli_prepare($conn, $deleteSQL);
    mysqli_stmt_bind_param($stmt, "i", $bookingID);
    mysqli_stmt_execute($stmt);
    echo "<script>alert('Booking cancelled successfully');</script>";
    echo "<script>window.location.href='modify.php';</script>"; // Refresh page
}

// Handle booking modification (future functionality)
if (isset($_POST['modify'])) {
    echo "<script>alert('Modify feature coming soon!');</script>";
}

// Fetch bookings
$sql = "SELECT * FROM bookings";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EuroTours - Modify Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="homepage.html">Home</a></li>
            <li><a href="Product.html">Product</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="UserLogin.html">Login</a></li>
            <li><a href="Booking.html">Booking</a></li>
            <li><a href="Schedule.html">Schedule</a></li>
            <li><a href="CustomerSupport.html">Customer Support</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <h1>Modify Bookings</h1>
        <p>View and manage your bookings below.</p>

        <table border="1" width="100%">
            <tr>
                <th>Booking ID</th>
                <th>Route</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['BookingID']}</td>
                            <td>{$row['Route']}</td>
                            <td>{$row['Date']}</td>
                            <td>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='bookingID' value='{$row['BookingID']}'>
                                    <button type='submit' name='cancel'>Cancel</button>
                                </form>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='bookingID' value='{$row['BookingID']}'>
                                    <button type='submit' name='modify'>Modify</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No bookings found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
