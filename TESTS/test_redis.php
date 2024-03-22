<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\RedisClient;

// Instantiate your Redis client class
$redisClient = new RedisClient();

// Test setting a value in Redis
$redisClient->set('test_key', 'Hello, Redis!', 300); // Expires in 300 seconds (5 minutes)

// Retrieve the value from Redis
$value = $redisClient->get('test_key');
echo "The value of 'test_key' is: " . $value . PHP_EOL;

// Check if the key exists in Redis
$exists = $redisClient->exists('test_key');
echo "'test_key' exists in Redis: " . ($exists ? 'YES' : 'NO') . PHP_EOL;

// Delete the key from Redis
$redisClient->del('test_key');

// Check again if the key exists after deletion
$existsAfterDelete = $redisClient->exists('test_key');
echo "'test_key' exists in Redis after deletion: " . ($existsAfterDelete ? 'YES' : 'NO') . PHP_EOL;
