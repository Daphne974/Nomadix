<?php
// Nomadix/models/Database.php
class Database {
    private static $readConnection = null;
    private static $writeConnection = null;

    // Connexion en lecture seule (pour l'affichage public)
    public static function getClientConnection() {
        if (self::$readConnection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST_CLIENT . ";dbname=" . DB_NAME_CLIENT . ";charset=utf8mb4";
                self::$readConnection = new PDO($dsn, DB_USER_CLIENT, DB_PASS_CLIENT, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("❌ Erreur de connexion en lecture : " . $e->getMessage());
            }
        }
        return self::$readConnection;
    }

    // Connexion administrateur (pour les modifications)
    public static function getAdminConnection() {
        if (self::$writeConnection === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST_ADMIN . ";dbname=" . DB_NAME_ADMIN . ";charset=utf8mb4";
                self::$writeConnection = new PDO($dsn, DB_USER_ADMIN, DB_PASS_ADMIN, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("❌ Erreur de connexion en écriture : " . $e->getMessage());
            }
        }
        return self::$writeConnection;
    }
}
?>