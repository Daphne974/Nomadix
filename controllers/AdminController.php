<?php
// Nomadix/controllers/AdminController.php
require_once __DIR__ . '/../config/config.php';
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
            // Afficher la page 403 (avec header/footer) pour une meilleure UX
            http_response_code(403);
            require_once __DIR__ . '/../403.php';
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

        header("Location: " . siteUrl('/admin') . "?page=users&success=1");
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

        header("Location: " . siteUrl('/admin') . "?page=users&success=deleted");
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

        header("Location: " . siteUrl('/admin') . "?page=reviews&success=deleted");
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
            } elseif ($action === 'delete_destination') {
                $this->deleteDestination();
            } elseif ($action === 'create_destination') {
                $this->createDestination();
            } elseif ($action === 'update_destination') {
                $this->updateDestination();
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
            case 'destinations':
                $this->manageDestinations();
                break;
            case 'dashboard':
            default:
                $this->showDashboard();
                break;
        }
    }

    /**
     * Affiche la page de gestion des destinations
     */
    public function manageDestinations()
    {
        $destinations = $this->adminModel->getAllDestinations();
        $page = 'destinations';
        $csrfToken = $this->generateCsrfToken();
        $success = $_GET['success'] ?? null;
        // If editing, load the destination
        $editDestination = null;
        if (isset($_GET['edit'])) {
            $editId = (int) $_GET['edit'];
            $editDestination = $this->adminModel->getDestinationById($editId);
        }
        require_once __DIR__ . '/../views/admin-destinations.php';
    }

    /**
     * Crée une nouvelle destination (POST)
     */
    public function createDestination()
    {
        if (!isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Parametres manquants");
        }
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $imageUrl = trim($_POST['image_url'] ?? '');
        if ($imageUrl === '' || empty($_FILES['image_file']['name'])) {
            header("Location: " . siteUrl('/admin') . "?page=destinations&error=photos_required");
            exit;
        }
        if (!$this->isValidImageUrl($imageUrl)) {
            header("Location: " . siteUrl('/admin') . "?page=destinations&error=image_url_invalid");
            exit;
        }

        $nom = $_POST['nom'] ?? '';
        $ville = $_POST['ville'] ?? '';
        $pays = $_POST['pays'] ?? '';
        $description = $_POST['description'] ?? '';

        // Vérifier les doublons
        $duplicateCheck = $this->adminModel->checkDestinationExists($nom, $ville, $pays);
        if ($duplicateCheck['exists']) {
            if ($duplicateCheck['reason'] === 'ville') {
                header("Location: " . siteUrl('/admin') . "?page=destinations&error=duplicate_ville");
            } else {
                header("Location: " . siteUrl('/admin') . "?page=destinations&error=duplicate_destination");
            }
            exit;
        }

        $data = [
            'nom' => $nom,
            'description' => $description,
            'pays' => $pays,
            'ville' => $ville,
            'image' => $imageUrl
        ];

        if (!$this->isUploadedJpeg($_FILES['image_file'])) {
            header("Location: " . siteUrl('/admin') . "?page=destinations&error=image_not_jpg");
            exit;
        }

        $ville = $data['ville'] ?? '';
        $uploadedLocal = $this->handleImageUpload($_FILES['image_file'], $ville);
        if ($uploadedLocal === null) {
            header("Location: " . siteUrl('/admin') . "?page=destinations&error=upload_failed");
            exit;
        }

        // Keep the image URL as the main image (local image is stored on disk, not in DB)
        $this->adminModel->createDestination($data);
        header("Location: " . siteUrl('/admin') . "?page=destinations&success=1");
        exit;
    }

    /**
     * Met à jour une destination (POST)
     */
    public function updateDestination()
    {
        if (!isset($_POST['csrf_token']) || !isset($_POST['destinationId'])) {
            http_response_code(400);
            die("Parametres manquants");
        }
        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $id = (int) $_POST['destinationId'];
        $imageUrl = trim($_POST['image_url'] ?? '');
        if ($imageUrl !== '' && !$this->isValidImageUrl($imageUrl)) {
            header("Location: " . siteUrl('/admin') . "?page=destinations&edit=" . $id . "&error=image_url_invalid");
            exit;
        }

        $existing = $this->adminModel->getDestinationById($id);

        $data = [
            'nom' => $_POST['nom'] ?? '',
            'description' => $_POST['description'] ?? '',
            'pays' => $_POST['pays'] ?? '',
            'ville' => $_POST['ville'] ?? '',
            // Use the provided image URL if given, otherwise keep the existing image
            'image' => $imageUrl !== '' ? $imageUrl : ($existing['image'] ?? null)
        ];

        if (!empty($_FILES['image_file']['name'])) {
            if (!$this->isUploadedJpeg($_FILES['image_file'])) {
                header("Location: " . siteUrl('/admin') . "?page=destinations&edit=" . $id . "&error=image_not_jpg");
                exit;
            }

            // remove previous local image (if any) before saving the new one
            if (!empty($existing) && !empty($existing['ville'])) {
                $oldName = normalizeString($existing['ville']) . '.jpg';
                $oldPath = __DIR__ . '/../public/images/' . $oldName;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $ville = $data['ville'] ?? '';
            $uploadedLocal = $this->handleImageUpload($_FILES['image_file'], $ville);
            // Local image is stored on disk, not in DB - keep the image URL
        } elseif (!empty($existing) && !empty($existing['ville']) && !empty($data['ville'])) {
            $oldName = normalizeString($existing['ville']) . '.jpg';
            $newName = normalizeString($data['ville']) . '.jpg';
            $oldPath = __DIR__ . '/../public/images/' . $oldName;
            $newPath = __DIR__ . '/../public/images/' . $newName;

            if ($oldName !== $newName && is_file($oldPath) && !is_file($newPath)) {
                @rename($oldPath, $newPath);
            }
        }

        $this->adminModel->updateDestination($id, $data);
        header("Location: " . siteUrl('/admin') . "?page=destinations&success=1");
        exit;
    }

    /**
     * Handle image upload: saves under public/images/destinations/ and returns stored path or null
     */
    private function handleImageUpload($file, $ville)
    {
        $uploadDir = __DIR__ . '/../public/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false || ($info['mime'] ?? '') !== 'image/jpeg') {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg'], true)) {
            return null;
        }

        // sanitize ville to build filename using normalizeString for consistency
        $name = $ville ?: pathinfo($file['name'], PATHINFO_FILENAME);
        $name = normalizeString($name);
        if ($name === '') {
            $name = bin2hex(random_bytes(6));
        }

        $destFilename = $name . '.jpg';
        $destPath = $uploadDir . $destFilename;

        // ensure storage for debug logs
        $storageDir = __DIR__ . '/../storage/';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }

        $log = function($msg) use ($storageDir) {
            $line = '['.date('Y-m-d H:i:s').'] '.$msg."\n";
            @file_put_contents($storageDir.'upload_debug.log', $line, FILE_APPEND);
        };

        if (@move_uploaded_file($file['tmp_name'], $destPath)) {
            @chmod($destPath, 0644);
            $log("moved uploaded jpeg file to $destPath");
            return '/Nomadix/public/images/' . $destFilename;
        }

        $log('move_uploaded_file failed');
        return null;
    }

    /**
     * Retourne un nom de fichier normalisé à partir du nom de la ville
     */
    private function sanitizeVille($ville)
    {
        $name = $ville ?: '';
        // transliterate accents
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        // remove all whitespace (concatenate words)
        $name = preg_replace('/\s+/', '', $name);
        // remove any remaining non-alphanumeric characters
        $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $name = strtolower($name);
        if ($name === '') {
            $name = bin2hex(random_bytes(6));
        }
        return $name;
    }

    /**
     * Supprime une destination (POST)
     */
    public function deleteDestination()
    {
        if (!isset($_POST['destinationId']) || !isset($_POST['csrf_token'])) {
            http_response_code(400);
            die("Parametres manquants");
        }

        if (!$this->verifyCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            die("Token CSRF invalide");
        }

        $destinationId = (int) $_POST['destinationId'];
        // remove local image file if it exists
        $existing = $this->adminModel->getDestinationById($destinationId);
        if (!empty($existing) && !empty($existing['ville'])) {
            $localName = $this->sanitizeVille($existing['ville']) . '.jpg';
            $localPath = __DIR__ . '/../public/images/' . $localName;
            if (is_file($localPath)) {
                @unlink($localPath);
            }
        }

        $this->adminModel->deleteDestination($destinationId);

        header("Location: " . siteUrl('/admin') . "?page=destinations&success=deleted");
        exit;
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

    /**
     * Verifie qu'une URL d'image est syntaxiquement valide.
     */
    private function isValidImageUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https'], true);
    }

    /**
     * Verifie qu'un fichier televerse est un JPEG.
     */
    private function isUploadedJpeg(array $file): bool
    {
        if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }

        $info = @getimagesize($file['tmp_name']);
        if ($info === false || ($info['mime'] ?? '') !== 'image/jpeg') {
            return false;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg'], true);
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
        $query = "SELECT avis.*, utilisateurs.login, utilisateurs.email, destinations.nom as destinationNom
              FROM avis
              INNER JOIN utilisateurs ON avis.idUtilisateur = utilisateurs.id
              INNER JOIN destinations ON avis.idDestination = destinations.id";

        if (!$showAll) {
            $query .= " WHERE avis.verified = 0"; // Filtre pour les avis non vérifiés
        }

        $query .= " ORDER BY avis.verified ASC, avis.dateAvis DESC";
        
        if (!$showAll) {
            $query .= " LIMIT 5";
        }

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
            header("Location: " . siteUrl('/admin'));
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

        header("Location: " . siteUrl('/admin') . "?success=1");
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

    public function getUsersByDate($conn, $order)
    {
        $stmt = $conn->prepare("SELECT COUNT(avis.id) AS 'nb_avis', utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin FROM utilisateurs LEFT JOIN avis ON utilisateurs.id = avis.idUtilisateur GROUP BY utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin ORDER BY utilisateurs.dateCreation $order");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsersByNote($conn, $order)
    {
        $stmt = $conn->prepare("SELECT COUNT(avis.id) AS 'nb_avis', utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin FROM utilisateurs LEFT JOIN avis ON utilisateurs.id = avis.idUtilisateur GROUP BY utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin ORDER BY nb_avis $order");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUsersByNom($conn, $order)
    {
        $stmt = $conn->prepare("SELECT COUNT(avis.id) AS 'nb_avis', utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin FROM utilisateurs LEFT JOIN avis ON utilisateurs.id = avis.idUtilisateur GROUP BY utilisateurs.id, utilisateurs.login, utilisateurs.email, utilisateurs.dateCreation, utilisateurs.admin ORDER BY utilisateurs.login $order");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
