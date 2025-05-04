<?php
require_once __DIR__ . '/../models/Booking.php';

class BookingController {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    b.id as booking_id,
                    b.booking_date,
                    b.status as booking_status,
                    b.total_amount,
                    b.confirmation_code,
                    t.title,
                    t.destination,
                    t.departure_date,
                    t.price
                FROM bookings b
                JOIN tour_trips t ON b.trip_id = t.id
                WHERE b.customer_id = ?
                ORDER BY b.booking_date DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../views/bookings/index.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Error retrieving bookings: ' . $e->getMessage();
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public function cancel() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_SANITIZE_NUMBER_INT);
            if (!$bookingId) {
                throw new Exception('Invalid booking ID');
            }

            // Get booking details to verify ownership
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$booking || $booking['customer_id'] != $_SESSION['user_id']) {
                throw new Exception('Booking not found or unauthorized');
            }

            // Check if booking can be cancelled (e.g., not within 24 hours of departure)
            $departureTime = strtotime($booking['departure_date']);
            if (time() > $departureTime - (24 * 60 * 60)) {
                throw new Exception('Bookings cannot be cancelled within 24 hours of departure');
            }

            // Update booking status to cancelled
            $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$bookingId]);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
                exit;
            }

            $_SESSION['success'] = 'Booking cancelled successfully';
            header('Location: /version 7.0/my-bookings');
            exit;

        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }

            $_SESSION['error'] = $e->getMessage();
            header('Location: /version 7.0/my-bookings');
            exit;
        }
    }
}
