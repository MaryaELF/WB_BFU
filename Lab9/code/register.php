<?php
require_once 'Database.php';
require_once 'HackathonRegistration.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $registration = new HackathonRegistration($pdo);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name' => $_POST['name'] ?? '',
            'age' => $_POST['age'] ?? '',
            'direction' => $_POST['direction'] ?? '',
            'hasExperience' => isset($_POST['hasExperience']),
            'role' => $_POST['role'] ?? ''
        ];
        
        $result = $registration->register($data);
        
        if ($result['success']) {
            header('Location: index.php?success=1');
        } else {
            // Показать ошибки
            echo "<h1>Ошибка регистрации</h1>";
            echo "<ul>";
            foreach ($result['errors'] as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo '<a href="form.html">Вернуться назад</a>';
        }
        exit;
    }
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}