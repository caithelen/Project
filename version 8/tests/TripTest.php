<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/../TourTrip.php';

class TripTest {
    private $trip;

    private function createTestTrip() {
        return new TourTrip(
            1,                          // id
            'Paris Adventure',          // title
            'Experience the magic of Paris', // description
            499.99,                     // price
            '2025-06-01',              // startDate
            '2025-06-07',              // endDate
            20,                        // capacity
            'images/paris.jpg'          // imagePath
        );
    }

    public function __construct() {}

    public function runTests() {
        $this->testTripCreation();
        $this->testAvailabilityManagement();
        $this->testPricingRules();
        $this->testDateValidation();
        $this->testTripModification();
        $this->testViewBookedTrips();
    }

    public function testTripCreation() {
        echo "Testing Trip Creation... ";
        
        $this->trip = $this->createTestTrip();
        
        assert($this->trip->getId() === 1, "ID should match");
        assert($this->trip->getTitle() === 'Paris Adventure', "Title should match");
        assert($this->trip->getDescription() === 'Experience the magic of Paris', "Description should match");
        assert($this->trip->getPrice() === 499.99, "Price should match");
        assert($this->trip->getStartDate() === '2025-06-01', "Start date should match");
        assert($this->trip->getEndDate() === '2025-06-07', "End date should match");
        assert($this->trip->getCapacity() === 20, "Capacity should match");
        assert($this->trip->getImagePath() === 'images/paris.jpg', "Image path should match");
        
        echo "PASSED\n";
    }

    public function testAvailabilityManagement() {
        echo "Testing Availability Management... ";
        
        $this->trip = $this->createTestTrip();
        
        // Test initial availability
        assert($this->trip->getAvailableSpots() === 20, "Should start with all spots available");
        
        // Test booking spots
        $this->trip->bookSpots(5);
        assert($this->trip->getAvailableSpots() === 15, "Should have 15 spots after booking 5");
        
        // Test overbooking prevention
        try {
            $this->trip->bookSpots(20); // More than available
            assert(false, "Should throw exception for overbooking");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Insufficient available spots") === 0, "Should throw availability error");
        }
        
        // Test cancellation
        $this->trip->cancelSpots(3);
        assert($this->trip->getAvailableSpots() === 18, "Should have 18 spots after cancellation");
        
        echo "PASSED\n";
    }

    public function testPricingRules() {
        echo "Testing Pricing Rules... ";
        
        $this->trip = $this->createTestTrip();
        
        // Test early bird discount
        $earlyBirdPrice = $this->trip->calculatePrice(true, 1);
        assert($earlyBirdPrice < 499.99, "Early bird price should be discounted");
        
        // Test group discount
        $groupPrice = $this->trip->calculatePrice(false, 5);
        assert($groupPrice < (499.99 * 5), "Group price should be discounted");
        
        // Test combination of discounts
        $combinedPrice = $this->trip->calculatePrice(true, 5);
        assert($combinedPrice < $groupPrice, "Combined discounts should be lower than group price alone");
        
        // Test invalid group size
        try {
            $this->trip->calculatePrice(false, -1);
            assert(false, "Should throw exception for invalid group size");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Invalid group size") === 0, "Should throw group size error");
        }
        
        echo "PASSED\n";
    }

    public function testDateValidation() {
        echo "Testing Date Validation... ";
        
        $this->trip = $this->createTestTrip();
        
        // Test valid date range
        assert($this->trip->isValidDateRange('2025-06-01', '2025-06-07') === true, "Should accept valid date range");
        
        // Test invalid date order
        try {
            $this->trip->setDates('2025-06-07', '2025-06-01');
            assert(false, "Should throw exception for end date before start date");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "End date must be after start date") === 0, "Should throw date order error");
        }
        
        // Test past dates
        try {
            $this->trip->setDates('2020-01-01', '2020-01-07');
            assert(false, "Should throw exception for past dates");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Trip dates must be in the future") === 0, "Should throw past date error");
        }
        
        echo "PASSED\n";
    }

    public function testTripModification() {
        echo "Testing Trip Modification... ";
        
        $this->trip = $this->createTestTrip();
        
        // Test title update
        $this->trip->setTitle('Paris Explorer');
        assert($this->trip->getTitle() === 'Paris Explorer', "Title should be updated");
        
        // Test description update
        $this->trip->setDescription('A new Paris adventure awaits');
        assert($this->trip->getDescription() === 'A new Paris adventure awaits', "Description should be updated");
        
        // Test price update with validation
        try {
            $this->trip->setPrice(-100);
            assert(false, "Should throw exception for negative price");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Price must be positive") === 0, "Should throw price validation error");
        }
        
        $this->trip->setPrice(599.99);
        assert($this->trip->getPrice() === 599.99, "Price should be updated to valid amount");
        
        // Test capacity update with validation
        try {
            $this->trip->setCapacity(0);
            assert(false, "Should throw exception for zero capacity");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Capacity must be positive") === 0, "Should throw capacity validation error");
        }
        
        $this->trip->setCapacity(25);
        assert($this->trip->getCapacity() === 25, "Capacity should be updated to valid amount");
        
        echo "PASSED\n";
    }
    
    public function testViewBookedTrips() {
        echo "Testing View Booked Trips... ";
        
        // Create a fresh trip instance
        $this->trip = $this->createTestTrip();
        
        // Book some spots
        $this->trip->bookSpots(2);
        
        // Test trip details visibility
        $tripDetails = $this->trip->getDetails();
        assert(isset($tripDetails['departure_date']), "Departure date should be visible");
        assert(isset($tripDetails['arrival_time']), "Arrival time should be visible");
        assert(isset($tripDetails['image']), "Trip image should be visible");
        assert(isset($tripDetails['description']), "Trip description should be visible");
        
        // Test booked spots tracking
        assert($this->trip->getBookedSpots() === 2, "Should show 2 booked spots");
        
        echo "PASSED\n";
    }
}

// Run the tests
echo "\nRunning Trip Class Unit Tests:\n";
$tripTest = new TripTest();
$tripTest->runTests();
?>
