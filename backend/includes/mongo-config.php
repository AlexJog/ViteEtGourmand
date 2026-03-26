<?php
function getMongoCollection(string $collection): MongoDB\Collection
{
    $uri    = getenv('MONGODB_URI');
    $client = new MongoDB\Client($uri);
    return $client->selectDatabase('vite_gourmand')->selectCollection($collection);
}
?>