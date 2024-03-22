<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Registration;

$message = ''; // Initialize a message variable.

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database())->connect();
    $registration = new Registration($db);

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($registration->registerUser($username, $email, $password)) {
        // Registration successful, redirect or show a success message.
        header('Location: /index.php?message=Registration successful. Please log in.');
        exit;
    } else {
        // Check if the email already exists.
        if ($registration->emailExists($email)) {
            // Email exists, redirect to login page.
            header('Location: /index.php?message=This Email is in Use. Please log in.');
            exit;
        } else {
            // Other registration failure.
            $message = "Registration failed. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body>
    <?php if ($message) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Register</button>
    </form>
</body>

</html>