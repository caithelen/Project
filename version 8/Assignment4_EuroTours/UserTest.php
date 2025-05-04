<?php
use PHPUnit\Framework\TestCase;
require_once '../User.php';

class UserTest extends TestCase {
    public function testUserCredentials() {
        $user = new User('johnsmith', 'password123');
        $this->assertEquals('johnsmith', $user->getUsername());
        $this->assertTrue(method_exists($user, 'verifyPassword'));
    }
}
?>
