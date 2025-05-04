<?php
require_once __DIR__ . '/../config/Database.php';

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollback() {
        return $this->db->rollBack();
    }

    protected function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->fetchAll();
    }

    protected function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    protected function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":$field";
        }, $fields);

        $sql = "INSERT INTO {$this->table} (" . 
               implode(', ', $fields) . 
               ") VALUES (" . 
               implode(', ', $placeholders) . 
               ")";

        $this->query($sql, $data);
        return $this->db->lastInsertId();
    }

    protected function update($id, $data) {
        $fields = array_map(function($field) {
            return "$field = :$field";
        }, array_keys($data));

        $sql = "UPDATE {$this->table} SET " . 
               implode(', ', $fields) . 
               " WHERE id = :id";

        $data['id'] = $id;
        return $this->query($sql, $data);
    }

    protected function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->query($sql, ['id' => $id]);
    }
}
