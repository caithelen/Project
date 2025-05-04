<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <h1>Live Trip Schedule</h1>
    
    <div class="row" id="live-updates">
        <!-- Live updates will be inserted here -->
    </div>
</div>

<template id="trip-template">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 trip-title"></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Destination:</strong> <span class="trip-destination"></span>
                </div>
                <div class="mb-3">
                    <strong>Current Location:</strong> <span class="trip-location"></span>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong> <span class="trip-status"></span>
                </div>
                <div class="mb-3">
                    <strong>Estimated Arrival:</strong> <span class="trip-arrival"></span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
function updateTripDisplay(trips) {
    const container = document.getElementById('live-updates');
    const template = document.getElementById('trip-template');
    
    // Clear existing content
    container.innerHTML = '';
    
    if (trips.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info">No active trips at the moment.</div></div>';
        return;
    }
    
    trips.forEach(trip => {
        const clone = template.content.cloneNode(true);
        
        // Update trip information
        clone.querySelector('.trip-title').textContent = trip.title;
        clone.querySelector('.trip-destination').textContent = trip.destination;
        clone.querySelector('.trip-location').textContent = trip.current_location;
        clone.querySelector('.trip-status').textContent = trip.status;
        clone.querySelector('.trip-arrival').textContent = trip.estimated_arrival;
        
        // Update progress bar
        const progressBar = clone.querySelector('.progress-bar');
        progressBar.style.width = `${trip.progress}%`;
        progressBar.setAttribute('aria-valuenow', trip.progress);
        progressBar.textContent = `${trip.progress}%`;
        
        container.appendChild(clone);
    });
}

function fetchLiveUpdates() {
    fetch('/schedule/live-updates', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTripDisplay(data.updates);
        }
    })
    .catch(error => console.error('Error fetching updates:', error));
}

// Update every 30 seconds
document.addEventListener('DOMContentLoaded', function() {
    fetchLiveUpdates();
    setInterval(fetchLiveUpdates, 30000);
});
</script>

<style>
.progress {
    height: 25px;
    margin-top: 10px;
}

.progress-bar {
    font-size: 14px;
    line-height: 25px;
}

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
