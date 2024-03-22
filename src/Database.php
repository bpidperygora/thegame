<?php

namespace App;

use PDO;
use PDOException;

/**
 * Database connection class using PDO.
 */
class Database
{
    private string $host = 'localhost';
    private string $dbName = 'mmorpg_db';
    private string $user = 'root';
    private string $pass = '';
    private ?PDO $conn = null;

    /**
     * Establishes a database connection.
     * 
     * @return PDO|null
     */
    public function connect(): ?PDO
    {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
            } catch (PDOException $e) {
                echo "Connection error: " . $e->getMessage();
                $this->conn = null;
            }
        }
        return $this->conn;
    }
}
