<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <img src="/images/trips/<?php echo htmlspecialchars($trip['image'] ?? 'default.jpg'); ?>" 
                 alt="<?php echo htmlspecialchars($trip['destination']); ?>"
                 class="img-fluid rounded">
        </div>
        <div class="col-md-6">
            <h1><?php echo htmlspecialchars($trip['title']); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($trip['description']); ?></p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Trip Details</h5>
                    <ul class="list-unstyled">
                        <li><strong>Destination:</strong> <?php echo htmlspecialchars($trip['destination']); ?></li>
                        <li><strong>Duration:</strong> <?php echo htmlspecialchars($trip['duration']); ?> days</li>
                        <li><strong>Departure:</strong> <?php echo date('F j, Y', strtotime($trip['departure_date'])); ?></li>
                        <li><strong>Price:</strong> €<?php echo number_format($trip['price'], 2); ?></li>
                        <li>
                            <strong>Availability:</strong> 
                            <?php 
                            $spotsLeft = $trip['max_participants'] - $trip['booked'];
                            echo $spotsLeft . ' spots left';
                            ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-grid gap-2">
                <form action="/cart/add" method="POST">
                    <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                    <input type="hidden" name="destination" value="<?php echo htmlspecialchars($trip['destination']); ?>">
                    <input type="hidden" name="cost" value="<?php echo $trip['price']; ?>">
                    <button type="submit" class="btn add-to-cart-btn btn-lg w-100 mb-2">Add to Cart</button>
                </form>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/checkout/trip/<?php echo $trip['id']; ?>" class="btn book-now-btn btn-lg w-100">Book Now</a>
                <?php else: ?>
                    <a href="/login" class="btn book-now-btn btn-lg w-100">Login to Book</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
