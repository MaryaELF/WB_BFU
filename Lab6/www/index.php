<?php

require 'vendor/autoload.php';

use App\ElasticExample;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск Фильмов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <p><strong>Этот сайт как-то называется, но я не придумала название</strong></p>
        </div>
        <div class="content">
<?php


try {
    $elastic = new ElasticExample();
    $result = $elastic->createIndex();
    $movies = [
        ['id' => 1, 'title' => 'Побег из Шоушенка', 'year' => 1994, 'genre' => 'драма', 'rating' => 9.3],
        ['id' => 2, 'title' => 'Крестный отец', 'year' => 1972, 'genre' => 'драма', 'rating' => 9.2],
        ['id' => 3, 'title' => 'Темный рыцарь', 'year' => 2008, 'genre' => 'боевик', 'rating' => 9.0],
        ['id' => 4, 'title' => 'Криминальное чтиво', 'year' => 1994, 'genre' => 'криминал', 'rating' => 8.9],
        ['id' => 5, 'title' => 'Властелин колец', 'year' => 2003, 'genre' => 'фэнтези', 'rating' => 9.0],
        ['id' => 6, 'title' => 'Бойцовский клуб', 'year' => 1999, 'genre' => 'драма', 'rating' => 8.8],
        ['id' => 7, 'title' => 'Молчание Ягнят', 'year' => 1991, 'genre' => 'детектив', 'rating' => 8.3],
    ];
    
    echo "<h3>Добавление фильмов:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #f0f0f0'><th>ID</th><th>Название</th><th>Год</th><th>Жанр</th><th>Рейтинг</th> </tr>";
    
    foreach ($movies as $movie) {
        $result = $elastic->addMovie($movie['id'], $movie['title'], $movie['year'], $movie['genre'], $movie['rating']);
        if (isset($result['result']) && ($result['result'] == 'created' || $result['result'] == 'updated')) {
            echo "<tr>";
            echo "<td>{$movie['id']}</td>";
            echo "<td>{$movie['title']}</td>";
            echo "<td>{$movie['year']}</td>";
            echo "<td>{$movie['genre']}</td>";
            echo "<td>{$movie['rating']}</td>";
            echo "</tr>";
        } else if (isset($result['error'])) {
            echo "<tr style='color:red'>";
            echo "<td colspan='5'>Ошибка: " . $result['error'] . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    
    $elastic->refreshIndex();
    sleep(1); 
    echo "<hr>";
    
    $allMovies = $elastic->getAllMovies();
    if (isset($allMovies['hits']['hits'])) {
        $total = $allMovies['hits']['total']['value'];
        echo "<p>Всего документов: <strong>{$total}</strong></p>";
    }
    
    echo "<h4>1. Поиск по слову 'рыцарь':</h4>";
    $results = $elastic->searchMovies('рыцарь');
    if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
        echo "<ul>";
        foreach ($results['hits']['hits'] as $hit) {
            $movie = $hit['_source'];
            $score = round($hit['_score'], 2);
            echo "<li><strong>{$movie['title']}</strong> ({$movie['year']}) - {$movie['genre']}, рейтинг: {$movie['rating']} <span style='color:gray'>(релевантность: {$score})</span></li>";
        }
        echo "</ul>";
        echo "<p>Найдено: " . $results['hits']['total']['value'] . " фильмов</p>";
    } else {
        echo "<p>Фильмы с 'рыцарь' не найдены</p>";
    }
    
    echo "<h4>2. Поиск по слову 'Темный':</h4>";
    $results = $elastic->searchMovies('Темный');
    if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
        echo "<ul>";
        foreach ($results['hits']['hits'] as $hit) {
            $movie = $hit['_source'];
            echo "<li><strong>{$movie['title']}</strong> ({$movie['year']}) - {$movie['genre']}, рейтинг: {$movie['rating']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Ничего не найдено</p>";
    }
    
    echo "<h4>3. Поиск фильмов 1994 года:</h4>";
    $results = $elastic->searchByYear(1994);
    if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
        echo "<ul>";
        foreach ($results['hits']['hits'] as $hit) {
            $movie = $hit['_source'];
            echo "<li><strong>{$movie['title']}</strong> ({$movie['year']}) - {$movie['genre']}, рейтинг: {$movie['rating']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Фильмы 1994 года не найдены</p>";
    }
    
    echo "<h4>4. Поиск драматических фильмов:</h4>";
    $results = $elastic->searchMovies('драма', 'genre');
    if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
        echo "<ul>";
        foreach ($results['hits']['hits'] as $hit) {
            $movie = $hit['_source'];
            echo "<li><strong>{$movie['title']}</strong> ({$movie['year']}) - рейтинг: {$movie['rating']}</li>";
        }
        echo "</ul>";
        echo "<p>Найдено драм: " . $results['hits']['total']['value'] . "</p>";
    } else {
        echo "<p>Драмы не найдены</p>";
    }
    
    $allMovies = $elastic->getAllMovies();
    if (isset($allMovies['hits']['hits']) && !isset($allMovies['error'])) {
        $total = $allMovies['hits']['total']['value'];
        echo "<hr>";
        echo "<h3>Статистика:</h3>";
        echo "<p>Всего фильмов: <strong>{$total}</strong></p>";
        
        $genres = [];
        foreach ($allMovies['hits']['hits'] as $hit) {
            $genre = $hit['_source']['genre'];
            if (!isset($genres[$genre])) {
                $genres[$genre] = 0;
            }
            $genres[$genre]++;
        }
        
        if (count($genres) > 0) {
            echo "<p>Фильмы по жанрам:</p>";
            echo "<ul>";
            foreach ($genres as $genre => $count) {
                echo "<li>{$genre}: {$count} фильмов</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:orange'>Не удалось получить статистику</p>";
    }
    echo "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red; padding: 10px; background-color: #f8d7da; border-radius: 5px;'>";
    echo "Elasticsearch ошибка: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><small>Elasticsearch версия: 8.10.2</p>";