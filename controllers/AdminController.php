<?php
// Nomadix/controllers/AdminController.php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/Database.php';

class AdminController {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new AdminModel();
    }
    
    /**
     * Vérifie que l'utilisateur est admin
     */
    public static function checkAdminAccess() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['admin']) || !$_SESSION['user']['admin']) {
            header("HTTP/1.0 403 Forbidden");
            die("Accès refusé. Réservé aux administrateurs.");
        }
    }
    
    /**
     * Affiche le dashboard admin
     */
    public function showDashboard() {
        $stats = $this->adminModel->getDashboardStats();
        $recentReviews = $this->adminModel->getRecentReviews(5);
        $users = $this->adminModel->getAllUsers();
        
        require_once __DIR__ . '/../views/admin.php';
    }
    
    /**
     * Affiche la page de gestion des utilisateurs
     */
    public function manageUsers() {
        $users = $this->adminModel->getAllUsers();
        require_once __DIR__ . '/../views/admin-users.php';
    }
    
    /**
     * Affiche la page de gestion des avis
     */
    public function manageReviews() {
        $reviews = $this->adminModel->getAllReviews();
        require_once __DIR__ . '/../views/admin-reviews.php';
    }
    
    /**
     * Bascule le statut admin d'un utilisateur
     */
    public function toggleAdmin() {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Paramètres manquants");
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }
        
        $userId = (int)$_POST['userId'];
        $user = $this->adminModel->getUserById($userId);
        
        if (!$user) {
            http_response_code(404);
            die("Utilisateur non trouvé");
        }
        
        // Ne pas supprimer le propre compte admin
        if ($userId === $_SESSION['user']['id']) {
            http_response_code(403);
            die("Vous ne pouvez pas modifier votre propre statut admin");
        }
        
        $newAdminStatus = $user['admin'] ? 0 : 1;
        $this->adminModel->updateAdminStatus($userId, $newAdminStatus);
        
        header("Location: admin.php?page=users&success=1");
        exit;
    }
    
    /**
     * Supprime un utilisateur
     */
    public function deleteUser() {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Paramètres manquants");
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }
        
        $userId = (int)$_POST['userId'];
        
        // Ne pas supprimer le propre compte
        if ($userId === $_SESSION['user']['id']) {
            http_response_code(403);
            die("Vous ne pouvez pas supprimer votre propre compte");
        }
        
        $this->adminModel->deleteUser($userId);
        
        header("Location: admin.php?page=users&success=deleted");
        exit;
    }
    
    /**
     * Supprime un avis
     */
    public function deleteReview() {
        if (!isset($_POST['reviewId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Paramètres manquants");
        }
        
        // Vérifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }
        
        $reviewId = (int)$_POST['reviewId'];
        $this->adminModel->deleteReview($reviewId);
        
        header("Location: admin.php?page=reviews&success=deleted");
        exit;
    }
    
    /**
     * Gère tous les appels d'administration
     */
    public function handleAdmin() {
        $page = $_GET['page'] ?? 'dashboard';
        $action = $_GET['action'] ?? null;

        // Traiter les actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? null;
            
            if ($action === 'toggle_admin') {
                $this->toggleAdmin();
            } elseif ($action === 'delete_user') {
                $this->deleteUser();
            } elseif ($action === 'delete_review') {
                $this->deleteReview();
            }
        }

        // Afficher les pages
        switch ($page) {
            case 'users':
                $this->manageUsers();
                break;
            case 'reviews':
                $this->manageReviews();
                break;
            case 'dashboard':
            default:
                $this->showDashboard();
                break;
        }
    }

    /**
     * Génère un token CSRF
     */
    public function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie un token CSRF
     */
    private function verifyCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
