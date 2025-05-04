<?php
session_start();

require_once __DIR__ . '/app/config/Database.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart']) && isset($_POST['trip_id'])) {
        try {
            // Get trip details from database
            $stmt = $pdo->prepare("SELECT * FROM trips WHERE trip_id = ?");
            $stmt->execute([$_POST['trip_id']]);
            $trip = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($trip) {
                $_SESSION['cart'][] = [
                    'trip_id' => $trip['trip_id'],
                    'destination' => $trip['destination'],
                    'cost' => $trip['cost'],
                    'description' => $trip['description']
                ];
                $_SESSION['success_message'] = 'Trip added to cart successfully!';
            } else {
                $_SESSION['error_message'] = 'Trip not found.';
            }
        } catch (PDOException $e) {
            error_log('Cart error: ' . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while adding to cart.';
        }
    } elseif (isset($_POST['remove_from_cart']) && isset($_POST['remove_index'])) {
        $index = $_POST['remove_index'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            $_SESSION['success_message'] = 'Item removed from cart successfully!';
        } else {
            $_SESSION['error_message'] = 'Item not found in cart.';
        }
    }
    
    // Redirect after any POST action
    header('Location: cart.php');
    exit;
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['cost'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - EuroTours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-table-wrapper {
            margin: 20px 0;
            overflow-x: auto;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .cart-table th, .cart-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .cart-table th {
            background-color: #2e7d32;
            color: white;
        }
        .cart-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .continue-shopping-btn, .checkout-btn {
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .continue-shopping-btn {
            background-color: #6c757d;
            color: white;
        }
        .continue-shopping-btn:hover {
            background-color: #5a6268;
        }
        .checkout-btn {
            background-color: #1565c0;
            color: white;
        }
        .checkout-btn:hover {
            background-color: #0d47a1;
        }
        .remove-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .remove-btn:hover {
            background-color: #c82333;
        }
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
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <div class="cart-content">
            <h2 class="mb-4">Shopping Cart</h2>
            <?php
            // Display error message if exists
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
        }
        
        // Display success message if exists
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }

        // Display cart items
        if (empty($_SESSION['cart'])) {
            echo '<div class="alert alert-info">Your cart is empty. <a href="shop.php">Continue shopping</a></div>';
        } else {
            $total = 0;
            echo '<div class="cart-table-wrapper">';
            echo '<table class="cart-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Destination</th>';
            echo '<th>Description</th>';
            echo '<th>Cost</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($_SESSION['cart'] as $index => $item) {
                $total += $item['cost'];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($item['destination']) . '</td>';
                echo '<td>' . htmlspecialchars($item['description']) . '</td>';
                echo '<td>$' . number_format($item['cost'], 2) . '</td>';
                echo '<td>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="remove_index" value="' . $index . '">';
                echo '<button type="submit" name="remove_from_cart" class="remove-btn">Remove</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';
            echo '<td colspan="2"><strong>Total:</strong></td>';
            echo '<td colspan="2"><strong>$' . number_format($total, 2) . '</strong></td>';
            echo '</tr>';
            echo '</tfoot>';
            echo '</table>';
            echo '</div>';
            
            // Debug session
            error_log('Cart checkout - Session data: ' . print_r($_SESSION, true));
            
            echo '<div class="cart-actions">';
            echo '<a href="shop.php" class="continue-shopping-btn">Continue Shopping</a>';
            
            // Check if user is logged in
            if (isset($_SESSION['user']) && isset($_SESSION['user']['user_id'])) {
                echo '<form action="checkout.php" method="POST" style="display: inline;">';
                echo '<input type="hidden" name="checkout_token" value="' . bin2hex(random_bytes(32)) . '">';
                echo '<button type="submit" class="checkout-btn">Proceed to Checkout</button>';
                echo '</form>';
            } else {
                // Store current URL for redirect after login
                $_SESSION['redirect_after_login'] = 'checkout.php';
                echo '<a href="login.php" class="checkout-btn">Login to Checkout</a>';
            }
            echo '</div>';
        }
?>

</div>
</div>
<?php include 'footer.php'; ?>
