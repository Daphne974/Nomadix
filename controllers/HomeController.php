<?php
// Nomadix/controllers/HomeController.php
require_once __DIR__ . '/../models/DestinationModel.php';

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
}
?>