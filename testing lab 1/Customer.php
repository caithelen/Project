<?php
class Customer {
    private $username;
    private $email;
    private $address;

    public function __construct($username, $email, $address) {
        $this->username = $username;
        $this->email = $email;
        $this->address = $address;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getAddress() {
        return $this->address;
    }
}
?>
