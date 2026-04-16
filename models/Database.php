<?php
// models/Database.php
class Database {
    private $connection;

    public function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
        mysqli_set_charset($this->connection, "utf8mb4");
    }

    public function getConnection() {
        return $this->connection;
    }
}