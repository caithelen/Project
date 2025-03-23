<?php

// User Class (Parent)
class User {
    protected $username;
    protected $email;

    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
    }

    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
}

// Customer Class (Child of User)
class Customer extends User {
    private $address;

    public function __construct($username, $email, $address) {
        parent::__construct($username, $email);
        $this->address = $address;
    }

    public function getAddress() { return $this->address; }
}

// Booking Class (Aggregation + Composition)
class Booking {
    private $customer; // Aggregation (Customer exists independently)
    private $trip;     // Composition (Booking "owns" the trip)

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
