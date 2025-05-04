<?php
header('Content-Type: text/plain');

require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../Customer.php';
require_once __DIR__ . '/../TourTrip.php';

class BasisPathTest {
    private $results = [];
    private $customer;
    private $cart;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = []; // Clear session
        $this->customer = new Customer('test_customer', 'test@example.com', '123 Test St');
        $this->cart = new Cart(false);
    }

    public function runTests() {
        $this->testBookingWorkflow();
        $this->testLoyaltyPointsWorkflow();
        $this->testCartWorkflow();
        $this->generateReport();
    }

    public function testBookingWorkflow() {
        echo "\nTesting Booking Workflow:\n";
        $this->results['booking_workflow'] = [
            'Successful booking' => $this->testSuccessfulBooking(),
            'Insufficient capacity' => $this->testInsufficientCapacity(),
            'Loyalty discount' => $this->testLoyaltyDiscount()
        ];
    }

    public function testLoyaltyPointsWorkflow() {
        echo "\nTesting Loyalty Points Workflow:\n";
        $this->results['loyalty_workflow'] = [
            'Earn points' => $this->testEarnPoints(),
            'Redeem points' => $this->testRedeemPoints(),
            'Insufficient points' => $this->testInsufficientPoints()
        ];
    }

    public function testCartWorkflow() {
        echo "\nTesting Cart Workflow:\n";
        $this->results['cart_workflow'] = [
            'Add valid items' => $this->testAddValidItems(),
            'Add duplicate item' => $this->testAddDuplicateItem(),
            'Exceed cart limit' => $this->testExceedCartLimit()
        ];
    }

    private function testSuccessfulBooking() {
        echo "Testing successful booking... ";
        try {
            $this->cart->clear();
            $this->cart->addItem(['trip_id' => 1, 'destination' => 'Paris', 'cost' => 499.99]);
            $booking = ['booking_id' => 1, 'trip_id' => 1, 'destination' => 'Paris', 'cost' => 499.99, 'date' => '2025-06-01'];
            $this->customer->addBooking($booking);
            echo "PASS\n";
            return true;
        } catch (Exception $e) {
            echo "FAIL (" . $e->getMessage() . ")\n";
            return false;
        }
    }

    private function testInsufficientCapacity() {
        echo "Testing insufficient capacity... ";
        try {
            $trip = new TourTrip(2, 'Full Trip', 'No Space', 499.99, '2025-01-01', '2025-01-07', 0, 'test.jpg');
            echo "FAIL (Should have thrown exception)\n";
            return false;
        } catch (InvalidArgumentException $e) {
            echo "PASS (Expected exception thrown)\n";
            return true;
        }
    }

    private function testLoyaltyDiscount() {
        echo "Testing loyalty discount... ";
        try {
            $this->customer->addLoyaltyPoints(100);
            $this->cart->addItem(['trip_id' => 3, 'destination' => 'Rome', 'cost' => 450.00]);
            $result = $this->cart->getTotal() === 450.00;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            echo "FAIL (" . $e->getMessage() . ")\n";
            return false;
        }
    }

    private function testEarnPoints() {
        echo "Testing earn points... ";
        try {
            $initialPoints = $this->customer->getLoyaltyPoints();
            $booking = ['booking_id' => 2, 'trip_id' => 2, 'destination' => 'Rome', 'cost' => 500.00];
            $this->customer->addBooking($booking);
            $result = $this->customer->getLoyaltyPoints() > $initialPoints;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            echo "FAIL (" . $e->getMessage() . ")\n";
            return false;
        }
    }

    private function testRedeemPoints() {
        echo "Testing redeem points... ";
        try {
            $this->customer->addLoyaltyPoints(100);
            $points = $this->customer->getLoyaltyPoints();
            $result = $this->customer->redeemPoints(50) && $this->customer->getLoyaltyPoints() === ($points - 50);
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            echo "FAIL (" . $e->getMessage() . ")\n";
            return false;
        }
    }

    private function testInsufficientPoints() {
        echo "Testing insufficient points... ";
        try {
            $this->customer->redeemPoints(1000);
            echo "FAIL (Should have thrown exception)\n";
            return false;
        } catch (InvalidArgumentException $e) {
            echo "PASS (Expected exception thrown)\n";
            return true;
        }
    }

    private function testAddValidItems() {
        echo "Testing add valid items... ";
        try {
            $this->cart->clear();
            $this->cart->addItem(['trip_id' => 1, 'destination' => 'Paris', 'cost' => 499.99]);
            $result = count($this->cart->getItems()) === 1;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            echo "FAIL (" . $e->getMessage() . ")\n";
            return false;
        }
    }

    private function testAddDuplicateItem() {
        echo "Testing add duplicate item... ";
        try {
            $this->cart->addItem(['trip_id' => 1, 'destination' => 'Paris', 'cost' => 499.99]);
            echo "FAIL (Should have thrown exception)\n";
            return false;
        } catch (InvalidArgumentException $e) {
            echo "PASS (Expected exception thrown)\n";
            return true;
        }
    }

    private function testExceedCartLimit() {
        echo "Testing exceed cart limit... ";
        try {
            $this->cart->addItem(['trip_id' => 2, 'destination' => 'Luxury Trip', 'cost' => 10000.00]);
            echo "FAIL (Should have thrown exception)\n";
            return false;
        } catch (InvalidArgumentException $e) {
            echo "PASS (Expected exception thrown)\n";
            return true;
        }
    }

    private function generateReport() {
        echo "\nBasis Path Test Results:\n";
        echo "=======================\n";
        $totalTests = 0;
        $passedTests = 0;

        foreach ($this->results as $workflow => $tests) {
            echo "\n$workflow:\n";
            foreach ($tests as $test => $result) {
                $totalTests++;
                if ($result) $passedTests++;
                echo "  $test: " . ($result ? "PASS" : "FAIL") . "\n";
            }
        }

        $percentage = ($passedTests / $totalTests) * 100;
        echo "\nOverall Results: $passedTests/$totalTests tests passed (" . number_format($percentage, 1) . "%)\n";
    }
}

// Run the tests
echo "Running Basis Path Tests:\n";
echo "=======================\n";
$test = new BasisPathTest();
$test->runTests();
