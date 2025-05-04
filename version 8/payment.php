<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if we have trip data
if (!isset($_GET['trip_id']) || !isset($_GET['cost'])) {
    $_SESSION['error_message'] = 'Missing trip information';
    header('Location: shop.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Get trip details
    $stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ?");
    $stmt->execute([$_GET['trip_id']]);
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$trip) {
        throw new Exception('Trip not found');
    }
    
    if ($trip['available_seats'] <= 0) {
        throw new Exception('No seats available for this trip');
    }
    
    // Calculate duration
    $departure = new DateTime($trip['departure_date']);
    $return = new DateTime($trip['return_date']);
    $interval = $departure->diff($return);
    $trip['duration'] = $interval->days;
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: shop.php');
    exit;
}

// Set initial cost from GET parameter
$cost = floatval($_GET['cost']);

// No need for cart functionality in direct booking
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .payment-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .trip-summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .trip-details {
            margin-top: 15px;
        }
        .trip-details h3 {
            color: #2e7d32;
            margin: 0 0 10px;
        }
        .trip-details p {
            color: #666;
            margin: 5px 0;
        }
        .trip-info {
            margin: 15px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
        }
        .trip-info p {
            margin: 8px 0;
        }
        .total-amount {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #eee;
            font-size: 1.2em;
            color: #1565c0;
        }
        .payment-form {
            display: grid;
            gap: 20px;
        }
        .form-group {
            display: grid;
            gap: 8px;
        }
        .form-group label {
            font-weight: bold;
            color: #2e4a2e;
        }
        .form-group input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .total-amount {
            font-size: 24px;
            color: #2e7d32;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
        }
        .submit-payment {
            background: #2e7d32;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-payment:hover {
            background: #1b5e20;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="payment-container">
        <h1>Complete Your Booking</h1>
        
        <div class="trip-summary">
            <h2>Order Summary</h2>
            <div class="trip-details">
                <h3><?= htmlspecialchars($trip['destination']) ?></h3>
                <p><?= htmlspecialchars($trip['description']) ?></p>
                <div class="trip-info">
                    <p><strong>📅 Departure:</strong> <?= date('F j, Y', strtotime($trip['departure_date'])) ?></p>
                    <p><strong>🔄 Return:</strong> <?= date('F j, Y', strtotime($trip['return_date'])) ?></p>
                    <p><strong>⏱️ Duration:</strong> <?= $trip['duration'] ?> days</p>
                    <p><strong>💺 Available Seats:</strong> <?= htmlspecialchars($trip['available_seats']) ?></p>
                </div>
                <div class="total-amount">
                    <strong>Total Amount:</strong> €<?= number_format($trip['cost'], 2) ?>
                </div>
            </div>
        </div>

        <form action="process_booking.php" method="post" class="payment-form">
            <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip['trip_id']) ?>">
            <input type="hidden" name="cost" value="<?= htmlspecialchars($trip['cost']) ?>">
            
            <h3>Billing Information</h3>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required 
                       pattern="[0-9]{10,}" placeholder="Enter at least 10 digits">
            </div>
            
            <div class="form-group">
                <label for="address">Street Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            
            <h3>Payment Information</h3>
            <div class="form-group">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" required 
                       pattern="[0-9]{16}" placeholder="1234 5678 9012 3456">
            </div>
            
            <div class="form-group">
                <label for="card_holder">Card Holder Name</label>
                <input type="text" id="card_holder" name="card_holder" required
                       placeholder="John Doe">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="expiry">Expiry Date</label>
                    <input type="text" id="expiry" name="expiry" required 
                           pattern="(0[1-9]|1[0-2])/[0-9]{2}" placeholder="MM/YY">
                </div>
                
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" required 
                           pattern="[0-9]{3,4}" placeholder="123">
                </div>
            </div>
            
            <button type="submit" class="submit-payment">Complete Booking</button>
        </form>
    </div>

            <!-- Discount Section -->
            <div class="discount-section">
                <h3>Check for Discounts</h3>
                <div class="form-group">
                    <label for="age">Your Age:</label>
                    <input type="number" id="age" name="age" min="1" max="120">
                    <small>Enter 65 or above for senior discount (20% off)</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="is_student" name="is_student" value="yes">
                        I am a student (15% discount)
                    </label>
                </div>
                
                <div class="discount-summary" style="margin-top: 15px; display: none;">
                    <p class="discount-message" style="color: #2e7d32; font-weight: bold;"></p>
                    <p>Original Price: €<span id="original-price"><?= number_format($trip['cost'], 2) ?></span></p>
                    <p>Discount Amount: -€<span id="discount-amount">0.00</span></p>
                    <p style="font-size: 1.2em; font-weight: bold;">Final Price: €<span id="final-price"><?= number_format($trip['cost'], 2) ?></span></p>
                </div>
            </div>
            
            <input type="hidden" name="applied_discount" id="applied_discount" value="0">
            <input type="hidden" name="final_price" id="hidden_final_price" value="<?= $trip['cost'] ?>">
            <button type="submit" class="submit-payment">Complete Booking</button>
        </form>
    </div>

    <script>
        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 16) value = value.slice(0, 16);
            e.target.value = value.replace(/(.{4})/g, '$1 ').trim();
        });

        // Format expiry date input
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.slice(0, 4);
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Format CVV input
        document.getElementById('cvv').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) value = value.slice(0, 4);
            e.target.value = value;
        });

        // Handle discount calculations
        function calculateDiscount() {
            const originalPrice = <?= $trip['cost'] ?>;
            let finalPrice = originalPrice;
            let discountMessage = '';
            let discountAmount = 0;

            const age = parseInt($('#age').val()) || 0;
            const isStudent = $('#is_student').is(':checked');

            if (age >= 65) {
                discountAmount = originalPrice * 0.20; // 20% senior discount
                discountMessage = 'Senior Discount (20% off) applied!';
            } else if (isStudent) {
                discountAmount = originalPrice * 0.15; // 15% student discount
                discountMessage = 'Student Discount (15% off) applied!';
            }

            if (discountAmount > 0) {
                finalPrice = originalPrice - discountAmount;
                $('.discount-summary').show();
                $('.discount-message').text(discountMessage);
                $('#discount-amount').text(discountAmount.toFixed(2));
                $('#final-price').text(finalPrice.toFixed(2));
                $('#applied_discount').val(discountAmount);
                $('#hidden_final_price').val(finalPrice);
            } else {
                $('.discount-summary').hide();
            }
        }

        // Attach event listeners for discount calculations
        $('#age, #is_student').on('change', calculateDiscount);
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
