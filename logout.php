<?php

namespace App;

session_start();

require './vendor/autoload.php';

// Instantiate Redis client
$redisClient = new RedisClient();

$userId = $_SESSION['user_id'] ?? null;
var_dump($_SESSION['user_id']);
if ($userId) {
    // Retrieve the random ID associated with the user's session from Redis
    $randomId = $redisClient->get("user:{$userId}");
    var_dump($randomId);
    if ($randomId) {
        // Publish a logout message to a Redis channel that the WebSocket server is subscribed to
        $redisClient->publish('logout_channel', json_encode(['id' => $randomId]));

        // Clean up Redis
        $redisClient->del("user:{$randomId}"); // Delete the random ID key
    }

    // Additionally, delete the session and is_active keys for the user
    $redisClient->del("user:session:{$userId}");
    $redisClient->del("user:is_active:{$userId}");
}

// // Destroy the session
session_destroy();

// // Redirect to the login page
header('Location: /');
exit;
