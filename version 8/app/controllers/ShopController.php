<?php
require_once __DIR__ . '/../models/TourTrip.php';

class ShopController {
    private $tripModel;

    public function __construct() {
        $this->tripModel = new TourTrip();
    }

    public function index() {
        try {
            $trips = $this->tripModel->findAvailable();
            require_once __DIR__ . '/../views/shop/index.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            require_once __DIR__ . '/../views/shop/index.php';
        }
    }

    public function view($id) {
        try {
            $trip = $this->tripModel->findById($id);
            if (!$trip) {
                throw new Exception('Trip not found');
            }
            require_once __DIR__ . '/../views/shop/view.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shop');
            exit;
        }
    }

    public function search() {
        try {
            $query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
            $sql = "SELECT * FROM tour_trips WHERE 
                   title LIKE :query OR 
                   description LIKE :query OR 
                   destination LIKE :query";
            
            $trips = $this->tripModel->query($sql, [
                'query' => "%$query%"
            ])->fetchAll();

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode($trips);
                exit;
            }

            require_once __DIR__ . '/../views/shop/search.php';
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }

            $_SESSION['error'] = $e->getMessage();
            header('Location: /shop');
            exit;
        }
    }
}
