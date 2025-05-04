<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Customer.php';

class CheckoutController {
    private $cart;
    private $booking;
    private $customer;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $this->cart = new Cart();
        $this->booking = new Booking();
        $this->customer = new Customer();
    }

    public function index() {
        $items = $this->cart->getItems();
        if (empty($items)) {
            $_SESSION['error'] = 'Your cart is empty';
            header('Location: /cart');
            exit;
        }
        require_once __DIR__ . '/../views/checkout/index.php';
    }

    public function process() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // Start transaction
            $this->booking->beginTransaction();

            try {
                // Validate cart
                $items = $this->cart->getItems();
                if (empty($items)) {
                    throw new Exception('Your cart is empty');
                }

                // Process each item in cart as a booking
                $bookings = [];
                foreach ($items as $item) {
                    require_once __DIR__ . '/../models/Validator.php';
                
                // Validate customer data
                $phone = Validator::validatePhone($_POST['phone'] ?? '');
                $address = Validator::sanitizeInput($_POST['address'] ?? '');
                
                if (empty($address)) {
                    throw new Exception('Shipping address is required');
                }
                
                $bookingData = [
                        'customer_id' => $_SESSION['user_id'],
                        'phone' => $phone,
                        'address' => $address,
                        'trip_id' => $item['trip_id'],
                        'total_amount' => $item['cost'],
                        'payment_status' => 'completed',
                        'booking_date' => date('Y-m-d H:i:s'),
                        'status' => 'confirmed'
                    ];

                    $bookingId = $this->booking->create($bookingData);
                    if (!$bookingId) {
                        throw new Exception('Failed to create booking');
                    }
                    $bookings[] = $this->booking->getBookingDetails($bookingId);
                }

                // Clear the cart after successful booking
                $this->cart->clear();

                // Commit transaction
                $this->booking->commit();

                // Store bookings in session for confirmation page
                $_SESSION['recent_bookings'] = $bookings;
                $_SESSION['success'] = 'Your booking has been confirmed!';

                // Redirect to confirmation page
                header('Location: /checkout/confirmation');
                exit;

            } catch (Exception $e) {
                // Rollback transaction on error
                $this->booking->rollback();
                throw $e;
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /checkout');
            exit;
        }
    }

    public function confirmation() {
        if (!isset($_SESSION['recent_bookings'])) {
            header('Location: /shop');
            exit;
        }

        $bookings = $_SESSION['recent_bookings'];
        // Clear the bookings from session after displaying
        unset($_SESSION['recent_bookings']);

        require_once __DIR__ . '/../views/checkout/confirmation.php';
    }
}
