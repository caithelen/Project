<?php
require_once __DIR__ . '/Model.php';

class Cart extends Model {
    private $items = [];
    private $total = 0;
    private $discount = 0;
    private const MAX_ITEMS = 5;
    private const MAX_TOTAL = 5000.00;
    protected $table = 'cart_items';

    public function __construct($loadFromSession = true) {
        if (!session_id()) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->items = &$_SESSION['cart'];
        parent::__construct();
        if ($loadFromSession) {
            $this->loadFromSession();
        }
    }

    public function loadFromSession() {
        $this->items = $_SESSION['cart'];
        $this->calculateTotal();
    }

    public function addItem($item) {
        // Sanitize input
        $item = array_map('htmlspecialchars', $item);
        
        // Check required fields
        $requiredFields = ['trip_id', 'destination', 'cost'];
        foreach ($requiredFields as $field) {
            if (!isset($item[$field])) {
                throw new InvalidArgumentException('Missing required field: ' . $field);
            }
        }

        // Validate cost
        if (!is_numeric($item['cost']) || $item['cost'] <= 0) {
            throw new InvalidArgumentException('Invalid cost: must be a positive number');
        }

        // Check for duplicate items using database
        $stmt = $this->query(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE trip_id = :trip_id AND session_id = :session_id",
            ['trip_id' => $item['trip_id'], 'session_id' => session_id()]
        );
        if ($stmt->fetch()['count'] > 0) {
            throw new InvalidArgumentException('Item already in cart: trip_id ' . $item['trip_id']);
        }

        // Check cart limits
        if (count($this->items) >= self::MAX_ITEMS) {
            throw new InvalidArgumentException('Cart is full: maximum ' . self::MAX_ITEMS . ' items allowed');
        }

        // Check if adding this item would exceed the maximum total
        $newTotal = $this->getSubtotal() + $item['cost'];
        if ($newTotal > self::MAX_TOTAL) {
            throw new InvalidArgumentException('Exceeds maximum cart value: ' . self::MAX_TOTAL);
        }

        // Add to database
        $this->create([
            'trip_id' => $item['trip_id'],
            'session_id' => session_id(),
            'destination' => $item['destination'],
            'cost' => $item['cost'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->items[] = $item;
        $this->saveToSession();
        $this->calculateTotal();
    }

    public function removeItem($index) {
        if (isset($this->items[$index])) {
            $item = $this->items[$index];
            
            // Remove from database
            $this->query(
                "DELETE FROM {$this->table} WHERE trip_id = :trip_id AND session_id = :session_id",
                ['trip_id' => $item['trip_id'], 'session_id' => session_id()]
            );

            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->saveToSession();
            $this->calculateTotal();
            return true;
        }
        return false;
    }

    public function clear() {
        // Clear from database
        $this->query(
            "DELETE FROM {$this->table} WHERE session_id = :session_id",
            ['session_id' => session_id()]
        );

        $this->items = [];
        $this->total = 0;
        $this->discount = 0;
        $this->saveToSession();
    }

    private function saveToSession() {
        $_SESSION['cart'] = $this->items;
    }

    public function getItems() {
        return $this->items;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getCount() {
        return count($this->items);
    }

    private function calculateTotal() {
        $subtotal = $this->getSubtotal();
        $tax = $this->calculateTax();
        $this->total = round($subtotal + $tax, 2);
        
        if ($this->discount > 0) {
            $this->total = round($this->total * (1 - ($this->discount / 100)), 2);
        }
    }

    private function getSubtotal() {
        return array_reduce($this->items, function($sum, $item) {
            return $sum + $item['cost'];
        }, 0);
    }

    private function calculateTax() {
        return round($this->getSubtotal() * 0.20, 2); // 20% tax rate
    }

    public function setDiscount($percentage) {
        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Invalid discount percentage');
        }
        $this->discount = $percentage;
        $this->calculateTotal();
    }
}
