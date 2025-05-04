<?php
require_once __DIR__ . '/Model.php';

class Booking extends Model {
    protected $table = 'bookings';
    private $id;
    private $customerId;
    private $tripId;
    private $bookingDate;
    private $status;
    private $totalAmount;
    private $confirmationCode;

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $data['booking_date'] = date('Y-m-d H:i:s');
        $data['confirmation_code'] = $this->generateConfirmationCode();
        $data['status'] = 'confirmed';
        
        return parent::create($data);
    }

    private function generateConfirmationCode() {
        // Generate a unique booking reference number
        $prefix = 'ET'; // EuroTours
        $timestamp = time();
        $random = rand(1000, 9999);
        return $prefix . $timestamp . $random;
    }

    public function getBookingDetails($bookingId) {
        $sql = "SELECT b.*, t.title, t.destination, t.departure_date, t.duration,
                c.username, c.email, c.phone
                FROM bookings b
                JOIN tour_trips t ON b.trip_id = t.id
                JOIN customers c ON b.customer_id = c.id
                WHERE b.id = :booking_id";
        
        return $this->query($sql, ['booking_id' => $bookingId])->fetch();
    }

    public function getCustomerBookings($customerId) {
        $sql = "SELECT b.*, t.title, t.destination, t.departure_date, t.duration
                FROM bookings b
                JOIN tour_trips t ON b.trip_id = t.id
                WHERE b.customer_id = :customer_id
                ORDER BY b.booking_date DESC";
        
        return $this->query($sql, ['customer_id' => $customerId])->fetchAll();
    }

    public function updateStatus($bookingId, $status) {
        return $this->update($bookingId, ['status' => $status]);
    }
}
