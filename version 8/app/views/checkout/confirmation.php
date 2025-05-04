<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title text-success">
                        <i class="fas fa-check-circle"></i> Booking Confirmed!
                    </h1>
                    <p class="lead">Thank you for booking with EuroTours!</p>
                </div>
            </div>

            <?php foreach ($bookings as $booking): ?>
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0">Booking Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><?php echo htmlspecialchars($booking['title']); ?></h4>
                                <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                                <p><strong>Departure Date:</strong> <?php echo date('F j, Y', strtotime($booking['departure_date'])); ?></p>
                                <p><strong>Duration:</strong> <?php echo htmlspecialchars($booking['duration']); ?> days</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking['confirmation_code']); ?></p>
                                <p><strong>Booking Date:</strong> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                <p><strong>Amount Paid:</strong> €<?php echo number_format($booking['total_amount'], 2); ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-success">Confirmed</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="card mt-4">
                <div class="card-body">
                    <h4>What's Next?</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">✓ A confirmation email has been sent to your registered email address</li>
                        <li class="mb-2">✓ You can view your booking details anytime in your account</li>
                        <li class="mb-2">✓ Our customer support team will contact you with additional information</li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="/my-bookings" class="btn btn-primary me-2">View My Bookings</a>
                <a href="/shop" class="btn btn-success">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
