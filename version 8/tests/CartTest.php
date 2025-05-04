<?php
require_once __DIR__ . '/../classes/Cart.php';

class CartTest {
    private $cart;

    private function clearSession() {
        $_SESSION = [];
    }

    public function __construct() {
        $this->clearSession();
        $this->cart = new Cart(false); // Don't load from session in constructor
    }

    public function runTests() {
        $this->testCartOperations();
        $this->testPriceCalculations();
        $this->testSessionPersistence();
        $this->testItemValidation();
        $this->testCartLimits();
    }

    public function testCartOperations() {
        $this->clearSession();
        echo "Testing Cart Operations... ";
        
        // Test adding items
        $trip1 = [
            'trip_id' => 1,
            'destination' => 'Paris',
            'cost' => 499.99
        ];
        
        $trip2 = [
            'trip_id' => 2,
            'destination' => 'Rome',
            'cost' => 599.99
        ];
        
        $this->cart->clear(); // Clear any existing items
        $this->cart->addItem($trip1);
        echo "\nItems in cart after adding first item: " . count($this->cart->getItems()) . "\n";
        assert(count($this->cart->getItems()) === 1, "Cart should have 1 item");
        
        $this->cart->addItem($trip2);
        assert(count($this->cart->getItems()) === 2, "Cart should have 2 items");
        
        // Test removing items
        $this->cart->removeItem(0); // Remove first item (Paris)
        $items = $this->cart->getItems();
        assert(count($items) === 1, "Cart should have 1 item after removal");
        assert($items[0]['destination'] === 'Rome', "Remaining item should be Rome trip");
        
        // Test clearing cart
        $this->cart->clear();
        assert(count($this->cart->getItems()) === 0, "Cart should be empty after clearing");
        
        echo "PASSED\n";
    }

    public function testPriceCalculations() {
        $this->clearSession();
        echo "Testing Price Calculations... ";
        
        // Add items with different prices
        $this->cart->addItem([
            'trip_id' => 1,
            'destination' => 'Paris',
            'cost' => 499.99
        ]);
        
        $this->cart->addItem([
            'trip_id' => 2,
            'destination' => 'Rome',
            'cost' => 599.99
        ]);
        
        // Test subtotal calculation
        $subtotal = $this->cart->getSubtotal();
        assert($subtotal === 1099.98, "Subtotal should be sum of all items");
        
        // Test tax calculation (assuming 23% VAT)
        $tax = $this->cart->calculateTax();
        echo "\nCalculated tax: " . $tax . "\n";
        echo "Expected tax: 252.90\n";
        assert(abs($tax - 252.90) < 0.01, "Tax calculation should be correct");
        
        // Test total with tax
        $total = $this->cart->getTotal();
        assert(abs($total - 1352.88) < 0.01, "Total should include tax");
        
        // Test discount application
        $this->cart->applyDiscount(10); // 10% discount
        $discountedTotal = $this->cart->getTotal();
        assert($discountedTotal < $total, "Discounted total should be less than original total");
        
        echo "PASSED\n";
    }

    public function testSessionPersistence() {
        $this->clearSession();
        echo "Testing Session Persistence... ";
        
        // Clear any existing session data
        $_SESSION = [];
        echo "\nInitial session state: " . json_encode($_SESSION) . "\n";
        
        // Clear the cart
        $this->cart->clear();
        
        // Add a single test item
        $this->cart->addItem([
            'trip_id' => 1,
            'destination' => 'Paris',
            'cost' => 499.99
        ]);
        
        // Test save to session
        $this->cart->saveToSession();
        echo "Session after save: " . json_encode($_SESSION['cart']) . "\n";
        $sessionData = $_SESSION['cart'] ?? null;
        assert($sessionData !== null, "Cart should be saved to session");
        
        // Test load from session
        $newCart = new Cart(false); // Don't load from session in constructor
        $newCart->loadFromSession();
        echo "Items in newCart: " . json_encode($newCart->getItems()) . "\n";
        assert(count($newCart->getItems()) === 1, "Cart should be loaded from session");
        assert($newCart->getItems()[0]['destination'] === 'Paris', "Loaded item should match saved item");
        
        echo "PASSED\n";
    }

    public function testItemValidation() {
        $this->clearSession();
        echo "Testing Item Validation... ";
        
        // Test missing required fields
        try {
            $this->cart->addItem([
                'destination' => 'Paris'
                // Missing trip_id and cost
            ]);
            assert(false, "Should throw exception for missing required fields");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Missing required fields") === 0, "Should throw validation error");
        }
        
        // Test invalid cost
        try {
            $this->cart->addItem([
                'trip_id' => 1,
                'destination' => 'Paris',
                'cost' => -100
            ]);
            assert(false, "Should throw exception for negative cost");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Invalid cost") === 0, "Should throw cost validation error");
        }
        
        // Test duplicate item
        $trip = [
            'trip_id' => 1,
            'destination' => 'Paris',
            'cost' => 499.99
        ];
        
        $this->cart->clear();
        $this->cart->addItem($trip);
        
        try {
            $this->cart->addItem($trip); // Adding same trip again
            assert(false, "Should throw exception for duplicate item");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Item already in cart") === 0, "Should throw duplicate item error");
        }
        
        echo "PASSED\n";
    }

    public function testCartLimits() {
        $this->clearSession();
        echo "Testing Cart Limits... ";
        
        $this->cart->clear();
        
        // Test maximum items limit
        for ($i = 1; $i <= 5; $i++) {
            $this->cart->addItem([
                'trip_id' => $i,
                'destination' => "Destination $i",
                'cost' => 100 * $i
            ]);
        }
        
        try {
            // Try to add 6th item
            $this->cart->addItem([
                'trip_id' => 6,
                'destination' => 'Extra Trip',
                'cost' => 600
            ]);
            assert(false, "Should throw exception for exceeding maximum items");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Cart is full") === 0, "Should throw cart limit error");
        }
        
        // Test maximum total value
        $this->cart->clear();
        try {
            $this->cart->addItem([
                'trip_id' => 1,
                'destination' => 'Luxury Trip',
                'cost' => 10000.00 // Assuming this exceeds maximum allowed value
            ]);
            assert(false, "Should throw exception for exceeding maximum value");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Exceeds maximum cart value") === 0, "Should throw value limit error");
        }
        
        echo "PASSED\n";
    }
}

// Run the tests
echo "\nRunning Cart Class Unit Tests:\n";
$cartTest = new CartTest();
$cartTest->runTests();
?>
