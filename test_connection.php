<?php
// test_connection.php (version MySQLi)
require_once __DIR__ . '/config/config.php';

echo "Test de connexion à MySQL avec MySQLi...<br><br>";

try {
    // Connexion à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("❌ Erreur de connexion à MySQL : " . $conn->connect_error);
    }

    // Définir le jeu de caractères
    $conn->set_charset("utf8mb4");

    echo "✅ Connexion à MySQL réussie !<br><br>";

    // Test : Exécuter une requête simple
    $sql = "SELECT COUNT(*) AS count FROM destinations";
    $result = $conn->query($sql);

    if ($result) {
        $row = $result->fetch_assoc();
        echo "Nombre de destinations dans la base : " . $row['count'] . "<br>";
    } else {
        echo "⚠️ Erreur lors de l'exécution de la requête : " . $conn->error;
    }

    // Fermer la connexion
    $conn->close();
} catch (Exception $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>