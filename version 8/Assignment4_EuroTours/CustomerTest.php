<?php
use PHPUnit\Framework\TestCase;
require_once '../Customer.php';

class CustomerTest extends TestCase {
    public function testCustomerCreation() {
        $customer = new Customer('Jane Doe', 'jane@example.com');
        $this->assertEquals('Jane Doe', $customer->getName());
        $this->assertEquals('jane@example.com', $customer->getEmail());
    }
}
?>
