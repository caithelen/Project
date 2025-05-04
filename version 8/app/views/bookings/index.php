<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>My Bookings</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo htmlspecialchars($_SESSION['success']);
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="alert alert-info">
            You don't have any bookings yet. <a href="/shop">Browse our trips</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($bookings as $booking): ?>
                <div class="col-md-6 mb-4">
                    <div class="card booking-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($booking['title']); ?></h5>
                            <span class="badge <?php echo $booking['status'] === 'confirmed' ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p><strong>Destination:</strong> <?php echo htmlspecialchars($booking['destination']); ?></p>
                            <p><strong>Departure:</strong> <?php echo date('F j, Y', strtotime($booking['departure_date'])); ?></p>
                            <p><strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking['confirmation_code']); ?></p>
                            <p><strong>Amount Paid:</strong> €<?php echo number_format($booking['total_amount'], 2); ?></p>
                            
                            <?php if ($booking['status'] === 'confirmed'): ?>
                                <?php 
                                $departureTime = strtotime($booking['departure_date']);
                                $canCancel = time() <= $departureTime - (24 * 60 * 60);
                                ?>
                                <?php if ($canCancel): ?>
                                    <form action="/bookings/cancel" method="POST" class="cancel-booking-form">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-danger cancel-booking">
                                            Cancel Booking
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        Cancellation is not available within 24 hours of departure
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelForms = document.querySelectorAll('.cancel-booking-form');
    
    cancelForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to cancel this booking?')) {
                return;
            }

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the parent booking card and update its status
                    const bookingCard = form.closest('.booking-card');
                    const statusBadge = bookingCard.querySelector('.badge');
                    statusBadge.classList.remove('bg-success');
                    statusBadge.classList.add('bg-danger');
                    statusBadge.textContent = 'Cancelled';
                    
                    // Remove the cancel button
                    form.remove();
                    
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.textContent = data.message;
                    bookingCard.querySelector('.card-body').appendChild(alert);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the booking. Please try again.');
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
