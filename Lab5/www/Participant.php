<?php

class Participant {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function add(string $name, int $age, string $direction, bool $hasExperience, string $teamRole): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO hackathon_participants (name, age, direction, has_experience, team_role) 
             VALUES (:name, :age, :direction, :has_experience, :team_role)"
        );
        return $stmt->execute([
            ':name' => $name,
            ':age' => $age,
            ':direction' => $direction,
            ':has_experience' => $hasExperience ? 1 : 0,
            ':team_role' => $teamRole
        ]);
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM hackathon_participants ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM hackathon_participants WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function update(int $id, string $name, int $age, string $direction, bool $hasExperience, string $teamRole): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE hackathon_participants 
             SET name = :name, age = :age, direction = :direction, 
                 has_experience = :has_experience, team_role = :team_role 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':age' => $age,
            ':direction' => $direction,
            ':has_experience' => $hasExperience ? 1 : 0,
            ':team_role' => $teamRole
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM hackathon_participants WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function getRoleLabel(string $role): string {
        $roles = [
            'team_lead' => 'Тимлид',
            'developer' => 'Разработчик',
            'designer' => 'Дизайнер',
            'analyst' => 'Аналитик',
            'other' => 'Другое'
        ];
        return $roles[$role] ?? $role;
    }
}