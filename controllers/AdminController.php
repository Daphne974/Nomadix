<?php
// Nomadix/controllers/AdminController.php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../models/Database.php';

class AdminController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    /**
     * Verifie que l'utilisateur est admin
     */
    public static function checkAdminAccess()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['admin']) || !$_SESSION['user']['admin']) {
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
    }

    /**
     * Affiche le dashboard admin
     */
    public function showDashboard()
    {
        $stats = $this->adminModel->getDashboardStats();
        $recentReviews = $this->adminModel->getRecentReviews(5);
        $users = $this->adminModel->getAllUsers();

        require_once __DIR__ . '/../views/admin.php';
    }

    /**
     * Affiche la page de gestion des utilisateurs
     */
    public function manageUsers()
    {
        $users = $this->adminModel->getAllUsers();
        require_once __DIR__ . '/../views/admin-users.php';
    }

    /**
     * Affiche la page de gestion des avis
     */
    public function manageReviews()
    {
        $reviews = $this->adminModel->getAllReviews();
        require_once __DIR__ . '/../views/admin-reviews.php';
    }

    /**
     * Bascule le statut admin d'un utilisateur
     */
    public function toggleAdmin()
    {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Parametres manquants");
        }

        // Verifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $userId = (int) $_POST['userId'];
        $user = $this->adminModel->getUserById($userId);

        if (!$user) {
            http_response_code(404);
            die("Utilisateur non trouve");
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
    public function deleteUser()
    {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Parametres manquants");
        }

        // Verifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $userId = (int) $_POST['userId'];

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
    public function deleteReview()
    {
        if (!isset($_POST['reviewId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Parametres manquants");
        }

        // Verifier le token CSRF
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $reviewId = (int) $_POST['reviewId'];
        $this->adminModel->deleteReview($reviewId);

        header("Location: admin.php?page=reviews&success=deleted");
        exit;
    }

    /**
     * Gere tous les appels d'administration
     */
    public function handleAdmin()
    {
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
     * Genere un token CSRF
     */
    public function generateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Verifie un token CSRF
     */
    private function verifyCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Nomadix/controllers/AdminController.php
    public function showAdminPanel()
    {
        $conn = Database::getAdminConnection();

        // Récupère les statistiques
        $stats = $this->getStats($conn);

        // Récupère les avis récents (non vérifiés par défaut)
        $recentReviews = $this->getRecentReviews($conn, false); // false = non vérifiés seulement

        // Récupère les utilisateurs
        $users = $this->getUsers($conn);

        require_once __DIR__ . '/../views/admin.php';
    }

    public function getRecentReviews($conn, $showAll = false)
    {
        $query = "SELECT avis.*, utilisateurs.login, destinations.nom as destinationNom
              FROM avis
              INNER JOIN utilisateurs ON avis.idUtilisateur = utilisateurs.id
              INNER JOIN destinations ON avis.idDestination = destinations.id";

        if (!$showAll) {
            $query .= " WHERE avis.verified = 0"; // Filtre pour les avis non vérifiés
        }

        $query .= " ORDER BY avis.dateAvis DESC LIMIT 5";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllReviews($conn)
    {
        return $this->getRecentReviews($conn, true); // true = tous les avis
    }

    public function toggleReviewVerification()
    {
        if (!isset($_GET['id'])) {
            header("Location: admin.php");
            exit;
        }

        $reviewId = (int) $_GET['id'];
        $conn = Database::getAdminConnection();

        // Bascule l'état de vérification
        $stmt = $conn->prepare("SELECT verified FROM avis WHERE id = ?");
        $stmt->execute([$reviewId]);
        $review = $stmt->fetch();

        if (!$review) {
            die("Avis non trouvé.");
        }

        $newVerifiedStatus = $review['verified'] ? 0 : 1;
        $stmt = $conn->prepare("UPDATE avis SET verified = ? WHERE id = ?");
        $stmt->execute([$newVerifiedStatus, $reviewId]);

        header("Location: admin.php?success=1");
        exit;
    }

    public function getStats($conn)
    {
        // Récupère les statistiques (utilisateurs, avis, etc.)
        $stats = [];

        // Total utilisateurs
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM utilisateurs");
        $stmt->execute();
        $stats['totalUsers'] = $stmt->fetch()['count'];

        // Total admins
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE admin = 1");
        $stmt->execute();
        $stats['totalAdmins'] = $stmt->fetch()['count'];

        // Total avis
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM avis");
        $stmt->execute();
        $stats['totalReviews'] = $stmt->fetch()['count'];

        // Total destinations
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM destinations");
        $stmt->execute();
        $stats['totalDestinations'] = $stmt->fetch()['count'];

        // Note moyenne
        $stmt = $conn->prepare("SELECT AVG(note) as avg FROM avis");
        $stmt->execute();
        $stats['averageRating'] = number_format($stmt->fetch()['avg'], 1);

        // Nouveaux utilisateurs ce mois
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM utilisateurs WHERE dateCreation >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $stmt->execute();
        $stats['usersThisMonth'] = $stmt->fetch()['count'];

        return $stats;
    }

    public function getUsers($conn)
    {
        $stmt = $conn->prepare("SELECT id, login, email, admin FROM utilisateurs ORDER BY dateCreation DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>