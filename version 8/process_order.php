<?php
session_start();
require_once __DIR__ . '/app/config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
    $_SESSION['error_message'] = 'Please login to complete your order.';
    header('Location: login.php');
    exit;
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error_message'] = 'Your cart is empty.';
    header('Location: cart.php');
    exit;
}

// Validate form data
$required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'postal_code'];
$errors = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

// Validate email
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
}

// Validate phone (basic validation)
if (!preg_match('/^[0-9]{10,}$/', $_POST['phone'])) {
    $errors[] = 'Invalid phone number';
}

if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['form_data'] = $_POST; // Save form data for repopulation
    header('Location: checkout.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $pdo->beginTransaction();

    // Calculate total amount
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += isset($item['cost']) ? (float)$item['cost'] : 0;
    }

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO eurotours_orders (user_id, total_amount, billing_first_name, billing_last_name, billing_email, billing_phone, billing_address, billing_city, billing_postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_SESSION['user']['user_id'],
        $total_amount,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['city'],
        $_POST['postal_code']
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, trip_id, price) VALUES (?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $item) {
        $stmt->execute([
            $order_id,
            $item['trip_id'],
            $item['cost']
        ]);
    }

    $pdo->commit();

    // Clear cart after successful order
    unset($_SESSION['cart']);
    unset($_SESSION['checkout_started']);

    // Set success message and redirect to confirmation page
    $_SESSION['success_message'] = 'Your order has been placed successfully!';
    header('Location: confirmation.php?order_id=' . $order_id);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Order processing error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while processing your order. Please try again.';
    header('Location: checkout.php');
    exit;
}
?>
    header('Location: checkout.php');
    exit;
}
?>
