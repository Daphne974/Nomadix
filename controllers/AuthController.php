<?php
// Nomadix/controllers/AuthController.php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    public function register() {
        session_start();
        $userModel = new UserModel();
        $message = '';
        $messageClass = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login = $_POST['login'] ?? '';
            $email = $_POST['email'] ?? '';
            $motDePasse = $_POST['motDePasse'] ?? '';
            $confirmerMotDePasse = $_POST['confirmerMotDePasse'] ?? '';

            // Validation des mots de passe
            if ($motDePasse !== $confirmerMotDePasse) {
                $message = "Les mots de passe ne correspondent pas.";
                $messageClass = "error";
            }
            // Validation de la complexité du mot de passe
            elseif (
                strlen($motDePasse) < 12 ||
                !preg_match('/[A-Z]/', $motDePasse) ||
                !preg_match('/[a-z]/', $motDePasse) ||
                !preg_match('/[0-9]/', $motDePasse) ||
                !preg_match('/[\W_]/', $motDePasse)
            ) {
                $message = "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un symbole.";
                $messageClass = "error";
            }
            // Vérification de l'unicité de l'email
            elseif ($userModel->emailExists($email)) {
                $message = "Cet e-mail est déjà utilisé pour un autre compte.";
                $messageClass = "error";
            }
            // Si tout est valide, enregistrer l'utilisateur
            else {
                $motDePasseHache = password_hash($motDePasse, PASSWORD_BCRYPT);
                if ($userModel->registerUser($login, $email, $motDePasseHache)) {
                    $user = $userModel->getUserByLogin($login);
                    if ($user) {
                        $_SESSION['user'] = $user;
                        header("Location: index.php");
                        exit;
                    }
                    $message = "Inscription réussie !";
                    $messageClass = "success";
                } else {
                    $message = "Erreur lors de l'inscription : " . $this->db->getConnection()->error;
                    $messageClass = "error";
                }
            }
        }

        // Gestion des messages flash
        if (!empty($message)) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_message_class'] = $messageClass;
        }

        // Inclure la vue
        require_once __DIR__ . '/../views/register.php';
    }
}