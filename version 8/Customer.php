<?php
require_once 'User.php';

class Customer extends User {
    private $address;
    private $cart;
    private $bookings;
    private $loyaltyPoints;
    private $profile;

    public function __construct($username, $email, $address) {
        parent::__construct($username, $email);
        $this->address = $address;
        $this->cart = [];
        $this->bookings = [];
        $this->loyaltyPoints = 0;
        $this->profile = [
            'phone' => '',
            'address' => $address,
            'preferences' => []
        ];
    }

    public function getRole(): string {
        return 'customer';
    }

    public function getPermissions(): array {
        return [
            'view_trips',
            'book_trips',
            'view_own_bookings',
            'manage_cart',
            'update_profile'
        ];
    }

    // Cart Management
    public function addToCart($trip) {
        $this->cart[] = $trip;
    }

    public function removeFromCart($tripId) {
        foreach ($this->cart as $key => $item) {
            if ($item['trip_id'] === $tripId) {
                unset($this->cart[$key]);
                $this->cart = array_values($this->cart); // Reindex array
                break;
            }
        }
    }

    public function getCart() {
        return $this->cart;
    }

    public function clearCart() {
        $this->cart = [];
    }

    public function getCartTotal() {
        return array_sum(array_column($this->cart, 'cost'));
    }

    // Booking Management
    public function addBooking($booking) {
        $this->bookings[] = $booking;
    }

    public function getBookingHistory() {
        return $this->bookings;
    }

    public function getBookings() {
        return $this->bookings;
    }

    public function getBooking($bookingId) {
        foreach ($this->bookings as $booking) {
            if ($booking['booking_id'] === $bookingId) {
                return $booking;
            }
        }
        return null;
    }

    // Profile Management
    public function updateProfile($newProfile) {
        if (isset($newProfile['phone'])) {
            if (!preg_match('/^\+[0-9]{1,4}\s[0-9\s]{8,}$/', $newProfile['phone'])) {
                throw new InvalidArgumentException('Invalid phone number format');
            }
            $this->profile['phone'] = $newProfile['phone'];
        }

        if (isset($newProfile['address'])) {
            $this->profile['address'] = $newProfile['address'];
            $this->address = $newProfile['address']; // Update both profile and base address
        }

        if (isset($newProfile['preferences'])) {
            $this->profile['preferences'] = $newProfile['preferences'];
        }
    }

    public function getProfile() {
        return $this->profile;
    }

    // Loyalty Points Management
    public function getLoyaltyPoints() {
        return $this->loyaltyPoints;
    }

    public function addLoyaltyPoints($points) {
        $this->loyaltyPoints += $points;
    }

    public function redeemPoints($points) {
        if ($points > $this->loyaltyPoints) {
            throw new InvalidArgumentException('Insufficient points available');
        }
        $this->loyaltyPoints -= $points;
        return true;
    }

    // Customer specific methods
    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        $this->profile['address'] = $address; // Update both base address and profile
    }

    // Override parent method to include customer-specific data
    public function __serialize(): array {
        return array_merge(parent::__serialize(), [
            'address' => $this->address,
            'cart' => $this->cart,
            'bookings' => $this->bookings,
            'loyaltyPoints' => $this->loyaltyPoints,
            'profile' => $this->profile
        ]);
    }

    public function __unserialize(array $data): void {
        parent::__unserialize($data);
        $this->address = $data['address'];
        $this->cart = $data['cart'];
        $this->bookings = $data['bookings'];
        $this->loyaltyPoints = $data['loyaltyPoints'];
        $this->profile = $data['profile'];
    }

    public function toArray(): array {
        $data = parent::toArray();
        $data['address'] = $this->address;
        $data['role'] = $this->getRole();
        $data['loyalty_points'] = $this->loyaltyPoints;
        return $data;
    }
}
