<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Главная</title>
</head>
<body>
    <h1>Хакатон</h1>

    <?php if (isset($_SESSION['errors'])): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['username'])): ?>
        <p>Данные из сессии:</p>
        <ul>
            <li>Имя: <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></li>
            <li>Роль: <?= htmlspecialchars($_SESSION['role_team'], ENT_QUOTES, 'UTF-8') ?></li>
        </ul>
    <?php else: ?>
        <p>Данных пока нет.</p>
    <?php endif; ?>

    <br>
    <a href="index.html">Заполнить форму</a> 
    <a href="view.php">Посмотреть все данные</a>
</body>
</html>