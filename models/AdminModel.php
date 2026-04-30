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
                   u.login, u.email,
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
                   u.login, d.nom as destinationNom
            FROM avis a
            LEFT JOIN utilisateurs u ON a.idUtilisateur = u.id
            LEFT JOIN destinations d ON a.idDestination = d.id
            ORDER BY a.dateAvis DESC
            LIMIT ?
        ");
        $stmt->execute([(int)$limit]);
        return $stmt->fetchAll();
    }
}
?>
