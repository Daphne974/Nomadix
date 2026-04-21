<?php
// Nomadix/controllers/AdminController.php
require_once __DIR__ . '/../models/Database.php';

class AdminController {
    public function showAdminPanel() {
        // Exemple : Récupérer tous les utilisateurs
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id, login, email, admin FROM utilisateurs");
        $stmt->execute();
        $users = $stmt->fetchAll();

        require_once __DIR__ . '/../views/admin.php';
    }

    // Nomadix/controllers/AdminController.php
public function toggleAdmin() {
    if (!isset($_GET['id'])) {
        header("Location: admin.php");
        exit;
    }

    $userId = (int)$_GET['id'];
    $conn = Database::getAdminConnection();

    // Vérifie si l'utilisateur existe
    $stmt = $conn->prepare("SELECT admin FROM utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Utilisateur non trouvé.");
    }

    // Bascule le rôle admin
    $newAdminStatus = $user['admin'] ? 0 : 1;
    $stmt = $conn->prepare("UPDATE utilisateurs SET admin = ? WHERE id = ?");
    $stmt->execute([$newAdminStatus, $userId]);

    header("Location: admin.php");
    exit;
}
}
?>