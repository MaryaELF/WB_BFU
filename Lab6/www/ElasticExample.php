<?php

namespace App;

use App\Helpers\ClientFactory;

class ElasticExample
{
    private $client;
    private $index = 'movies';

    public function __construct()
    {
        $this->client = ClientFactory::make('http://elasticsearch:9200/');
    }
    
    public function getClient()
    {
        return $this->client;
    }

    public function createIndex()
    {
        try {
            $response = $this->client->put($this->index, [
                'json' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'analysis' => [
                            'analyzer' => [
                                'russian_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'standard',
                                    'filter' => ['lowercase', 'russian_stemmer']
                                ]
                            ],
                            'filter' => [
                                'russian_stemmer' => [
                                    'type' => 'stemmer',
                                    'language' => 'russian'
                                ]
                            ]
                        ]
                    ],
                    'mappings' => [
                        'properties' => [
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'russian_analyzer',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword']
                                ]
                            ],
                            'year' => ['type' => 'integer'],
                            'genre' => [
                                'type' => 'text',
                                'analyzer' => 'russian_analyzer',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword']
                                ]
                            ],
                            'rating' => ['type' => 'float']
                        ]
                    ]
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function addMovie($id, $title, $year, $genre, $rating)
    {
        try {
            $response = $this->client->put("{$this->index}/_doc/{$id}", [
                'json' => [
                    'title' => $title,
                    'year' => $year,
                    'genre' => $genre,
                    'rating' => $rating
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function refreshIndex()
    {
        try {
            $response = $this->client->post("{$this->index}/_refresh");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function indexExists()
    {
        try {
            $response = $this->client->head($this->index);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function searchMovies($query, $field = 'title')
    {
        try {
            $searchBody = [
                'query' => [
                    'match' => [
                        $field => [
                            'query' => $query,
                            'fuzziness' => 'AUTO'
                        ]
                    ]
                ],
                'size' => 10
            ];
            
            $response = $this->client->get("{$this->index}/_search", [
                'json' => $searchBody
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function searchByYear($year)
    {
        try {
            $response = $this->client->get("{$this->index}/_search", [
                'json' => [
                    'query' => [
                        'term' => ['year' => $year]
                    ]
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function searchAllFields($query)
    {
        try {
            $response = $this->client->get("{$this->index}/_search", [
                'json' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title', 'genre'],
                            'fuzziness' => 'AUTO'
                        ]
                    ],
                    'size' => 10
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function deleteIndex()
    {
        try {
            $response = $this->client->delete($this->index);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getAllMovies()
    {
        try {
            $response = $this->client->get("{$this->index}/_search", [
                'json' => [
                    'query' => ['match_all' => new \stdClass()],
                    'size' => 20
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}