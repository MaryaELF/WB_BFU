<?php

require_once 'Database.php';
require_once 'HackathonRegistration.php';  // Исправлено название файла

// Создание подключения к БД
try {
    $database = new Database();
    $pdo = $database->getConnection();
    $hackathonRegistration = new HackathonRegistration($pdo);  // Исправлено название класса
    
    // API обработка
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST' && strpos($_SERVER['REQUEST_URI'], '/api') !== false) {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $result = $hackathonRegistration->register($input);
        echo json_encode($result);
        exit;
    }
    
    if ($method === 'GET' && isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/api') !== false) {
        header('Content-Type: application/json');
        $participant = $hackathonRegistration->getParticipantById($_GET['id']);
        echo json_encode($participant);
        exit;
    }
    
    if ($method === 'GET' && strpos($_SERVER['REQUEST_URI'], '/api') !== false) {
        header('Content-Type: application/json');
        $participants = $hackathonRegistration->getAllParticipants();
        echo json_encode($participants);
        exit;
    }
    
    // HTML отображение
    $all = $hackathonRegistration->getAllParticipants();  // Исправлено название метода
    $success = isset($_GET['success']) && $_GET['success'] === '1';
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Участники хакатона</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { display: inline-block; padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .empty { color: #666; padding: 20px; text-align: center; }
        .badge-yes { background: #28a745; color: white; padding: 2px 8px; border-radius: 4px; }
        .badge-no { background: #dc3545; color: white; padding: 2px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Участники хакатона</h1>
    
    <?php if ($success): ?>
        <div class="success">Регистрация успешна</div>
    <?php endif; ?>
    
    <p><a href="form.html" class="btn">Добавить участника</a></p>

    <?php if (empty($all)): ?>
        <div class="empty">Пока нет зарегистрированных участников</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Возраст</th>
                    <th>Направление</th>
                    <th>Опыт</th>
                    <th>Роль</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td><?= (int)$row['age'] ?></td>
                    <td><?= htmlspecialchars($row['direction']) ?></td>
                    <td>
                        <span class="<?= $row['has_experience'] ? 'badge-yes' : 'badge-no' ?>">
                            <?= $row['has_experience'] ? 'Да' : 'Нет' ?>
                        </span>
                    </td>
                    <td><?= HackathonRegistration::getRoleLabel($row['team_role']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <p><small>Всего участников: <strong><?= count($all) ?></strong></small></p>
</body>
</html>