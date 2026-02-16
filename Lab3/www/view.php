<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Все данные</title>
</head>
<body>
    <h2>Все сохранённые данные:</h2>
    <ul>
        <?php
        if (file_exists("data.txt")) {
            $lines = file("data.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!empty($lines)) {
                foreach ($lines as $line) {
                    $parts = explode(";", $line, 2);
                    if (count($parts) === 2) {
                        [$name, $role] = $parts;
                        echo "<li>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
                             " — роль: " . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . "</li>\n";
                    }
                }
            } else {
                echo "<li>Данных нет</li>";
            }
        } else {
            echo "<li>Файл данных ещё не создан</li>";
        }
        ?>
    </ul>
    <br>
    <a href="index.php">На главную</a>
</body>
</html>