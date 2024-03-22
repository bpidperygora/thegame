<?php

namespace App;

use PDO;
use PDOException;

class Registration
{
    private PDO $db;

    /**
     * Constructor for the Registration class.
     *
     * @param PDO $db Database connection object.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Registers a new user in the database.
     *
     * @param string $username The username of the new user.
     * @param string $email The email of the new user.
     * @param string $password The password of the new user.
     * @return bool True if registration is successful, false otherwise.
     */
    public function registerUser(string $username, string $email, string $password): bool
    {
        if ($this->emailExists($email)) {
            return false; // Email already in use.
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO players (username, email, password) VALUES (?, ?, ?)";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$username, $email, $hashedPassword]);
        } catch (PDOException $e) {
            // Instead of a generic error, log the exception or handle it as per your error handling strategy
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if an email already exists in the database.
     *
     * @param string $email The email to check.
     * @return bool True if the email exists, false otherwise.
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM players WHERE email = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Instead of a generic error, log the exception or handle it as per your error handling strategy
            error_log("Email check error: " . $e->getMessage());
            return true;
        }
    }
}