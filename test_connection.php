<?php
// test_connection.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "✅ Connexion à Azure SQL réussie !";
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}