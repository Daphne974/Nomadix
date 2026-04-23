<?php
// Nomadix/controllers/DestinationController.php
require_once __DIR__ . '/../models/DestinationModel.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../config/config.php';

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
            // Nettoyer et valider le paramètre ville
            $ville = sanitizeInput($_GET['ville']);
            if (strlen($ville) < 2 || strlen($ville) > 100) {
                throw new Exception("Paramètre invalide");
            }

            $destinationModel = new DestinationModel();
            $destination = $destinationModel->getDestinationByVille($ville);

            if (!$destination) {
                header("HTTP/1.0 404 Not Found");
                include __DIR__ . '/../404.php';
                exit;
            }

            // Récupérer les avis
            $conn = Database::getClientConnection();
            $stmt = $conn->prepare("
                SELECT a.*, u.login 
                FROM avis a
                INNER JOIN utilisateurs u ON a.idUtilisateur = u.id 
                WHERE a.idDestination = ? 
                ORDER BY a.dateAvis DESC
            ");
            $stmt->execute([$destination['id']]);
            $allAvis = $stmt->fetchAll();

            // Récupérer la note moyenne
            $stmt = $conn->prepare("SELECT ROUND(AVG(note), 1) AS moyenne_notes FROM avis WHERE idDestination = ?");
            $stmt->execute([$destination['id']]);
            $noteMoyenne = $stmt->fetch()['moyenne_notes'] ?? null;

            // Récupérer l'avis de l'utilisateur connecté
            $userAvis = null;
            $message = '';
            $messageClass = '';
            
            if (isset($_SESSION['user'])) {
                $userId = (int)$_SESSION['user']['id'];
                $stmt = $conn->prepare("SELECT id, note, commentaire FROM avis WHERE idUtilisateur = ? AND idDestination = ?");
                $stmt->execute([$userId, (int)$destination['id']]);
                $userAvis = $stmt->fetch();
            }

            // Générer token CSRF
            $csrfToken = generateCsrfToken();

            // Gérer la soumission du formulaire
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérifier le CSRF token
                if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                    throw new Exception("Token de sécurité invalide");
                }

                if (isset($_SESSION['user'])) {
                    $userId = (int)$_SESSION['user']['id'];

                    if (isset($_POST['note'])) {
                        $note = (int)$_POST['note'];
                        $commentaire = sanitizeInput($_POST['commentaire'] ?? '');

                        // Valider la note
                        if ($note < 1 || $note > 5) {
                            throw new Exception("La note doit être entre 1 et 5");
                        }

                        $conn = Database::getAdminConnection();

                        if ($userAvis) {
                            $stmt = $conn->prepare("UPDATE avis SET note = ?, commentaire = ?, dateAvis = CURRENT_TIMESTAMP WHERE id = ? AND idUtilisateur = ?");
                            $stmt->execute([$note, $commentaire, (int)$userAvis['id'], $userId]);
                            $message = "✓ Avis modifié avec succès.";
                            $messageClass = "success";
                        } else {
                            $stmt = $conn->prepare("INSERT INTO avis (idUtilisateur, idDestination, note, commentaire, dateAvis) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
                            $stmt->execute([$userId, (int)$destination['id'], $note, $commentaire]);
                            $message = "✓ Avis ajouté avec succès.";
                            $messageClass = "success";
                        }
                        
                        $_SESSION['flash_message'] = $message;
                        $_SESSION['flash_message_class'] = $messageClass;
                        header("Location: destination.php?ville=" . urlencode($ville));
                        exit;
                    } elseif (isset($_POST['supprimer_avis']) && $userAvis) {
                        $conn = Database::getAdminConnection();
                        $stmt = $conn->prepare("DELETE FROM avis WHERE id = ? AND idUtilisateur = ?");
                        $stmt->execute([(int)$userAvis['id'], $userId]);
                        
                        $_SESSION['flash_message'] = "✓ Avis supprimé avec succès.";
                        $_SESSION['flash_message_class'] = "success";
                        header("Location: destination.php?ville=" . urlencode($ville));
                        exit;
                    }
                } else {
                    throw new Exception("Vous devez être connecté pour laisser un avis");
                }
            }

            // Inclure la vue
            require_once __DIR__ . '/../views/destination.php';

        } catch (Exception $e) {
            $_SESSION['flash_message'] = "❌ Erreur : " . htmlspecialchars($e->getMessage());
            $_SESSION['flash_message_class'] = "error";
            header("Location: index.php");
            exit;
        }
    }
}
?>
