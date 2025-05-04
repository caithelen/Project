<?php
// BasisPathTest.php
// Testing Student Discount Application for EuroTours

function calculatePrice($originalPrice, $isStudent) {
    if ($isStudent) {
        return $originalPrice * 0.85; // 15% discount
    } else {
        return $originalPrice; // full price
    }
}

// Path 1: User is a student
assert(abs(calculatePrice(100, true) - 85) < 0.01);

// Path 2: User is not a student
assert(abs(calculatePrice(100, false) - 100) < 0.01);

?>
