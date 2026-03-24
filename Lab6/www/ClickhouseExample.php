<?php

namespace App;

use App\Helpers\ClientFactory;

class ClickhouseExample
{
    private $client;

    public function __construct()
    {
        $this->client = ClientFactory::make('http://clickhouse:8123/');
    }

    public function query($sql)
    {
        try {
            $response = $this->client->post('', [
                'body' => $sql,
                'headers' => [
                    'Content-Type' => 'text/plain; charset=utf-8'
                ]
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    public function queryJson($sql)
    {
        try {
            $response = $this->client->post('', [
                'body' => $sql,
                'headers' => [
                    'Content-Type' => 'text/plain; charset=utf-8'
                ],
                'query' => [
                    'default_format' => 'JSONEachRow'
                ]
            ]);
            $result = $response->getBody()->getContents();
            
            $rows = [];
            $lines = explode("\n", trim($result));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $rows[] = json_decode($line, true);
                }
            }
            return $rows;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function execute($sql)
    {
        return $this->query($sql);
    }
}