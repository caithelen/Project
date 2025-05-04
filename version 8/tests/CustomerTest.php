<?php
require_once __DIR__ . '/../Customer.php';

class CustomerTest {
    private $customer;

    public function __construct() {
        $this->customer = new Customer('test_customer', 'customer@example.com', '123 Test St');
    }

    public function runTests() {
        $this->testCustomerCreation();
        $this->testShoppingCart();
        $this->testBookingHistory();
        $this->testProfileManagement();
        $this->testLoyaltyPoints();
    }

    public function testCustomerCreation() {
        echo "Testing Customer Creation... ";
        assert($this->customer->getUsername() === 'test_customer', "Username should be 'test_customer'");
        assert($this->customer->getEmail() === 'customer@example.com', "Email should be 'customer@example.com'");
        assert($this->customer->getRole() === 'customer', "Role should be 'customer'");
        assert($this->customer->getLoyaltyPoints() === 0, "Initial loyalty points should be 0");
        echo "PASSED\n";
    }

    public function testShoppingCart() {
        echo "Testing Shopping Cart Management... ";
        
        // Test adding items to cart
        $trip1 = ['trip_id' => 1, 'destination' => 'Paris', 'cost' => 499.99];
        $trip2 = ['trip_id' => 2, 'destination' => 'Rome', 'cost' => 599.99];
        
        $this->customer->addToCart($trip1);
        assert(count($this->customer->getCart()) === 1, "Cart should have 1 item");
        
        $this->customer->addToCart($trip2);
        assert(count($this->customer->getCart()) === 2, "Cart should have 2 items");
        
        // Test removing items from cart
        $this->customer->removeFromCart(1);
        assert(count($this->customer->getCart()) === 1, "Cart should have 1 item after removal");
        assert($this->customer->getCart()[0]['destination'] === 'Rome', "Remaining item should be Rome trip");
        
        // Test cart total calculation
        assert($this->customer->getCartTotal() === 599.99, "Cart total should be 599.99");
        
        // Test clearing cart
        $this->customer->clearCart();
        assert(count($this->customer->getCart()) === 0, "Cart should be empty after clearing");
        
        echo "PASSED\n";
    }

    public function testBookingHistory() {
        echo "Testing Booking History... ";
        
        $booking1 = [
            'booking_id' => 1,
            'trip_id' => 1,
            'destination' => 'Paris',
            'cost' => 499.99,
            'date' => '2025-06-01'
        ];
        
        $booking2 = [
            'booking_id' => 2,
            'trip_id' => 2,
            'destination' => 'Rome',
            'cost' => 599.99,
            'date' => '2025-07-01'
        ];
        
        // Test adding bookings
        $this->customer->addBooking($booking1);
        assert(count($this->customer->getBookingHistory()) === 1, "Should have 1 booking");
        
        $this->customer->addBooking($booking2);
        assert(count($this->customer->getBookingHistory()) === 2, "Should have 2 bookings");
        
        // Test retrieving specific booking
        $retrievedBooking = $this->customer->getBooking(1);
        assert($retrievedBooking['destination'] === 'Paris', "Should retrieve correct booking");
        
        echo "PASSED\n";
    }

    public function testProfileManagement() {
        echo "Testing Profile Management... ";
        
        // Test updating profile information
        $newProfile = [
            'phone' => '+353 123 456 789',
            'address' => '123 Test St, Dublin',
            'preferences' => ['adventure', 'culture']
        ];
        
        $this->customer->updateProfile($newProfile);
        $profile = $this->customer->getProfile();
        
        assert($profile['phone'] === '+353 123 456 789', "Phone number should be updated");
        assert($profile['address'] === '123 Test St, Dublin', "Address should be updated");
        assert(in_array('adventure', $profile['preferences']), "Preferences should be updated");
        
        // Test invalid phone number
        try {
            $this->customer->updateProfile(['phone' => '123']); // Invalid format
            assert(false, "Should throw exception for invalid phone number");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Invalid phone number") === 0, "Should throw phone validation error");
        }
        
        echo "PASSED\n";
    }

    public function testLoyaltyPoints() {
        echo "Testing Loyalty Points System... ";
        
        // Test points earning
        $this->customer->addLoyaltyPoints(100); // For a €500 booking
        assert($this->customer->getLoyaltyPoints() === 100, "Should have 100 points");
        
        $this->customer->addLoyaltyPoints(120); // For a €600 booking
        assert($this->customer->getLoyaltyPoints() === 220, "Should have 220 points");
        
        // Test points redemption
        $success = $this->customer->redeemPoints(50);
        assert($success === true, "Should successfully redeem 50 points");
        assert($this->customer->getLoyaltyPoints() === 170, "Should have 170 points after redemption");
        
        // Test invalid redemption
        try {
            $this->customer->redeemPoints(500); // More than available
            assert(false, "Should throw exception for insufficient points");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Insufficient points") === 0, "Should throw points validation error");
        }
        
        echo "PASSED\n";
    }
}

// Run the tests
echo "\nRunning Customer Class Unit Tests:\n";
$customerTest = new CustomerTest();
$customerTest->runTests();
?>
