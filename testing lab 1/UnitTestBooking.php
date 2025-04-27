<?php
require_once 'Booking.php';

// Create test data
$customer = new Customer("JohnDoe", "johndoe@example.com", "123 Main Street, NY");
$trip = new TourTrip("Hawaii", 1500.00);

// Create a Booking instance
$booking = new Booking($customer, $trip);

// Run test and display the results
echo "<h2>Testing Booking Summary:</h2>";
echo "<hr>";
echo $booking->getSummary();
echo "<hr>";
?>
