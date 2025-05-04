<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Debug session data
error_log('Checkout page - Session data: ' . print_r($_SESSION, true));

// Check if this is a POST request from cart
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_SESSION['checkout_started'])) {
    header('Location: cart.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// Mark checkout as started to allow page refreshes
$_SESSION['checkout_started'] = true;

// Initialize database connection
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user']['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // User not found in database
        unset($_SESSION['user']);
        $_SESSION['error_message'] = 'Please login again.';
        header('Location: login.php');
        exit;
    }
    
    // Update session with fresh user data
    $_SESSION['user'] = [
        'user_id' => $user['user_id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred. Please try again.';
    header('Location: cart.php');
    exit;
}

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Calculate subtotal from cart items
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += isset($item['cost']) ? (float)$item['cost'] : 0;
}

// Get discount from session if exists
require_once 'Discount.php';

// Calculate discount without age/student status for now
$discount = Discount::calculateDiscount($subtotal, null, false);

// Store discount in session
$_SESSION['discount'] = $discount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }
        .alert-danger li {
            margin: 5px 0;
        }
    </style>
    <title>Checkout - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="checkout-container">
        <?php 
        if (isset($_SESSION['checkout_errors'])): 
            echo '<div class="alert alert-danger"><ul>';
            foreach ($_SESSION['checkout_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul></div>';
            unset($_SESSION['checkout_errors']);
        endif;
        
        if (isset($_SESSION['error_message'])):
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        endif;
        ?>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-details">
                        <h4><?= htmlspecialchars($item['destination']) ?></h4>
                        <p class="cart-item-description"><?= htmlspecialchars($item['description']) ?></p>
                    </div>
                    <div class="cart-item-price">€<?= number_format($item['cost'], 2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="checkout-form-section">
            <h3>Billing Information</h3>
            <form action="process_order.php" method="POST" id="checkout-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10,}">
                        <small>Enter at least 10 digits</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Street Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                </div>

                <div class="discount-options">
                    <h3>Discount Options</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" max="<?= date('Y-m-d') ?>">
                            <small>For senior discount (65+ years)</small>
                        </div>
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="is_student" name="is_student">
                                <label for="is_student">I am a student</label>
                                <small>15% discount with valid student ID</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-icons">
                    <!-- Add payment method icons here -->
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="card_name">Card Holder Name</label>
                        <input type="text" id="card_name" name="card_name" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" required maxlength="16" placeholder="1234 5678 9012 3456">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" required placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" required maxlength="4" placeholder="123">
                        <small>3 or 4 digits on the back of your card</small>
                    </div>
                </div>

                <div class="price-summary">
                    <div class="price-row">
                        <span>Subtotal:</span>
                        <span>€<?= number_format($discount['originalPrice'], 2) ?></span>
                    </div>
                    <?php if ($discount['discountAmount'] > 0): ?>
                    <div class="price-row discount">
                        <span><?= htmlspecialchars($discount['discountMessage']) ?>:</span>
                        <span>-€<?= number_format($discount['discountAmount'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="price-row total">
                        <span>Total:</span>
                        <span>€<?= number_format($discount['finalPrice'], 2) ?></span>
                    </div>
                    <input type="hidden" name="subtotal" value="<?= $discount['originalPrice'] ?>">
                    <input type="hidden" name="discount_amount" value="<?= $discount['discountAmount'] ?>">
                    <input type="hidden" name="final_price" value="<?= $discount['finalPrice'] ?>">
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="terms.php">Terms and Conditions</a> and <a href="privacy.php">Privacy Policy</a></label>
                </div>

                <button type="submit" class="submit-payment">Complete Booking</button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        var cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
        if (cardNumber.length !== 16 || !/^\d+$/.test(cardNumber)) {
            e.preventDefault();
            alert('Please enter a valid 16-digit card number');
            return;
        }
        
        var expiryDate = document.getElementById('expiry_date').value;
        if (!/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(expiryDate)) {
            e.preventDefault();
            alert('Please enter a valid expiry date (MM/YY)');
            return;
        }
        
        var cvv = document.getElementById('cvv').value;
        if (!/^[0-9]{3,4}$/.test(cvv)) {
            e.preventDefault();
            alert('Please enter a valid CVV (3-4 digits)');
            return;
        }
    });
    
    // Format card number as user types
    document.getElementById('card_number').addEventListener('input', function(e) {
        var value = e.target.value.replace(/\D/g, '');
        if (value.length > 16) value = value.substr(0, 16);
        e.target.value = value;
    });
    
    // Format expiry date as user types
    document.getElementById('expiry_date').addEventListener('input', function(e) {
        var value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substr(0, 2) + (value.length > 2 ? '/' + value.substr(2, 2) : '');
        }
        if (value.length > 5) value = value.substr(0, 5);
        e.target.value = value;
    });
    </script>
</body>
</html>
