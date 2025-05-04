<?php
abstract class User {
    public function __serialize(): array {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'id' => $this->id,
            'created_at' => $this->created_at,
            'last_login' => $this->last_login
        ];
    }

    public function __unserialize(array $data): void {
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->id = $data['id'];
        $this->created_at = $data['created_at'];
        $this->last_login = $data['last_login'];
    }
    protected $username;
    protected $email;
    protected $id;
    protected $created_at;
    protected $last_login;

    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
        $this->created_at = date('Y-m-d H:i:s');
        $this->last_login = null;
    }

    // Common getters/setters
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getId() { return $this->id; }
    public function getCreatedAt() { return $this->created_at; }
    public function getLastLogin() { return $this->last_login; }

    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }

    public function validatePassword(string $password): bool {
        // Password must be at least 8 characters long and contain:
        // - At least one uppercase letter
        // - At least one lowercase letter
        // - At least one number
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        
        if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            throw new InvalidArgumentException(
                'Password must be at least 8 characters long and contain at least one uppercase letter, '.
                'one lowercase letter, and one number'
            );
        }
        return true;
    }

    public function isActive(): bool {
        // Consider user inactive if they haven't logged in for 30 days
        if (!$this->last_login) return true; // New users are active by default
        
        $thirtyDaysAgo = new DateTime('-30 days');
        $lastLogin = new DateTime($this->last_login);
        return $lastLogin > $thirtyDaysAgo;
    }
    public function setId($id) { $this->id = $id; }
    
    // Abstract methods that child classes must implement
    abstract public function getRole(): string;
    abstract public function getPermissions(): array;
    
    // Common functionality
    public function updateLastLogin() {
        $this->last_login = date('Y-m-d H:i:s');
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'last_login' => $this->last_login
        ];
    }
}
