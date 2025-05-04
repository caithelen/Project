<?php
class Booking {
    private $customer;  // Aggregation
    private $trip;      // Composition
    private $bookingDate;

    public function __construct(Customer $customer, TourTrip $trip) {
        $this->customer = $customer;
        $this->trip = $trip;
        $this->bookingDate = date("Y-m-d");
    }

    public function getSummary() {
        return "
            <div class='booking-summary'>
                <h3>Booking Summary</h3>
                <p><strong>Customer:</strong> " . htmlspecialchars($this->customer->getUsername()) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($this->customer->getEmail()) . "</p>
                <p><strong>Address:</strong> " . htmlspecialchars($this->customer->getAddress()) . "</p>
                <p><strong>Destination:</strong> " . htmlspecialchars($this->trip->getDestination()) . "</p>
                <p><strong>Description:</strong> " . htmlspecialchars($this->trip->getDescription()) . "</p>
                <p><strong>Cost:</strong> $". number_format($this->trip->getCost(), 2) ."</p>
                <p><strong>Booking Date:</strong> " . $this->bookingDate . "</p>
            </div>
        ";
    }
}
