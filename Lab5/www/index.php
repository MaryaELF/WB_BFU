<?php
require_once 'db.php';
require_once 'Participant.php';

$participant = new Participant($pdo);
$all = $participant->getAll();
$success = isset($_GET['success']) && $_GET['success'] === '1';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Участники хакатона</title>
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
                        <span class="badge <?= $row['has_experience'] ? 'badge-yes' : 'badge-no' ?>">
                            <?= $row['has_experience'] ? 'Да' : 'Нет' ?>
                        </span>
                    </td>
                    <td><?= Participant::getRoleLabel($row['team_role']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <p><small>Всего участников: <strong><?= count($all) ?></strong></small></p>
</body>
</html>