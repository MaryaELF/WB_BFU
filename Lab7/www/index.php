<?php

require_once 'Student.php';

$student = new Student();
$students = $student->getAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Асинхронная обработка данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Форма</h2>
            </div>
            <div class="card-body">
                
                <div id="alert" class="alert"></div>
                
                <form id="studentForm">
                    <div class="form-group">
                        <label for="name">ФИО студента:</label>
                        <input type="text" id="name" name="name" required placeholder="Введите ФИО">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required placeholder="student@example.com">
                    </div>
                    <button type="submit">Отправить на обработку</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Список студентов</h2>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Дата создания</th>
                            <th>Дата обработки</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Нет данных</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                                <td><?php echo $student['processed_at'] ? htmlspecialchars($student['processed_at']) : '-'; ?></td>
                                <td>
                                    <?php if ($student['processed_at']): ?>
                                        <span class="status-processed">Обработано</span>
                                    <?php else: ?>
                                        <span class="status-pending">В очереди</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('studentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const alert = document.getElementById('alert');
            
            try {
                const response = await fetch('send.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`
                });
                
                const result = await response.text();
                
                alert.className = 'alert alert-success';
                alert.style.display = 'block';
                alert.textContent = 'Сообщение отправлено в очередь Kafka. Данные будут обработаны в ближайшее время.';
                
                document.getElementById('studentForm').reset();
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } catch (error) {
                alert.className = 'alert alert-error';
                alert.style.display = 'block';
                alert.textContent = 'Ошибка при отправке сообщения';
            }
        });
    </script>
</body>
</html>