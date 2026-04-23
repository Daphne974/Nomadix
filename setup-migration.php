<?php
// Nomadix/setup-migration.php
require_once __DIR__ . '/models/Database.php';

try {
    $conn = Database::getAdminConnection();
    
    // Vérifier si la colonne admin existe
    $stmt = $conn->query("DESC utilisateurs");
    $columns = $stmt->fetchAll();
    $hasAdminColumn = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'admin') {
            $hasAdminColumn = true;
            break;
        }
    }
    
    if (!$hasAdminColumn) {
        echo "Ajout de la colonne admin...\n";
        $conn->exec("ALTER TABLE utilisateurs ADD COLUMN admin TINYINT DEFAULT 0 AFTER email");
        echo "✓ Colonne admin ajoutée\n";
    } else {
        echo "✓ Colonne admin existe déjà\n";
    }
    
    // Rendre admin le premier utilisateur (id=1)
    $stmt = $conn->prepare("UPDATE utilisateurs SET admin = 1 WHERE id = 1 LIMIT 1");
    $stmt->execute();
    echo "✓ Utilisateur 1 défini comme admin\n";
    
    echo "\n✓ Migration complète!\n";
} catch (Exception $e) {
    die("❌ Erreur: " . $e->getMessage());
}
?>
