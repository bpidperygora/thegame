<?php

namespace App;

use PDO;
use PDOException;
use Exception;

class Login
{
    private PDO $db;
    private RedisClient $redisClient;

    /**
     * Constructor for the Login class.
     *
     * @param PDO $db Database connection object.
     * @param RedisClient $redisClient Redis client object.
     */
    public function __construct(PDO $db, RedisClient $redisClient)
    {
        $this->db = $db;
        $this->redisClient = $redisClient;
    }

    /**
     * Authenticates a user by their email and password.
     *
     * @param string $email User's email address.
     * @param string $password User's password.
     * @return bool True if authentication is successful, false otherwise.
     * @throws Exception Throws an exception if there is a database error.
     */
    public function authenticate(string $email, string $password): bool
    {
        $sql = "SELECT id, password FROM players WHERE email = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Check if there's an active session for this user in Redis
                if ($this->redisClient->exists("user:session:{$user['id']}")) {
                    // User already has an active session
                    return false;
                }

                // Update session information in Redis
                $sessionId = session_create_id();
                $this->redisClient->set("user:session:{$user['id']}", $sessionId);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['session_id'] = $sessionId;
                return true;
            }

            return false;
        } catch (PDOException $e) {
            // Rethrow the exception with a custom message
            throw new Exception("Authentication error: " . $e->getMessage());
        }
    }
}