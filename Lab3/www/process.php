<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$errors = [];

$username = trim($_POST['username'] ?? '');
$role_team = trim($_POST['role_team'] ?? '');

if (empty($username)) {
    $errors[] = "Имя не может быть пустым";
}
if (empty($role_team)) {
    $errors[] = "Роль в команде обязательна";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
$role_team = htmlspecialchars($role_team, ENT_QUOTES, 'UTF-8');

$_SESSION['username'] = $username;
$_SESSION['role_team'] = $role_team;

$line = $username . ";" . $role_team . "\n";
file_put_contents("data.txt", $line, FILE_APPEND | LOCK_EX);

header("Location: index.php");
exit();