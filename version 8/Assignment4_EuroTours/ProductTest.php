<?php
use PHPUnit\Framework\TestCase;
require_once '../Product.php';

class ProductTest extends TestCase {
    public function testProductAttributes() {
        $product = new Product(10, 'Travel Insurance', 19.99);
        $this->assertEquals(10, $product->getId());
        $this->assertEquals('Travel Insurance', $product->getName());
        $this->assertEquals(19.99, $product->getPrice());
    }
}
?>
