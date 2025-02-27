<?php
$servername = "localhost";
$username = "root";
$password = "Ehw2019!";
$dbname = "tours";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
} else {
    echo "Successfully Connected to Database<br/><br/>";
}

// Query to get data from the trips table
$sql = "SELECT * FROM trips";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'><tr>";
        // Fetch column names
        while ($field = mysqli_fetch_field($result)) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";

        // Fetch rows
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table><br/>";
    } else {
        echo "No data found in the trips table.<br/>";
    }
} else {
    echo "Error retrieving data: " . mysqli_error($conn);
}

// Close connection
mysqli_close($conn);
?>
