<?php
header('Content-Type: text/plain');

require_once __DIR__ . '/../classes/Cart.php';
require_once __DIR__ . '/../Customer.php';
require_once __DIR__ . '/../TourTrip.php';

class EquivalencePartitionTest {
    private $results = [];

    public function runTests() {
        $this->testPriceRanges();
        $this->testCapacityRanges();
        $this->testDateRanges();
        $this->testLoyaltyPointsRanges();
        $this->generateReport();
    }

    public function testPriceRanges() {
        echo "\nTesting Price Ranges:\n";
        $this->results['price_ranges'] = [
            'Invalid low price (-100)' => $this->testPrice(-100, false),
            'Valid price (499.99)' => $this->testPrice(499.99, true),
            'Invalid high price (5500)' => $this->testPrice(5500, false)
        ];
    }

    public function testCapacityRanges() {
        echo "\nTesting Capacity Ranges:\n";
        $this->results['capacity_ranges'] = [
            'Invalid low capacity (0)' => $this->testCapacity(0, false),
            'Valid capacity (25)' => $this->testCapacity(25, true),
            'Invalid high capacity (51)' => $this->testCapacity(51, false)
        ];
    }

    public function testDateRanges() {
        echo "\nTesting Date Ranges:\n";
        $this->results['date_ranges'] = [
            'Past date (-60 days)' => $this->testDate('-60 days', false),
            'Valid date (+30 days)' => $this->testDate('+30 days', true),
            'Future date (+60 days)' => $this->testDate('+60 days', true)
        ];
    }

    public function testLoyaltyPointsRanges() {
        echo "\nTesting Loyalty Points Ranges:\n";
        $this->results['loyalty_points'] = [
            'Invalid negative points (-50)' => $this->testLoyaltyPoints(-50, false),
            'Valid points (500)' => $this->testLoyaltyPoints(500, true),
            'Invalid high points (1001)' => $this->testLoyaltyPoints(1001, false)
        ];
    }

    private function testPrice($price, $shouldPass) {
        echo "Testing price: $price... ";
        try {
            $trip = new TourTrip(1, 'Test', 'Test Trip', $price, '2025-01-01', '2025-01-07', 10, 'test.jpg');
            $result = $shouldPass;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            $result = !$shouldPass;
            echo ($result ? "PASS" : "FAIL") . " (" . $e->getMessage() . ")\n";
            return $result;
        }
    }

    private function testCapacity($capacity, $shouldPass) {
        echo "Testing capacity: $capacity... ";
        try {
            $trip = new TourTrip(1, 'Test', 'Test Trip', 499.99, '2025-01-01', '2025-01-07', $capacity, 'test.jpg');
            $result = $shouldPass;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            $result = !$shouldPass;
            echo ($result ? "PASS" : "FAIL") . " (" . $e->getMessage() . ")\n";
            return $result;
        }
    }

    private function testDate($offset, $shouldPass) {
        echo "Testing date offset: $offset... ";
        try {
            $date = date('Y-m-d', strtotime($offset));
            $trip = new TourTrip(1, 'Test', 'Test Trip', 499.99, $date, date('Y-m-d', strtotime($date . ' +7 days')), 10, 'test.jpg');
            $result = $shouldPass;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            $result = !$shouldPass;
            echo ($result ? "PASS" : "FAIL") . " (" . $e->getMessage() . ")\n";
            return $result;
        }
    }

    private function testLoyaltyPoints($points, $shouldPass) {
        echo "Testing loyalty points: $points... ";
        try {
            $customer = new Customer('test_customer', 'test@example.com', '123 Test St');
            $customer->addLoyaltyPoints($points);
            $result = $shouldPass;
            echo ($result ? "PASS" : "FAIL") . "\n";
            return $result;
        } catch (Exception $e) {
            $result = !$shouldPass;
            echo ($result ? "PASS" : "FAIL") . " (" . $e->getMessage() . ")\n";
            return $result;
        }
    }

    private function generateReport() {
        echo "\nEquivalence Partition Test Results:\n";
        echo "================================\n";
        $totalTests = 0;
        $passedTests = 0;

        foreach ($this->results as $category => $tests) {
            echo "\n$category:\n";
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
echo "Running Equivalence Partition Tests:\n";
echo "==================================\n";
$test = new EquivalencePartitionTest();
$test->runTests();
