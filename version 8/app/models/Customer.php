<?php
require_once __DIR__ . '/User.php';

class Customer extends User {
    private $phone;
    private $address;
    protected $table = 'customers';

    public function __construct($username = '', $email = '', $phone = '', $address = '') {
        parent::__construct($username, $email);
        $this->phone = $phone;
        $this->address = $address;
    }

    public function getRole(): string {
        return 'customer';
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        // Basic phone validation
        if (!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
        $this->phone = $phone;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function save() {
        $data = parent::toArray();
        $data['phone'] = $this->phone;
        $data['address'] = $this->address;
        
        if ($this->id) {
            return $this->update($this->id, $data);
        } else {
            $this->id = $this->create($data);
            return $this->id;
        }
    }

    public function getBookings() {
        $sql = "SELECT b.* FROM bookings b 
                WHERE b.customer_id = :customer_id 
                ORDER BY b.booking_date DESC";
        return $this->query($sql, ['customer_id' => $this->id])->fetchAll();
    }

    public function __serialize(): array {
        return array_merge(parent::__serialize(), [
            'phone' => $this->phone,
            'address' => $this->address
        ]);
    }

    public function __unserialize(array $data): void {
        parent::__unserialize($data);
        $this->phone = $data['phone'];
        $this->address = $data['address'];
    }

    public function toArray(): array {
        $data = parent::toArray();
        $data['phone'] = $this->phone;
        $data['address'] = $this->address;
        $data['role'] = $this->getRole();
        return $data;
    }
}
