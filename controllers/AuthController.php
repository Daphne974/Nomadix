<?php
// Nomadix/controllers/AuthController.php
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

                // Choisir un avatar aléatoire depuis public/profil si présent (SVG autorisés)
                $avatarDir = __DIR__ . '/../public/profil';
                $avatarWebPath = null;
                if (is_dir($avatarDir)) {
                    $files = array_values(array_filter(scandir($avatarDir), function($f) use ($avatarDir) {
                        return is_file($avatarDir . '/' . $f) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f);
                    }));
                    if (count($files) > 0) {
                        $pick = $files[array_rand($files)];
                        // chemin relatif pour l'utilisation dans les balises img
                        $avatarWebPath = 'public/profil/' . $pick;
                    }
                }

                if ($userModel->registerUser($login, $email, $motDePasseHache, $avatarWebPath)) {
                    $user = $userModel->getUserByLogin($login);
                    if ($user) {
                        $_SESSION['user'] = $user;
                        header("Location: index.php");
                        exit;
                    }
                    $message = "Inscription réussie !";
                    $messageClass = "success";
                } else {
                    $message = "Erreur lors de l'inscription.";
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

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = '';
        $messageClass = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login = trim($_POST['login'] ?? '');
            $motDePasse = $_POST['motDePasse'] ?? '';

            // Validation des champs
            if (empty($login) || empty($motDePasse)) {
                $message = "Veuillez remplir tous les champs.";
                $messageClass = "error";
            } else {
                $userModel = new UserModel();
                $user = $userModel->getUserByLogin($login);

                // Vérifier les identifiants
                if ($user && password_verify($motDePasse, $user['motDePasse'])) {
                    $_SESSION['user'] = $user;
                    $_SESSION['user']['is_admin'] = (int)$user['admin'] === 1;
                    
                    // Redirection selon le rôle
                    if ($_SESSION['user']['is_admin']) {
                        header("Location: admin.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                } else {
                    $message = "Identifiants incorrects.";
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
        require_once __DIR__ . '/../views/login.php';
    }
}
?>