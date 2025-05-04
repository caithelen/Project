<?php
require_once __DIR__ . '/../models/TourTrip.php';

class ScheduleController {
    private $trip;

    public function __construct() {
        $this->trip = new TourTrip();
    }

    public function index() {
        $trips = $this->trip->getUpcomingTrips();
        require_once __DIR__ . '/../views/schedule/index.php';
    }

    public function getLiveUpdates() {
        try {
            // Get all active trips
            $trips = $this->trip->getActiveTrips();
            
            // Simulate live updates for each trip
            $updates = [];
            foreach ($trips as $trip) {
                $departureTime = strtotime($trip['departure_date']);
                $currentTime = time();
                
                // Calculate progress for trips in progress
                if ($currentTime >= $departureTime && $currentTime <= $departureTime + ($trip['duration'] * 24 * 60 * 60)) {
                    $totalDuration = $trip['duration'] * 24 * 60 * 60;
                    $elapsed = $currentTime - $departureTime;
                    $progress = min(100, ($elapsed / $totalDuration) * 100);
                    
                    // Simulate current location
                    $location = $this->simulateLocation($trip['destination'], $progress);
                    
                    $updates[] = [
                        'trip_id' => $trip['id'],
                        'title' => $trip['title'],
                        'destination' => $trip['destination'],
                        'progress' => round($progress, 1),
                        'current_location' => $location,
                        'status' => $this->getTripStatus($progress),
                        'estimated_arrival' => date('H:i', $departureTime + $totalDuration)
                    ];
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'updates' => $updates]);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    private function simulateLocation($destination, $progress) {
        // Simplified simulation - you can enhance this with actual waypoints
        $waypoints = [
            'Paris' => ['Brussels', 'Luxembourg', 'Paris'],
            'Rome' => ['Venice', 'Florence', 'Rome'],
            'Barcelona' => ['Valencia', 'Zaragoza', 'Barcelona'],
            'Amsterdam' => ['Brussels', 'Antwerp', 'Amsterdam']
        ];

        $route = $waypoints[$destination] ?? [$destination];
        $index = floor(($progress / 100) * (count($route) - 1));
        return $route[$index];
    }

    private function getTripStatus($progress) {
        if ($progress < 1) return 'Preparing for departure';
        if ($progress < 25) return 'Just departed';
        if ($progress < 50) return 'En route';
        if ($progress < 75) return 'More than halfway';
        if ($progress < 99) return 'Approaching destination';
        return 'Arrived';
    }
}
