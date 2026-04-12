<?php

class HacakthonRegistration {
    private ?PDO $db;

    public function __construct(?PDO $db = null) {
        $this->db = $db;
    }

    public function register(array $data): array {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $participantId = $this->saveToDatabase($data);
        
        if ($participantId) {
            return [
                'success' => true,
                'message' => "Участник {$data['name']} успешно зарегистрирован",
                'id' => $participantId
            ];
        }
        
        return ['success' => false, 'errors' => ['Database error']];
    }

    private function validate(array $data): array {
        $errors = [];
        
        if (empty($data['name']) || strlen($data['name']) < 2) {
            $errors[] = 'Имя должно содержать минимум 2 символа';
        }
        
        if (empty($data['age']) || $data['age'] < 14 || $data['age'] > 100) {
            $errors[] = 'Возраст должен быть от 14 до 100 лет';
        }
        
        $allowedDirections = ['backend', 'frontend', 'mobile', 'ai', 'design'];
        if (empty($data['direction']) || !in_array($data['direction'], $allowedDirections)) {
            $errors[] = 'Выберите корректное направление';
        }
        
        if (!isset($data['hasExperience'])) {
            $errors[] = 'Укажите наличие опыта участия';
        }
        
        $allowedRoles = ['team_lead', 'developer', 'designer', 'tester'];
        if (empty($data['role']) || !in_array($data['role'], $allowedRoles)) {
            $errors[] = 'Выберите корректную роль в команде';
        }
        
        return $errors;
    }

    private function saveToDatabase(array $data): ?int {
        if ($this->db === null) {
            return rand(1, 1000);
        }
        
        try {
            $sql = "INSERT INTO participants (name, age, direction, has_experience, role, created_at) 
                    VALUES (:name, :age, :direction, :has_experience, :role, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':age' => $data['age'],
                ':direction' => $data['direction'],
                ':has_experience' => $data['hasExperience'] ? 1 : 0,
                ':role' => $data['role']
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAllParticipants(): array {
        if ($this->db === null) {
            return [];
        }
        
        try {
            $stmt = $this->db->query("SELECT * FROM participants ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getParticipantById(int $id): ?array {
        if ($this->db === null) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM participants WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}