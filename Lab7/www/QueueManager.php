<?php

use Kafka\Producer;
use Kafka\ProducerConfig;
use Kafka\Consumer;
use Kafka\ConsumerConfig;

class QueueManager {
    private $topic = 'lab7_topic';
    private $brokerList = 'kafka:9092';

    public function publish($data) {
        try {
            $config = ProducerConfig::getInstance();
            $config->setMetadataBrokerList($this->brokerList);
            $config->setRequiredAck(1);
            $config->setIsAsyn(false);
            $config->setProduceInterval(500);
            
            $producer = new Producer(function() use ($data) {
                return [
                    [
                        'topic' => $this->topic,
                        'value' => json_encode($data, JSON_UNESCAPED_UNICODE),
                        'key' => md5(uniqid())
                    ]
                ];
            });
            
            $producer->send();
            return true;
        } catch (\Exception $e) {
            error_log("Kafka publish error: " . $e->getMessage());
            return false;
        }
    }

    public function consume(callable $callback) {
        try {
            $config = ConsumerConfig::getInstance();
            $config->setMetadataBrokerList($this->brokerList);
            $config->setGroupId('lab7_consumer_group');
            $config->setTopics([$this->topic]);
            $config->setOffsetReset('earliest');
            
            $consumer = new Consumer();
            $consumer->start(function($topic, $partition, $message) use ($callback) {
                $data = json_decode($message['message']['value'], true);
                if ($data && is_callable($callback)) {
                    $callback($data);
                }
            });
            
        } catch (\Exception $e) {
            error_log("Kafka consume error: " . $e->getMessage());
            throw $e;
        }
    }
}