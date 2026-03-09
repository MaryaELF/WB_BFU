<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

class ApiClient {
    private Client $client;

    public function __construct() {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'PHP-App/1.0', 
                'Accept' => 'application/vnd.github.v3+json'
            ]
        ]);
    }

    public function request(string $url): array {
        try {
            $response = $this->client->get($url, ['timeout' => 10]);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            
            if (isset($data['message']) && $response->getStatusCode() >= 400) {
                return ['error' => $data['message']];
            }
            
            return $data ?? ['error' => 'Empty response'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}