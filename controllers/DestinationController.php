<?php
// Nomadix/controllers/DestinationController.php
require_once __DIR__ . '/../models/DestinationModel.php';
require_once __DIR__ . '/../models/Database.php';

class DestinationController {
    public function showDestination() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET['ville']) || empty($_GET['ville'])) {
            header("HTTP/1.0 404 Not Found");
            include __DIR__ . '/../404.php';
            exit;
        }

        try {
            $ville = $_GET['ville'];
            $destinationModel = new DestinationModel();
            $destination = $destinationModel->getDestinationByVille($ville);

            if (!$destination) {
                header("HTTP/1.0 404 Not Found");
                include __DIR__ . '/../404.php';
                exit;
            }

            // Récupérer les avis
            $conn = Database::getClientConnection();
            $stmt = $conn->prepare("SELECT avis.*, utilisateurs.login FROM avis INNER JOIN utilisateurs ON avis.idUtilisateur = utilisateurs.id WHERE idDestination = ? ORDER BY dateAvis DESC");
            $stmt->execute([$destination['id']]);
            $allAvis = $stmt->fetchAll();

            // Récupérer la note moyenne
            $stmt = $conn->prepare("SELECT ROUND(AVG(note), 1) AS moyenne_notes FROM avis WHERE idDestination = ?");
            $stmt->execute([$destination['id']]);
            $noteMoyenne = $stmt->fetch()['moyenne_notes'] ?? null;

            // Récupérer l'avis de l'utilisateur connecté
            $userAvis = null;
            if (isset($_SESSION['user'])) {
                $userId = $_SESSION['user']['id'];
                $stmt = $conn->prepare("SELECT note, commentaire FROM avis WHERE idUtilisateur = ? AND idDestination = ?");
                $stmt->execute([$userId, $destination['id']]);
                $userAvis = $stmt->fetch();
            }

            // Gérer la soumission du formulaire
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_SESSION['user']) && isset($_POST['note'])) {
                    $note = (int)$_POST['note'];
                    $commentaire = $_POST['commentaire'] ?? '';

                    if ($userAvis) {
                        $stmt = $conn->prepare("UPDATE avis SET note = ?, commentaire = ?, dateAvis = CURRENT_TIMESTAMP WHERE idUtilisateur = ? AND idDestination = ?");
                        $stmt->execute([$note, $commentaire, $userId, $destination['id']]);
                        $message = "Avis modifié avec succès.";
                        $messageClass = "success";
                    } else {
                        $stmt = $conn->prepare("INSERT INTO avis (idUtilisateur, idDestination, note, commentaire, dateAvis) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
                        $stmt->execute([$userId, $destination['id'], $note, $commentaire]);
                        $message = "Avis ajouté avec succès.";
                        $messageClass = "success";
                    }
                    header("Location: destination.php?ville=" . urlencode($ville));
                    exit;
                } elseif (isset($_POST['supprimer_avis'])) {
                    $stmt = $conn->prepare("DELETE FROM avis WHERE idUtilisateur = ? AND idDestination = ?");
                    $stmt->execute([$userId, $destination['id']]);
                    $message = "Avis supprimé avec succès.";
                    $messageClass = "success";
                    header("Location: destination.php?ville=" . urlencode($ville));
                    exit;
                }
            }

            // Gérer la déconnexion
            if (isset($_POST["deconnectetoi"])) {
                session_unset();
                session_destroy();
                header("Location: index.php");
                exit;
            }

            // Inclure la vue
            require_once __DIR__ . '/../views/destination.php';

        } catch (PDOException $e) {
            die("❌ Erreur : " . $e->getMessage());
        }
    }
}
?>