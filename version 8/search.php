<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=euro", "root", "Ehw2019!");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    $stmt = $pdo->prepare("SELECT * FROM trips WHERE destination LIKE ? OR description LIKE ?");
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - EuroTours</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Search Results for "<?= htmlspecialchars($query) ?>"</h2>

    <?php if (!empty($results)): ?>
        <div class="product-list">
            <?php foreach ($results as $trip): ?>
                <div class="product">
                    <?php if (!empty($trip['image'])): ?>
                        <img src="<?= htmlspecialchars($trip['image']); ?>" alt="<?= htmlspecialchars($trip['destination']); ?>" style="width:100%; height:150px; object-fit:cover; border-radius:5px;">
                    <?php endif; ?>
                    <p><strong><?= htmlspecialchars($trip['destination']); ?></strong></p>
                    <p><?= htmlspecialchars($trip['description']); ?></p>
                    <p>Price: $<?= number_format($trip['cost'], 2); ?></p>

                    <form method="POST" action="shop.php" style="display:inline;">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id']; ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>

                    <form method="POST" action="payment.php" style="display:inline;">
                        <input type="hidden" name="trip_ids[]" value="<?= $trip['trip_id']; ?>">
                        <button type="submit" name="book_now">Book Now</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No trips matched your search.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>
</body>
</html>
