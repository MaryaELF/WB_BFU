<?php
session_start();

require_once 'ApiClient.php';
require_once 'UserInfo.php';

$cacheFile = 'api_cache.json';
$cacheTtl = 300;
$forceRefresh = isset($_GET['refresh']); 

if (!$forceRefresh && file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTtl) {
    $apiData = json_decode(file_get_contents($cacheFile), true);
    $source = 'данные из кеша';
} else {
    $api = new ApiClient();
    $url = 'https://api.github.com/repositories?per_page=5'; 
    $apiData = $api->request($url);
    
    if (!isset($apiData['error'])) {
        file_put_contents($cacheFile, json_encode($apiData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    $source = 'сеть из API';
}
$userInfo = UserInfo::getInfo();

if (!isset($_COOKIE['last_submission'])) {
    setcookie('last_submission', date('Y-m-d H:i:s'), time() + 3600, '/');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>GitHub API</title>
</head>
<body>
    <h1>Последние репозитории GitHub</h1>
    <p class="meta">Источник данных: <?= $source ?></p>

    <button onclick="refreshData()">Обновить данные</button>
    <div id="refresh-status"></div>

    <?php if (!empty($apiData['error'])): ?>
        <div class="error">
            <strong>Ошибка API:</strong> <?= htmlspecialchars($apiData['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($apiData) && !isset($apiData['error'])): ?>
        <?php foreach ($apiData as $repo): ?>
            <div class="repo">
                <strong>
                    <a href="<?= htmlspecialchars($repo['html_url']) ?>" target="_blank">
                        <?= htmlspecialchars($repo['full_name']) ?>
                    </a>
                </strong>
                <div class="meta">
                     <?= $repo['stargazers_count'] ?> | 
                     <?= $repo['forks_count'] ?> | 
                     <?= htmlspecialchars($repo['language'] ?? 'N/A') ?>
                </div>
                <?php if (!empty($repo['description'])): ?>
                    <p><?= htmlspecialchars($repo['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="card">
        <h3> Информация о пользователе</h3>
        <?php foreach ($userInfo as $key => $val): ?>
            <strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($val) ?><br>
        <?php endforeach; ?>
    </div>

    <script>
    function refreshData() {
        const status = document.getElementById('refresh-status');
        status.textContent = 'Загрузка...';
        
        fetch('index.php?refresh=1')
            .then(r => r.text())
            .then(html => {
                location.reload();
            })
            .catch(err => {
                status.textContent = 'Ошибка: ' + err.message;
            });
    }
    </script>
</body>
</html>