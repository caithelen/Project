<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Bus Updates - EuroTours</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .status-On\ Time { color: green; }
    .status-En\ Route { color: orange; }
    .status-Arrived { color: blue; }

    .status-badge {
      padding: 5px 10px;
      border-radius: 5px;
      font-weight: bold;
      display: inline-block;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
  <h2>Your Bus Updates</h2>
  <div id="status-table">
    <!-- AJAX loads here -->
  </div>
</div>

<script>
function loadTripStatus() {
  fetch('bus_status_ajax.php')
    .then(res => res.text())
    .then(html => {
      document.getElementById('status-table').innerHTML = html;
    });
}
loadTripStatus();
setInterval(loadTripStatus, 15000); // Refresh every 15 seconds
</script>

<footer>
  <p>&copy; <?= date("Y"); ?> EuroTours. All rights reserved.</p>
</footer>
</body>
</html>

