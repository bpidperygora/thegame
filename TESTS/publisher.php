<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\RedisClient;

// Instantiate your Redis client class
$redisClient = new RedisClient();

// Define the channel and message
$channel = 'test_channel';
$message = 'Hello, World!';

// Publish a message to the channel
$redisClient->publish($channel, $message);

echo "Published message '{$message}' to channel '{$channel}'." . PHP_EOL;
