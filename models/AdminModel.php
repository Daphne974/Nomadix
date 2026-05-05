<?php
// Nomadix/models/AdminModel.php
require_once __DIR__ . '/Database.php';

class AdminModel {
    
    /**
     * Récupère tous les utilisateurs
     */
    public function getAllUsers() {
        $conn = Database::getAdminConnection();
        $stmt = $conn->query("SELECT id, login, email, admin, dateCreation FROM utilisateurs ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Récupère un utilisateur par ID
     */
    public function getUserById($id) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id, login, email, admin, dateCreation FROM utilisateurs WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }
    
    /**
     * Met à jour le statut admin d'un utilisateur
     */
    public function updateAdminStatus($userId, $isAdmin) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE utilisateurs SET admin = ? WHERE id = ?");
        return $stmt->execute([(int)$isAdmin, (int)$userId]);
    }
    
    /**
     * Supprime un utilisateur et ses avis
     */
    public function deleteUser($userId) {
        $conn = Database::getAdminConnection();
        try {
            $conn->beginTransaction();
            
            // Supprimer les avis de l'utilisateur
            $stmt = $conn->prepare("DELETE FROM avis WHERE idUtilisateur = ?");
            $stmt->execute([(int)$userId]);
            
            // Supprimer l'utilisateur
            $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([(int)$userId]);
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    
    /**
     * Récupère tous les avis avec les infos utilisateur et destination
     */
    public function getAllReviews() {
        $conn = Database::getClientConnection();
        $stmt = $conn->query("
            SELECT a.id, a.note, a.commentaire, a.dateAvis, a.verified,
                   u.login, u.email, u.avatar,
                   d.nom as destinationNom, d.image
            FROM avis a
            LEFT JOIN utilisateurs u ON a.idUtilisateur = u.id
            LEFT JOIN destinations d ON a.idDestination = d.id
            ORDER BY a.dateAvis DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Supprime un avis
     */
    public function deleteReview($reviewId) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("DELETE FROM avis WHERE id = ?");
        return $stmt->execute([(int)$reviewId]);
    }
    
    /**
     * Récupère les statistiques du dashboard
     */
    public function getDashboardStats() {
        $conn = Database::getClientConnection();
        
        $stats = [];
        
        // Nombre total d'utilisateurs
        $stmt = $conn->query("SELECT COUNT(*) as total FROM utilisateurs");
        $stats['totalUsers'] = $stmt->fetch()['total'];
        
        // Nombre d'administrateurs
        $stmt = $conn->query("SELECT COUNT(*) as total FROM utilisateurs WHERE admin = 1");
        $stats['totalAdmins'] = $stmt->fetch()['total'];
        
        // Nombre total d'avis
        $stmt = $conn->query("SELECT COUNT(*) as total FROM avis");
        $stats['totalReviews'] = $stmt->fetch()['total'];
        
        // Nombre total de destinations
        $stmt = $conn->query("SELECT COUNT(*) as total FROM destinations");
        $stats['totalDestinations'] = $stmt->fetch()['total'];
        
        // Note moyenne
        $stmt = $conn->query("SELECT AVG(note) as average FROM avis");
        $avg = $stmt->fetch()['average'];
        $stats['averageRating'] = round($avg ?? 0, 2);
        
        // Utilisateurs créés ce mois
        $stmt = $conn->query("
            SELECT COUNT(*) as total FROM utilisateurs 
            WHERE MONTH(dateCreation) = MONTH(NOW()) 
            AND YEAR(dateCreation) = YEAR(NOW())
        ");
        $stats['usersThisMonth'] = $stmt->fetch()['total'];
        
        return $stats;
    }
    
    /**
     * Récupère les avis récents (derniers 5)
     */
    public function getRecentReviews($limit = 5) {
        $conn = Database::getClientConnection();
        $stmt = $conn->prepare("
            SELECT a.id, a.note, a.commentaire, a.dateAvis,
                   u.login, u.avatar, d.nom as destinationNom
            FROM avis a
            LEFT JOIN utilisateurs u ON a.idUtilisateur = u.id
            LEFT JOIN destinations d ON a.idDestination = d.id
            ORDER BY a.dateAvis DESC
            LIMIT ?
        ");
        $stmt->execute([(int)$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère toutes les destinations
     */
    public function getAllDestinations() {
        $conn = Database::getAdminConnection();
        $stmt = $conn->query("SELECT id, nom, pays, ville, image FROM destinations ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /**
     * Supprime une destination et ses avis associés
     */
    public function deleteDestination($destinationId) {
        $conn = Database::getAdminConnection();
        try {
            $conn->beginTransaction();

            // Supprimer les avis liés
            $stmt = $conn->prepare("DELETE FROM avis WHERE idDestination = ?");
            $stmt->execute([(int)$destinationId]);

            // Supprimer la destination
            $stmt = $conn->prepare("DELETE FROM destinations WHERE id = ?");
            $stmt->execute([(int)$destinationId]);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    /**
     * Récupère une destination par ID
     */
    public function getDestinationById($id) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id, nom, description, pays, ville, image FROM destinations WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    /**
     * Crée une nouvelle destination
     */
    public function createDestination($data) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("INSERT INTO destinations (nom, description, pays, ville, image) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['nom'] ?? null,
            $data['description'] ?? null,
            $data['pays'] ?? null,
            $data['ville'] ?? null,
            $data['image'] ?? null
        ]);
    }

    /**
     * Met à jour une destination
     */
    public function updateDestination($id, $data) {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE destinations SET nom = ?, description = ?, pays = ?, ville = ?, image = ? WHERE id = ?");
        return $stmt->execute([
            $data['nom'] ?? null,
            $data['description'] ?? null,
            $data['pays'] ?? null,
            $data['ville'] ?? null,
            $data['image'] ?? null,
            (int)$id
        ]);
    }
}
?>
