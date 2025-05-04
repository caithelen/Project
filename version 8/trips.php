<?php
$pdo = new PDO("mysql:host=localhost;dbname=euro", "root", "Ehw2019!");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch trips from database
$stmt = $pdo->query("SELECT trip_id, destination, description, cost, image FROM trips ORDER BY trip_id ASC");
$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
