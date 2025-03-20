<?php
// trips.php: Fetch trips (optionally filtered by search)
$host = 'localhost';
$dbname = 'euro';
$username = 'root';
$password = 'Ehw2019!';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $trips = [];

    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = '%' . $_GET['query'] . '%';
        $stmt = $conn->prepare("SELECT * FROM trips WHERE destination LIKE :query OR description LIKE :query");
        $stmt->bindParam(':query', $query);
        $stmt->execute();
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Default: fetch all trips
        $stmt = $conn->prepare("SELECT * FROM trips");
        $stmt->execute();
        $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $trips = [];
}
?>
