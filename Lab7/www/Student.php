<?php

require_once 'db.php';

class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getConnection();
    }
    
    public function create($name, $email) {
        try {
            $stmt = $this->db->prepare("INSERT INTO students (name, email) VALUES (:name, :email)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create student error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markAsProcessed($id) {
        $stmt = $this->db->prepare("UPDATE students SET processed_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}