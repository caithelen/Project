<?php
require_once __DIR__ . '/User.php';

class Admin extends User {
    private $department;
    private $accessLevel;

    public function __construct($username = '', $email = '', $department = '', $accessLevel = 1) {
        parent::__construct($username, $email);
        $this->department = $department;
        $this->accessLevel = $accessLevel;
    }

    public function getRole(): string {
        return 'admin';
    }

    public function getPermissions(): array {
        $permissions = [
            'view_trips',
            'manage_trips',
            'view_all_bookings',
            'manage_customers',
            'view_reports'
        ];

        if ($this->accessLevel >= 2) {
            $permissions[] = 'manage_admins';
            $permissions[] = 'system_settings';
        }

        return $permissions;
    }

    public function getDepartment() {
        return $this->department;
    }

    public function setDepartment($department) {
        $this->department = $department;
    }

    public function getAccessLevel() {
        return $this->accessLevel;
    }

    public function setAccessLevel($level) {
        if (!is_numeric($level) || $level < 1 || $level > 3) {
            throw new InvalidArgumentException('Access level must be between 1 and 3');
        }
        $this->accessLevel = $level;
    }

    public function save() {
        $data = parent::toArray();
        $data['department'] = $this->department;
        $data['access_level'] = $this->accessLevel;
        
        if ($this->id) {
            return $this->update($this->id, $data);
        } else {
            $this->id = $this->create($data);
            return $this->id;
        }
    }

    public function validateTripChanges(array $changes): bool {
        // Validate trip modifications
        $requiredFields = ['title', 'description', 'price', 'departure_date'];
        foreach ($requiredFields as $field) {
            if (!isset($changes[$field]) || empty($changes[$field])) {
                throw new InvalidArgumentException("Missing required field: {$field}");
            }
        }
        return true;
    }

    public function __serialize(): array {
        return array_merge(parent::__serialize(), [
            'department' => $this->department,
            'access_level' => $this->accessLevel
        ]);
    }

    public function __unserialize(array $data): void {
        parent::__unserialize($data);
        $this->department = $data['department'];
        $this->accessLevel = $data['access_level'];
    }

    public function toArray(): array {
        $data = parent::toArray();
        $data['department'] = $this->department;
        $data['access_level'] = $this->accessLevel;
        $data['role'] = $this->getRole();
        return $data;
    }
}
