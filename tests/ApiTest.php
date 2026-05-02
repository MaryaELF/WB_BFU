<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class ApiTest extends TestCase
{
    public function testSimpleMockRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['status' => 'ok']))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false
        ]);
        
        $response = $client->get('/test');
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertEquals('ok', $data['status']);
    }
    
    public function testPostRequestMock(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'success' => true,
                'id' => 999
            ]))
        ]);
        
        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false
        ]);
        
        $response = $client->post('/api/register', [
            'json' => ['name' => 'Test', 'age' => 25]
        ]);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
    }
}