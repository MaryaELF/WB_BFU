CREATE TABLE IF NOT EXISTS hackathon_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT UNSIGNED NOT NULL,
    direction VARCHAR(100) NOT NULL,
    has_experience TINYINT(1) DEFAULT 0,
    team_role ENUM('team_lead', 'developer', 'designer', 'analyst', 'other') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;