<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\Login;
use App\RedisClient; 

$message = '';

// Instantiate RedisClient
$redisClient = new RedisClient();

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}

// Check if the user is already logged in by checking the session.
if (isset($_SESSION['user_id']) && $redisClient->exists("user:session:{$_SESSION['user_id']}")) {
    // User is already logged in, show a message instead of the login form.
    $message = "You are already logged in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->connect();
    $login = new Login($db, $redisClient);

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($login->authenticate($email, $password)) {
        // Redirect to welcome page upon successful login.
        header('Location: welcome/index.php');
        exit;
    } else {
        // Show an error message if authentication fails.
        $message = "Invalid login credentials or you are already logged in from another location.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if (!isset($_SESSION['user_id'])): // Show form only if not logged in ?>
        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="registration/index.php">Register here</a>.</p>
    <?php else: ?>
        <!-- Optionally, provide a logout or redirect option for already logged-in users -->
        <p><a href="logout.php">Logout</a></p>
    <?php endif; ?>
</body>
</html>
