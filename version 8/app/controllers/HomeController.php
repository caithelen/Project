<?php
require_once __DIR__ . '/../models/TourTrip.php';

class HomeController {
    private $tripModel;

    public function __construct() {
        $this->tripModel = new TourTrip();
    }

    public function index() {
        try {
            // Get featured trips (latest 3 available trips)
            $sql = "SELECT t.*, 
                    (SELECT COUNT(*) FROM bookings b WHERE b.trip_id = t.id) as booked
                    FROM tour_trips t 
                    WHERE t.departure_date > NOW()
                    HAVING booked < t.max_participants
                    ORDER BY t.departure_date
                    LIMIT 3";
            
            $featuredTrips = $this->tripModel->query($sql)->fetchAll();
            require_once __DIR__ . '/../views/home/index.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            require_once __DIR__ . '/../views/home/index.php';
        }
    }

    public function about() {
        require_once __DIR__ . '/../views/home/about.php';
    }

    public function contact() {
        require_once __DIR__ . '/../views/home/contact.php';
    }
}
