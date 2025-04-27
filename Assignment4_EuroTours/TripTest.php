<?php
use PHPUnit\Framework\TestCase;
require_once '../TourTrip.php';

class TripTest extends TestCase {
    public function testTripDetails() {
        $trip = new TourTrip(1, 'Paris', 299.99);
        $this->assertEquals(1, $trip->getTripId());
        $this->assertEquals('Paris', $trip->getDestination());
        $this->assertEquals(299.99, $trip->getPrice());
    }
}
?>
