<?php
class Cart {
    private $items = [];
    private $db;

    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->items = &$_SESSION['cart'];
        $this->db = Database::getInstance()->getConnection();
    }

    public function addItem($item) {
        if (!isset($item['trip_id']) || !isset($item['destination']) || !isset($item['cost'])) {
            throw new InvalidArgumentException('Missing required fields for cart item');
        }

        $tripId = $item['trip_id'];
        if (!isset($this->items[$tripId])) {
            $this->items[$tripId] = $item;
            $this->items[$tripId]['quantity'] = 1;
        } else {
            $this->items[$tripId]['quantity']++;
            $this->items[$tripId]['cost'] += $item['cost'];
        }
    }

    public function removeItem($tripId) {
        if (isset($this->items[$tripId])) {
            unset($this->items[$tripId]);
        }
    }

    public function clear() {
        $this->items = [];
        $_SESSION['cart'] = [];
    }

    public function getItems() {
        return $this->items;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['cost'] * $item['quantity'];
        }
        return $total;
    }

    public function isEmpty() {
        return empty($this->items);
    }
}
