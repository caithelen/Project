<?php
require_once __DIR__ . '/Model.php';

class TourTrip extends Model {
    private $id;
    private $title;
    private $description;
    private $destination;
    private $price;
    private $departureDate;
    private $duration;
    private $maxParticipants;
    protected $table = 'tour_trips';

    public function __construct(array $data = []) {
        parent::__construct();
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    private function hydrate(array $data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['title'])) $this->title = $data['title'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['destination'])) $this->destination = $data['destination'];
        if (isset($data['price'])) $this->price = $data['price'];
        if (isset($data['departure_date'])) $this->departureDate = $data['departure_date'];
        if (isset($data['duration'])) $this->duration = $data['duration'];
        if (isset($data['max_participants'])) $this->maxParticipants = $data['max_participants'];
    }

    public function save() {
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'destination' => $this->destination,
            'price' => $this->price,
            'departure_date' => $this->departureDate,
            'duration' => $this->duration,
            'max_participants' => $this->maxParticipants
        ];

        if ($this->id) {
            return $this->update($this->id, $data);
        } else {
            $this->id = $this->create($data);
            return $this->id;
        }
    }

    public function findAvailable() {
        $sql = "SELECT t.*, 
                (SELECT COUNT(*) FROM bookings b WHERE b.trip_id = t.id) as booked
                FROM tour_trips t 
                WHERE t.departure_date > NOW()
                HAVING booked < t.max_participants
                ORDER BY t.departure_date";
        return $this->query($sql)->fetchAll();
    }

    public function getUpcomingTrips() {
        $sql = "SELECT * FROM tour_trips WHERE departure_date >= CURDATE() ORDER BY departure_date ASC";
        return $this->query($sql)->fetchAll();
    }

    public static function getActiveTrips() {
        $instance = new self();
        $sql = "SELECT * FROM tour_trips 
               WHERE departure_date <= NOW() 
               AND DATE_ADD(departure_date, INTERVAL duration DAY) >= NOW()";
        return $instance->query($sql)->fetchAll();
    }

    public function isAvailable() {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE trip_id = :trip_id";
        $result = $this->query($sql, ['trip_id' => $this->id])->fetch();
        return $result['count'] < $this->maxParticipants;
    }

    public function getBookings() {
        $sql = "SELECT b.*, c.username, c.email 
                FROM bookings b 
                JOIN customers c ON b.customer_id = c.id 
                WHERE b.trip_id = :trip_id 
                ORDER BY b.booking_date DESC";
        return $this->query($sql, ['trip_id' => $this->id])->fetchAll();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getDestination() { return $this->destination; }
    public function getPrice() { return $this->price; }
    public function getDepartureDate() { return $this->departureDate; }
    public function getDuration() { return $this->duration; }
    public function getMaxParticipants() { return $this->maxParticipants; }

    // Setters with validation
    public function setTitle($title) {
        if (empty($title)) {
            throw new InvalidArgumentException('Title cannot be empty');
        }
        $this->title = $title;
    }

    public function setDescription($description) {
        if (empty($description)) {
            throw new InvalidArgumentException('Description cannot be empty');
        }
        $this->description = $description;
    }

    public function setDestination($destination) {
        if (empty($destination)) {
            throw new InvalidArgumentException('Destination cannot be empty');
        }
        $this->destination = $destination;
    }

    public function setPrice($price) {
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException('Price must be a positive number');
        }
        $this->price = $price;
    }

    public function setDepartureDate($date) {
        $timestamp = strtotime($date);
        if ($timestamp === false || $timestamp < time()) {
            throw new InvalidArgumentException('Invalid departure date');
        }
        $this->departureDate = date('Y-m-d H:i:s', $timestamp);
    }

    public function setDuration($duration) {
        if (!is_numeric($duration) || $duration <= 0) {
            throw new InvalidArgumentException('Duration must be a positive number');
        }
        $this->duration = $duration;
    }

    public function setMaxParticipants($max) {
        if (!is_numeric($max) || $max <= 0) {
            throw new InvalidArgumentException('Maximum participants must be a positive number');
        }
        $this->maxParticipants = $max;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'destination' => $this->destination,
            'price' => $this->price,
            'departure_date' => $this->departureDate,
            'duration' => $this->duration,
            'max_participants' => $this->maxParticipants
        ];
    }
}
