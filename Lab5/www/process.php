<?php

require_once 'db.php';
require_once 'Participant.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.html');
    exit();
}

$name = trim($_POST['name'] ?? '');
$age = intval($_POST['age'] ?? 0);
$direction = trim($_POST['direction'] ?? '');
$hasExperience = isset($_POST['has_experience']) && $_POST['has_experience'] === '1';
$teamRole = $_POST['team_role'] ?? '';

$errors = [];
if (empty($name)) $errors[] = 'Имя обязательно';
if ($age < 3 || $age > 1000) $errors[] = 'Возраст должен быть от 3 до 1000';
if (empty($direction)) $errors[] = 'Выберите направление';
if (empty($teamRole)) $errors[] = 'Выберите роль в команде';

$validRoles = ['team_lead', 'developer', 'designer', 'analyst', 'other'];
if (!in_array($teamRole, $validRoles)) {
    $errors[] = 'Хм, ну у меня для тебя плохие новости. С ролями что-то не так';
}

if (!empty($errors)) {
    echo '<h2>Ошибки:</h2><ul>';
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo '</ul><a href="form.html">Вернуться к форме</a>';
    exit();
}

try {
    $participant = new Participant($pdo);
    $participant->add(
        htmlspecialchars($name),
        $age,
        htmlspecialchars($direction),
        $hasExperience,
        $teamRole
    );
    
    header('Location: index.php?success=1');
    exit();
    
} catch (Exception $e) {
    error_log("Ошибка при добавлении участника: " . $e->getMessage());
    echo "Произошла ошибка при сохранении данных.";
    echo '<br><a href="form.html">Попробовать снова</a>';
}