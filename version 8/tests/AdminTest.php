<?php
require_once __DIR__ . '/../Admin.php';

class AdminTest {
    private $admin;

    public function __construct() {
        $this->admin = new Admin('admin_user', 'admin@eurotours.com', 'Sales', 2);
    }

    public function runTests() {
        $this->testAdminCreation();
        $this->testAccessLevelValidation();
        $this->testPermissionsBasedOnLevel();
        $this->testDepartmentManagement();
        $this->testTripValidation();
    }

    public function testAdminCreation() {
        echo "Testing Admin Creation... ";
        assert($this->admin->getUsername() === 'admin_user', "Username should be 'admin_user'");
        assert($this->admin->getEmail() === 'admin@eurotours.com', "Email should be 'admin@eurotours.com'");
        assert($this->admin->getDepartment() === 'Sales', "Department should be 'Sales'");
        assert($this->admin->getAccessLevel() === 2, "Access level should be 2");
        assert($this->admin->getRole() === 'admin', "Role should be 'admin'");
        echo "PASSED\n";
    }

    public function testAccessLevelValidation() {
        echo "Testing Access Level Validation... ";
        
        // Test invalid access levels
        try {
            $this->admin->setAccessLevel(0);
            assert(false, "Should throw exception for access level below 1");
        } catch (InvalidArgumentException $e) {
            assert($e->getMessage() === 'Access level must be between 1 and 3', "Should throw correct error message");
        }

        try {
            $this->admin->setAccessLevel(4);
            assert(false, "Should throw exception for access level above 3");
        } catch (InvalidArgumentException $e) {
            assert($e->getMessage() === 'Access level must be between 1 and 3', "Should throw correct error message");
        }

        // Test valid access levels
        $this->admin->setAccessLevel(1);
        assert($this->admin->getAccessLevel() === 1, "Access level should be updated to 1");
        
        $this->admin->setAccessLevel(3);
        assert($this->admin->getAccessLevel() === 3, "Access level should be updated to 3");
        
        echo "PASSED\n";
    }

    public function testPermissionsBasedOnLevel() {
        echo "Testing Permissions Based on Access Level... ";
        
        // Test level 1 permissions
        $this->admin->setAccessLevel(1);
        $level1Permissions = $this->admin->getPermissions();
        assert(in_array('view_trips', $level1Permissions), "Level 1 admin should have view_trips permission");
        assert(in_array('manage_trips', $level1Permissions), "Level 1 admin should have manage_trips permission");
        assert(!in_array('manage_admins', $level1Permissions), "Level 1 admin should not have manage_admins permission");
        
        // Test level 2 permissions
        $this->admin->setAccessLevel(2);
        $level2Permissions = $this->admin->getPermissions();
        assert(in_array('manage_admins', $level2Permissions), "Level 2 admin should have manage_admins permission");
        assert(in_array('system_settings', $level2Permissions), "Level 2 admin should have system_settings permission");
        
        echo "PASSED\n";
    }

    public function testDepartmentManagement() {
        echo "Testing Department Management... ";
        
        $this->admin->setDepartment('Marketing');
        assert($this->admin->getDepartment() === 'Marketing', "Department should be updated to Marketing");
        
        $this->admin->setDepartment('Support');
        assert($this->admin->getDepartment() === 'Support', "Department should be updated to Support");
        
        echo "PASSED\n";
    }

    public function testTripValidation() {
        echo "Testing Trip Validation... ";
        
        $validTrip = [
            'title' => 'Paris Adventure',
            'description' => 'Explore the city of lights',
            'price' => 499.99,
            'departure_date' => '2025-06-01'
        ];
        
        assert($this->admin->validateTripChanges($validTrip) === true, "Valid trip should pass validation");
        
        // Test missing required fields
        $invalidTrip = [
            'title' => 'Paris Adventure',
            'description' => 'Explore the city of lights'
            // Missing price and departure_date
        ];
        
        try {
            $this->admin->validateTripChanges($invalidTrip);
            assert(false, "Should throw exception for missing required fields");
        } catch (InvalidArgumentException $e) {
            assert(strpos($e->getMessage(), "Missing required field") === 0, "Should throw missing field error");
        }
        
        echo "PASSED\n";
    }
}

// Run the tests
echo "\nRunning Admin Class Unit Tests:\n";
$adminTest = new AdminTest();
$adminTest->runTests();
?>
