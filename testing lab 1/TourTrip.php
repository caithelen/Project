<?php
class TourTrip {
    private $destination;
    private $cost;

    public function __construct($destination, $cost) {
        $this->destination = $destination;
        $this->cost = $cost;
    }

    public function getDestination() {
        return $this->destination;
    }

    public function getCost() {
        return $this->cost;
    }
}
?>
