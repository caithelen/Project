<?php
class TourTrip {
    private $destination;
    private $description;
    private $cost;
    private $image;

    public function __construct($destination, $description, $cost, $image = null) {
        $this->destination = $destination;
        $this->description = $description;
        $this->cost = $cost;
        $this->image = $image;
    }

    public function getDestination() { return $this->destination; }
    public function getDescription() { return $this->description; }
    public function getCost() { return $this->cost; }
    public function getImage() { return $this->image; }

    public function applyDiscount($percent) {
        $this->cost = $this->cost * (1 - $percent / 100);
    }
}
?>
