<?php
require_once 'User.php';

class Customer extends User {
    private $address;

    public function __construct($username, $email, $address) {
        parent::__construct($username, $email);
        $this->address = $address;
    }

    public function getAddress() { return $this->address; }
}
?>
