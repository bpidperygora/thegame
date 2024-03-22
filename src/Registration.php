<?php

namespace App;

use PDO;
use PDOException;

class Registration
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

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
            // Handle error.
            return false;
        }
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM players WHERE email = ?";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Handle error.
            return true;
        }
    }
}
