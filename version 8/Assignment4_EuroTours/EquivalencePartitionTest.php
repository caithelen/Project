<?php
// EquivalencePartitionTest.php
// Testing Trip Price Validations for EuroTours

function isValidPrice($price) {
    return is_numeric($price) && $price > 0;
}

// Valid Inputs
assert(isValidPrice(100));
assert(isValidPrice(500));

// Invalid Inputs
assert(!isValidPrice(0));      // Price cannot be zero
assert(!isValidPrice(-20));    // Price cannot be negative
assert(!isValidPrice('abc'));  // Price must be a number
?>
