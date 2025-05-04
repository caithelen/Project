<?php
class DatabaseConnection {
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        $this->config = require __DIR__ . '/db_config.php';
        $this->connect();
    }
    
    private function connect() {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
        
        try {
            $this->connection = new PDO(
                $dsn, 
                $this->config['username'], 
                $this->config['password'],
                $this->config['options']
            );
            
            // Verify connection is working
            $this->connection->query('SELECT 1');
            
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        try {
            // Test if connection is still alive
            $this->connection->query('SELECT 1');
            return $this->connection;
        } catch (PDOException $e) {
            // If connection lost, try to reconnect once
            error_log("Database connection lost, attempting to reconnect: " . $e->getMessage());
            $this->connect();
            return $this->connection;
        }
    }
    
    private function __clone() {}
    public function __wakeup() {
        if (self::$instance === null) {
            $this->config = require __DIR__ . '/db_config.php';
            $this->connect();
        }
    }
}
