<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Available Trips</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="trips-grid">
        <?php foreach ($trips as $trip): ?>
            <div class="trip-card">
                <img src="<?php echo BASE_URL; ?>/images/trips/<?php echo htmlspecialchars($trip['image'] ?? 'default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($trip['destination']); ?>">
                <div class="p-3">
                    <h3><?php echo htmlspecialchars($trip['title']); ?></h3>
                    <p><?php echo htmlspecialchars($trip['description']); ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h4">€<?php echo number_format($trip['price'], 2); ?></span>
                        <div>
                            <form action="<?php echo BASE_URL; ?>/cart/add" method="POST" class="d-inline">
                                <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                                <input type="hidden" name="destination" value="<?php echo htmlspecialchars($trip['destination']); ?>">
                                <input type="hidden" name="cost" value="<?php echo $trip['price']; ?>">
                                <button type="submit" class="btn add-to-cart-btn">Add to Cart</button>
                            </form>
                            <a href="<?php echo BASE_URL; ?>/shop/view/<?php echo $trip['id']; ?>" class="btn book-now-btn">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
