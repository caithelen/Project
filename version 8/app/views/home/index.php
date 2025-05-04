<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="jumbotron bg-light p-5 rounded">
        <h1 class="display-4">Welcome to EuroTours</h1>
        <p class="lead">Discover Europe's rich history and culture with our guided tours.</p>
        <hr class="my-4">
        <p>We offer unforgettable experiences across the continent's most beautiful destinations.</p>
        <a class="btn btn-success btn-lg" href="<?php echo BASE_URL; ?>/shop" role="button">Browse Tours</a>
    </div>

    <h2 class="mt-5 mb-4">Featured Tours</h2>
    <div class="row">
        <?php if (!empty($featuredTrips)) foreach ($featuredTrips as $trip): ?>
            <div class="col-md-4 mb-4">
                <div class="card trip-card">
                    <img src="<?php echo BASE_URL; ?>/images/trips/<?php echo htmlspecialchars($trip['image'] ?? 'default.jpg'); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($trip['destination']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($trip['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($trip['description']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">€<?php echo number_format($trip['price'], 2); ?></span>
                            <a href="<?php echo BASE_URL; ?>/shop" class="btn book-now-btn">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row mt-5">
        <div class="col-md-4">
            <h3>Why Choose Us?</h3>
            <ul class="list-unstyled">
                <li>✓ Expert local guides</li>
                <li>✓ Small group sizes</li>
                <li>✓ Authentic experiences</li>
                <li>✓ Best price guarantee</li>
            </ul>
        </div>
        <div class="col-md-4">
            <h3>Popular Destinations</h3>
            <ul class="list-unstyled">
                <li>→ Paris, France</li>
                <li>→ Rome, Italy</li>
                <li>→ Barcelona, Spain</li>
                <li>→ Amsterdam, Netherlands</li>
            </ul>
        </div>
        <div class="col-md-4">
            <h3>Customer Support</h3>
            <p>Need help planning your trip?</p>
            <p>Email: info@eurotours.com</p>
            <p>Phone: +1 (555) 123-4567</p>
            <p>Available 24/7</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
