<?php
session_start();
include 'trips.php';
require_once 'TourTrip.php';
require_once 'User.php';
require_once 'Customer.php';
require_once 'Booking.php';


class Booking {
    private $customer;
    private $trip;

    public function __construct(Customer $customer, TourTrip $trip) {
        $this->customer = $customer;
        $this->trip = $trip;
    }

    public function getSummary() {
        return "
            Booking for: {$this->customer->getUsername()}<br>
            Destination: {$this->trip->getDestination()}<br>
            Cost: $" . number_format($this->trip->getCost(), 2) . "<br>
            Customer Address: {$this->customer->getAddress()}<br>
        ";
    }
}
?>
