<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $config;

    private function __construct() {
        $this->config = require __DIR__ . '/db_config.php';
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }



    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevent cloning of the instance
    public function __clone() {
        throw new Exception('Cloning is not allowed.');
    }

    // Prevent unserialize of the instance
    public function __wakeup() {
        throw new Exception('Unserialize is not allowed.');
    }
}
