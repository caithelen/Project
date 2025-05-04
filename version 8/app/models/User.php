<?php
require_once __DIR__ . '/Model.php';

abstract class User extends Model {
    protected $id;
    protected $username;
    protected $email;
    protected $password;
    protected $table = 'users';

    public function __construct($username = '', $email = '') {
        parent::__construct();
        $this->username = $username;
        $this->email = $email;
    }

    abstract public function getRole(): string;

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
    }

    public function setPassword($password) {
        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long');
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password): bool {
        return password_verify($password, $this->password);
    }

    public function save() {
        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->getRole()
        ];

        if ($this->id) {
            return $this->update($this->id, $data);
        } else {
            $this->id = $this->create($data);
            return $this->id;
        }
    }

    public static function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->query($sql, ['email' => $email]);
        return $stmt->fetch();
    }

    public function __serialize(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
    }

    public function __unserialize(array $data): void {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
    }
}
