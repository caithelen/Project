<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/Database.php';
require_once 'header.php';

try {
    $db = DatabaseConnection::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->query("SELECT * FROM trips WHERE DATE(collection_time) = CURDATE() ORDER BY collection_time ASC");
    $todayTrips = $stmt->fetchAll();
} catch (Exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    die('Sorry, there was a problem connecting to the database. Please try again later.');
}
?>

<div class="container">
    <h2>Live Trip Updates</h2>
    
    <div class="realtime-grid">
        <?php if (!empty($todayTrips)): ?>
            <?php foreach ($todayTrips as $trip): ?>
                <div class="realtime-card">
                    <div class="realtime-header">
                        <h3><?= htmlspecialchars($trip['destination']) ?></h3>
                        <span class="status-badge <?= strtotime($trip['collection_time']) < time() ? 'departed' : 'scheduled' ?>">
                            <?= strtotime($trip['collection_time']) < time() ? 'Departed' : 'Scheduled' ?>
                        </span>
                    </div>
                    <div class="realtime-times">
                        <div class="time-block">
                            <span class="time-label">Departure:</span>
                            <span class="time"><?= date('g:i A', strtotime($trip['collection_time'])) ?></span>
                        </div>
                        <div class="time-block">
                            <span class="time-label">Expected Arrival:</span>
                            <span class="time"><?= date('g:i A', strtotime($trip['arrival_time'])) ?></span>
                        </div>
                    </div>
                    <div class="realtime-updates">
                        <h4>Status Updates</h4>
                        <div class="update-feed" id="updates-<?= $trip['trip_id'] ?>">
                            Loading updates...
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-trips">No trips scheduled for today.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function fetchUpdates() {
    const updateFeeds = document.querySelectorAll('.update-feed');
    updateFeeds.forEach(feed => {
        const tripId = feed.id.split('-')[1];
        fetch(`api/updates.php?trip_id=${tripId}`)
            .then(response => response.json())
            .then(data => {
                if (data.updates && data.updates.length > 0) {
                    feed.innerHTML = data.updates.map(update => `
                        <div class="update-item">
                            <span class="update-time">${update.time}</span>
                            <span class="update-text">${update.message}</span>
                        </div>
                    `).join('');
                } else {
                    feed.innerHTML = '<p>No updates available.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching updates:', error);
                feed.innerHTML = '<p>Failed to load updates.</p>';
            });
    });
}

// Fetch updates every 30 seconds
fetchUpdates();
setInterval(fetchUpdates, 30000);
</script>

</body>
</html>
