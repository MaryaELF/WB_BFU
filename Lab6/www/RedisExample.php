<?php

namespace App;

class RedisExample
{
    private $client;

    public function __construct()
    {
        if (!class_exists('\Predis\Client')) {
            throw new \Exception('Predis not installed. Run: composer require predis/predis');
        }
        
        $this->client = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'redis',
            'port'   => 6379,
            'timeout' => 5.0,
        ]);
        
        try {
            $this->client->ping();
        } catch (\Exception $e) {
            throw new \Exception('Redis connection failed: ' . $e->getMessage());
        }
    }

    public function setValue($key, $value)
    {
        return $this->client->set($key, $value);
    }

    public function getValue($key)
    {
        return $this->client->get($key);
    }
    
    public function deleteValue($key)
    {
        return $this->client->del($key);
    }
}