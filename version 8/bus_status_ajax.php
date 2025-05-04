<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=euro", "root", "Ehw2019!");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("
  SELECT t.*, b.departure_date 
  FROM bookings b 
  JOIN trips t ON b.trip_id = t.trip_id 
  WHERE b.customer_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($trips)) {
  echo "<p>You currently have no bookings.</p>";
  exit;
}

function simulateStatus($departureDate, $collectionTime, $arrivalTime) {
  $now = new DateTime();
  $departureDateTime = new DateTime("$departureDate $collectionTime");
  $arrivalDateTime = new DateTime("$departureDate $arrivalTime");

  if ($now < $departureDateTime) {
    return 'On Time';
  } elseif ($now >= $departureDateTime && $now < $arrivalDateTime) {
    return 'En Route';
  } else {
    return 'Arrived';
  }
}
?>
<table border="1" width="100%">
  <tr>
    <th>Destination</th>
    <th>Departure Date</th>
    <th>Collection Time</th>
    <th>Arrival Time</th>
    <th>Status</th>
  </tr>
  <?php foreach ($trips as $trip): ?>
    <?php 
      $status = simulateStatus($trip['departure_date'], $trip['collection_time'], $trip['arrival_time']);
      $statusClass = str_replace(' ', '\\\\ ', $status);
    ?>
    <tr>
      <td><?= htmlspecialchars($trip['destination']) ?></td>
      <td><?= date("F j, Y", strtotime($trip['departure_date'])) ?></td>
      <td><?= date("g:i A", strtotime($trip['collection_time'])) ?></td>
      <td><?= date("g:i A", strtotime($trip['arrival_time'])) ?></td>
      <td><span class="status-badge status-<?= $statusClass ?>"><?= $status ?></span></td>
    </tr>
  <?php endforeach; ?>
</table>

