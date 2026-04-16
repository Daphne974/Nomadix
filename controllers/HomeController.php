<?php
// controllers/HomeController.php
require_once __DIR__ . '/../models/DestinationModel.php';

class HomeController {
    public function index() {
        session_start();
        $_SESSION['page_davant'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $destinationModel = new DestinationModel();
        $recherche = $_POST['recherche'] ?? '';
        $destinations = $destinationModel->searchDestinations($recherche);

        // Gestion des messages flash
        $message = '';
        $messageClass = '';
        $supprime = $_GET['supprime'] ?? null;
        if ($supprime === 'ok') {
            $message = 'Votre compte a été supprimé avec succès';
            $messageClass = "success";
        } else if ($supprime === 'non') {
            $message = 'Votre compte n\'a pas été supprimé. Veuillez recommencer.';
            $messageClass = "error";
        }

        if (!empty($message)) {
            if (!isset($_SESSION['messageShown'])) {
                $_SESSION['messageShown'] = true;
            } else {
                $message = '';
            }
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