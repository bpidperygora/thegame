<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\RedisClient;

// Instantiate your Redis client class
$redisClient = new RedisClient();

// Define the channel
$channel = 'test_channel';

// Subscribe to the channel and listen for messages
echo "Subscribing to '{$channel}'. Waiting for messages...\n";
$redisClient->subscribe([$channel], function($message) {
    echo "Received message: {$message}\n";
    // Optionally, add logic here to break the loop and exit after receiving a certain message
});
