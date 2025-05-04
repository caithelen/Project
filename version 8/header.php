<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize user session if not set
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    $_SESSION['user'] = [
        'username' => null,
        'role' => 'guest'
    ];
}

// Ensure all required keys exist
if (!isset($_SESSION['user']['username'])) {
    $_SESSION['user']['username'] = null;
}
if (!isset($_SESSION['user']['role'])) {
    $_SESSION['user']['role'] = 'guest';
}
?>
<header>
    <div class="header-content">
        <div class="header-left">
            <div class="menu-icon">&#9776;</div>
            <a href="./" class="logo">EUROTOURS</a>
        </div>
        <nav>
            <a href="./">Home</a>
            <a href="shop.php">Shop</a>
            <a href="cart.php">Cart</a>
            <a href="schedule.php">Schedule</a>
            <a href="live_updates.php">Live Updates</a>
            <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['username'])): ?>
                <a href="my_bookings.php">My Bookings</a>
            <?php endif; ?>
            <a href="support.php">Support</a>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                <a href="admin/trips.php">Manage Trips</a>
                <a href="admin/bookings.php">Manage Bookings</a>
                <a href="admin/users.php">Users</a>
            <?php endif; ?>
        </nav>
        <div class="header-right">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search trips">
                <div id="live-results" class="search-results"></div>
            </div>
            <a href="cart.php" class="cart-icon">
                &#128722;
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="cart-count"><?= count($_SESSION['cart']) ?></span>
                <?php endif; ?>
            </a>
            <?php if (!empty($_SESSION['user']['username'])): ?>
                <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['user']['username']); ?>!</span>
                <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="admin-btn">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="login-btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register_new.php" class="login-btn">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Live Search Script -->
<script>
    document.querySelector('.search-bar').addEventListener('keyup', function () {
        const query = this.value;
        if (query.length < 2) {
            document.getElementById('live-results').style.display = 'none';
            return;
        }

        fetch('live_search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                const results = document.getElementById('live-results');
                if (data.length > 0) {
                    results.innerHTML = data.map(trip => `
                        <div class="search-result">
                            <a href="trip.php?id=${trip.trip_id}">
                                <div class="result-header">
                                    <span class="result-title">${trip.destination}</span>
                                    ${trip.popularity ? `<span class="popularity-badge">${trip.popularity}</span>` : ''}
                                </div>
                                <div class="result-details">
                                    <div class="result-dates">
                                        <span>${trip.departure_date} - ${trip.duration} days</span>
                                    </div>
                                    <div class="result-price">
                                        €${parseFloat(trip.cost).toFixed(2)}
                                    </div>
                                </div>
                                <div class="result-description">
                                    ${trip.description ? trip.description.substring(0, 100) + '...' : ''}
                                </div>
                                <div class="result-seats">
                                    ${trip.available_seats} seats available
                                </div>
                            </a>
                        </div>
                    `).join('');
                    results.style.display = 'block';
                } else {
                    results.innerHTML = '<div class="no-results">No trips found</div>';
                    results.style.display = 'block';
                }
            })
            .catch(() => {
                document.getElementById('live-results').style.display = 'none';
            });
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-bar')) {
            document.getElementById('live-results').style.display = 'none';
        }
    });
</script>
