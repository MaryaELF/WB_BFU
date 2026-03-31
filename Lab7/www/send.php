<?php

require 'vendor/autoload.php';
require 'QueueManager.php';
require 'Student.php';

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($name) || empty($email)) {
    http_response_code(400);
    echo "Name and email are required";
    exit;
}

$student = new Student();
$studentId = $student->create($name, $email);

if (!$studentId) {
    http_response_code(500);
    echo "Failed to save student to database";
    exit;
}

$queueManager = new QueueManager();
$result = $queueManager->publish([
    'id' => $studentId,
    'name' => $name,
    'email' => $email,
    'timestamp' => date('Y-m-d H:i:s')
]);

if ($result) {
    echo "Message sent to Kafka queue successfully";
} else {
    http_response_code(500);
    echo "Failed to send message to Kafka";
}