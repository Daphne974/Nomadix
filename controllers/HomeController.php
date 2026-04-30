<?php
// Nomadix/controllers/HomeController.php
require_once __DIR__ . '/../models/DestinationModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class HomeController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['page_davant'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $destinationModel = new DestinationModel();
        $recherche = $_POST['recherche'] ?? '';
        $destinations = $destinationModel->searchDestinations($recherche);

        // Gestion des messages flash
        if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_message_class'])) {
            $message = $_SESSION['flash_message'];
            $messageClass = $_SESSION['flash_message_class'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_class']);
        }

        // Gestion de la déconnexion
        if (isset($_POST["deconnectetoi"])) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
        }

        // Inclure la vue
        require_once __DIR__ . '/../views/home.php';
    }

    public function showProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            header("Location: connexion.php");
            exit;
        }

        $userModel = new UserModel();
        $userId = (int)$_SESSION['user']['id'];
        $user = $userModel->getUserById($userId);

        if (!$user) {
            die("Utilisateur non trouvé.");
        }

        // Inclure la vue
        require_once __DIR__ . '/../views/header.php';
        require_once __DIR__ . '/../views/profil.php';
        require_once __DIR__ . '/../views/footer.php';
    }
}
?>