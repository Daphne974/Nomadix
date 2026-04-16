<?php
// models/Database.php
class Database {
    private $connection;

    public function __construct() {
        try {
            // Options de connexion dans le DSN pour Azure SQL
            $dsn = "sqlsrv:server=tcp:nomadix.database.windows.net,1433;Database=Nomadix;Encrypt=yes;TrustServerCertificate=no";

            // Options PDO générales
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("❌ Erreur de connexion à Azure SQL : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}