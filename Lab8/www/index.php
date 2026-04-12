<?php

require_once 'Database.php';
require_once 'HacakthonRegistration.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    $database = new Database();
    $db = $database->getConnection();
    $registration = new HacakthonRegistration($db);
    
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $result = $registration->register($input);
        echo json_encode($result);
    } 
    elseif ($method === 'GET' && isset($_GET['id'])) {
        $participant = $registration->getParticipantById($_GET['id']);
        echo json_encode($participant);
    }
    elseif ($method === 'GET') {
        $participants = $registration->getAllParticipants();
        echo json_encode($participants);
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}