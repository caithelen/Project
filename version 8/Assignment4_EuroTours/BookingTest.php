<?php
use PHPUnit\Framework\TestCase;
require_once '../Booking.php';

class BookingTest extends TestCase {
    public function testBookingCreation() {
        $booking = new Booking(1, 2, '2025-05-01');
        $this->assertEquals(1, $booking->getCustomerId());
        $this->assertEquals(2, $booking->getTripId());
        $this->assertEquals('2025-05-01', $booking->getDepartureDate());
    }
}
?>
