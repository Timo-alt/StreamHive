<?php

class Database {
    private string $host = "localhost";
    private string $username = "root";
    private string $password = "";
    private string $dbname = "streamhive";
    private ?PDO $conn = null;

    public function connect(): PDO {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Zorg ervoor dat PDO direct foutmeldingen gooit bij fouten
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $this->conn;
        } catch (PDOException $e) {
            // Geeft error als de verbinding verkeert gaat
            die("Database verbinding mislukt: " . $e->getMessage());
        }
    }
}