<?php
// ValidationTests.php
// Validation Tests for EuroTours project

// 1. Validate Email Format (from register.php)
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Test
assert(isValidEmail("test@example.com")); // Valid email
assert(!isValidEmail("bademail.com"));    // Invalid email

// 2. Validate Password Strength (from register.php)
function isStrongPassword($password) {
    return strlen($password) >= 8;
}

// Test
assert(isStrongPassword("strongpass"));   // Strong password
assert(!isStrongPassword("weak"));         // Too short

// 3. Validate Trip ID is Numeric (from booking.php)
function isNumericTripId($trip_id) {
    return is_numeric($trip_id);
}

// Test
assert(isNumericTripId(5));    // Numeric Trip ID
assert(!isNumericTripId("abc")); // Non-numeric Trip ID

// 4. Validate Payment Amount Positive (from payment.php)
function isValidPaymentAmount($amount) {
    return is_numeric($amount) && $amount > 0;
}

// Test
assert(isValidPaymentAmount(199.99));   // Positive payment
assert(!isValidPaymentAmount(-20));      // Negative amount

// 5. Validate Search Term Length (from live_search.php)
function isValidSearchTerm($term) {
    return strlen(trim($term)) >= 2;
}

// Test
assert(isValidSearchTerm("Paris"));  // Good search term
assert(!isValidSearchTerm("A"));      // Too short
?>
