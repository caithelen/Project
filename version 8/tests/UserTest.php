<?php
require_once __DIR__ . '/../User.php';

class TestUser extends User {
    public function getRole(): string {
        return 'test';
    }
    
    public function getPermissions(): array {
        return ['test_permission'];
    }
}

class UserTest {
    private $user;

    public function __construct() {
        $this->user = new TestUser('testuser', 'test@example.com');
    }

    public function runTests() {
        $this->testUserCreation();
        $this->testEmailValidation();
        $this->testLastLoginUpdate();
        $this->testSerialization();
        $this->testPasswordValidation();
    }

    public function testUserCreation() {
        echo "Testing User Creation... ";
        assert($this->user->getUsername() === 'testuser', "Username should be 'testuser'");
        assert($this->user->getEmail() === 'test@example.com', "Email should be 'test@example.com'");
        assert($this->user->getRole() === 'test', "Role should be 'test'");
        echo "PASSED\n";
    }

    public function testEmailValidation() {
        echo "Testing Email Validation... ";
        try {
            $this->user->setEmail('invalid-email');
            assert(false, "Should throw exception for invalid email");
        } catch (InvalidArgumentException $e) {
            assert($e->getMessage() === 'Invalid email format', "Should throw correct error message");
        }
        
        $this->user->setEmail('valid@email.com');
        assert($this->user->getEmail() === 'valid@email.com', "Email should be updated for valid email");
        echo "PASSED\n";
    }

    public function testLastLoginUpdate() {
        echo "Testing Last Login Update... ";
        $this->user->updateLastLogin();
        assert($this->user->getLastLogin() !== null, "Last login should be updated");
        assert(strtotime($this->user->getLastLogin()) <= time(), "Last login should be in the past");
        echo "PASSED\n";
    }

    public function testSerialization() {
        echo "Testing Serialization... ";
        $serialized = serialize($this->user);
        $unserialized = unserialize($serialized);
        
        assert($unserialized->getUsername() === $this->user->getUsername(), "Username should survive serialization");
        assert($unserialized->getEmail() === $this->user->getEmail(), "Email should survive serialization");
        echo "PASSED\n";
    }

    public function testPasswordValidation() {
        echo "Testing Password Validation... ";
        try {
            $this->user->validatePassword('weak');
            assert(false, "Should throw exception for weak password");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), 'Password must be') === 0, "Should throw password requirements message");
        }
        
        assert($this->user->validatePassword('StrongPass123') === true, "Should accept valid password");
        echo "PASSED\n";
    }
}

// Run the tests
echo "\nRunning User Class Unit Tests:\n";
$userTest = new UserTest();
$userTest->runTests();
?>
