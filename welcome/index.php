<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\RedisClient;

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

$redisClient = new RedisClient();
$userId = $_SESSION['user_id'];
// Assuming the var_dump is for debugging purposes, it should be removed in production code.
// var_dump($userId);

// Check if the is_active flag is set for the current user
$isActive = $redisClient->get("user:is_active:$userId");

if ($isActive) {
    echo "You are already logged in from another location.";
    // Provide a button to force logout from the other session
    echo '<p><a href="logout.php">Logout</a></p>';
    exit;
} else {
    // Generate a random ID
    $randomId = bin2hex(random_bytes(16)); // 32 characters long

    // Set the is_active flag for the user
    $redisClient->set("user:is_active:$userId", 'true');

    // Associate this random ID with user_id in Redis
    $redisClient->set("user:session:$userId", $randomId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to the Game</title>
    <script>
        // Define the random ID as a global constant in JavaScript
        const TAB_ID = "<?= $randomId ?>";
    </script>
</head>

<body>
    <h1>Welcome to the Game</h1>
    <!-- Your game content goes here -->
    <script src="/script.js"></script>
</body>

</html>
