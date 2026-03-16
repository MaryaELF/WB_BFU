<?php

$host = 'db';
$db   = 'lab5_db';
$user = 'lab5_user';
$pass = 'lab5_pass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Ошибка подключения к BD: " . $e->getMessage());
    echo "Ошибка подключения к базе данных.";
    exit();
}