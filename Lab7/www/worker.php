#!/usr/bin/env php
<?php

require 'vendor/autoload.php';
require 'QueueManager.php';
require 'Student.php';

echo "Worker started (Kafka consumer)\n";
echo "Listening for messages on topic: lab7_topic\n";
echo "Press Ctrl+C to stop\n\n";

$queueManager = new QueueManager();
$student = new Student();

$queueManager->consume(function($data) use ($student) {
    echo sprintf(
        "[%s] Received message: id=%s, name=%s, email=%s\n",
        date('Y-m-d H:i:s'),
        $data['id'] ?? 'N/A',
        $data['name'] ?? 'N/A',
        $data['email'] ?? 'N/A'
    );
    
    sleep(2);
    
    if (isset($data['id'])) {
        $result = $student->markAsProcessed($data['id']);
        
        if ($result) {
            echo sprintf(
                "[%s] Processed successfully: student %s\n",
                date('Y-m-d H:i:s'),
                $data['name'] ?? 'Unknown'
            );
            
            $logEntry = sprintf(
                "%s|%s|%s|%s|SUCCESS\n",
                date('Y-m-d H:i:s'),
                $data['id'],
                $data['name'],
                $data['email']
            );
            file_put_contents('processed_kafka.log', $logEntry, FILE_APPEND);
        } else {
            echo sprintf(
                "[%s] Failed to process: student %s\n",
                date('Y-m-d H:i:s'),
                $data['name'] ?? 'Unknown'
            );
        }
    } else {
        echo sprintf(
            "[%s] Invalid message format: missing student ID\n",
            date('Y-m-d H:i:s')
        );
    }
    
    echo "---\n";
});