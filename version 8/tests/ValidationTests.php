<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/../TourTrip.php';
require_once __DIR__ . '/../Discount.php';

class ValidationTests {
    public function testTripPriceValidation() {
        echo "Testing Trip Price Validation...\n";
        
        $testCases = [
            ['price' => 300, 'expected' => true, 'category' => 'Budget Trip'],
            ['price' => 750, 'expected' => true, 'category' => 'Standard Trip'],
            ['price' => 1500, 'expected' => true, 'category' => 'Luxury Trip'],
            ['price' => 0, 'expected' => false],
            ['price' => -100, 'expected' => false],
            ['price' => 2500, 'expected' => false],
            ['price' => 'abc', 'expected' => false]
        ];
        
        foreach ($testCases as $test) {
            [$isValid, $message] = TourTrip::validatePrice($test['price']);
            
            if ($isValid === $test['expected']) {
                if ($isValid) {
                    echo "✓ Price €{$test['price']} correctly validated as {$message}\n";
                } else {
                    echo "✓ Invalid price €{$test['price']} correctly rejected: {$message}\n";
                }
            } else {
                echo "× Test failed for price €{$test['price']}\n";
            }
        }
        
        echo "Price Validation Tests Complete\n\n";
    }
    
    public function testDiscountValidation() {
        echo "Testing Discount Validation...\n";
        
        $testCases = [
            // Test senior discount
            ['price' => 1000, 'age' => 65, 'isStudent' => false, 'expectedDiscount' => 200],
            ['price' => 1000, 'age' => 70, 'isStudent' => false, 'expectedDiscount' => 200],
            
            // Test student discount
            ['price' => 1000, 'age' => 20, 'isStudent' => true, 'expectedDiscount' => 150],
            
            // Test no discount
            ['price' => 1000, 'age' => 30, 'isStudent' => false, 'expectedDiscount' => 0],
            
            // Edge cases
            ['price' => 1000, 'age' => 64, 'isStudent' => false, 'expectedDiscount' => 0],
            ['price' => 0, 'age' => 65, 'isStudent' => false, 'expectedDiscount' => 0]
        ];
        
        foreach ($testCases as $test) {
            $result = Discount::calculateDiscount($test['price'], $test['age'], $test['isStudent']);
            $actualDiscount = $result['discountAmount'];
            
            if (abs($actualDiscount - $test['expectedDiscount']) < 0.01) {
                echo "✓ Correct discount calculated: {$result['discountMessage']}\n";
                echo "   Original: €{$result['originalPrice']}, Discount: €{$result['discountAmount']}, Final: €{$result['finalPrice']}\n";
            } else {
                echo "× Incorrect discount. Expected €{$test['expectedDiscount']}, got €{$actualDiscount}\n";
            }
        }
        
        echo "Discount Validation Tests Complete\n\n";
    }

    public function runTests() {
        $this->testTripPriceValidation();
        $this->testDiscountValidation();
    }
}

// Create instance and run tests
$tests = new ValidationTests();
$tests->runTests();
?>
