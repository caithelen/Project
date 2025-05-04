<?php
require_once __DIR__ . '/../models/Cart.php';

class CartController {
    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    public function index() {
        $items = $this->cart->getItems();
        $total = $this->cart->getTotal();
        
        // Include the view
        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function add() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $item = [
                'trip_id' => filter_input(INPUT_POST, 'trip_id', FILTER_SANITIZE_NUMBER_INT),
                'destination' => filter_input(INPUT_POST, 'destination', FILTER_SANITIZE_SPECIAL_CHARS),
                'cost' => filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
            ];

            $this->cart->addItem($item);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Item added to cart']);
            } else {
                header('Location: /cart');
            }
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /cart');
            }
        }
    }

    public function remove() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $index = filter_input(INPUT_POST, 'index', FILTER_SANITIZE_NUMBER_INT);
            if ($index === false || $index === null) {
                throw new Exception('Invalid item index');
            }

            $success = $this->cart->removeItem($index);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => $success]);
            } else {
                header('Location: /cart');
            }
        } catch (Exception $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            } else {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /cart');
            }
        }
    }

    public function clear() {
        $this->cart->clear();
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Location: /cart');
        }
    }
}
