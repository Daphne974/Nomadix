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
            header('Location: 404.php');
            exit;
        }

        // Traitement des mises à jour du profil (email / mot de passe / login)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            // Initialisation des messages flash
            $message = '';
            $messageClass = 'error';

            // Récupération du mot de passe courant pour vérification
            $currentPassword = $_POST['currentPassword'] ?? '';

            if ($action === 'update_avatar') {
                $chosen = $_POST['avatar'] ?? '';
                // sécuriser le choix: ne garder que le basename et vérifier qu'il existe dans avatars dir
                $basename = basename($chosen);
                $avatarDir = __DIR__ . '/../public/profil';
                $allowed = [];
                if (is_dir($avatarDir)) {
                    $files = scandir($avatarDir);
                    foreach ($files as $f) {
                        if (is_file($avatarDir . '/' . $f) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f)) {
                            $allowed[] = $f;
                        }
                    }
                }

                if (!in_array($basename, $allowed, true)) {
                    $message = 'Avatar non valide.';
                } else {
                    $path = 'public/profil/' . $basename;
                    if ($userModel->updateAvatar($userId, $path)) {
                        $_SESSION['user']['avatar'] = $path;
                        $message = 'Avatar mis à jour.';
                        $messageClass = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour de l\'avatar.';
                    }
                }
            }

            if ($action === 'update_email') {
                $newEmail = trim($_POST['email'] ?? '');
                if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Adresse e-mail invalide.';
                } elseif ($userModel->emailExistsExceptUser($newEmail, $userId)) {
                    $message = 'Cet e-mail est déjà utilisé pour un autre compte.';
                } elseif (!password_verify($currentPassword, $user['motDePasse'])) {
                    $message = 'Mot de passe courant incorrect.';
                } else {
                    if ($userModel->updateEmail($userId, $newEmail)) {
                        $_SESSION['user']['email'] = $newEmail;
                        $message = 'E-mail mis à jour.';
                        $messageClass = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour de l\'e-mail.';
                    }
                }
            } elseif ($action === 'update_password') {
                $newPassword = $_POST['newPassword'] ?? '';
                $confirmNewPassword = $_POST['confirmNewPassword'] ?? '';

                if (empty($currentPassword) || !password_verify($currentPassword, $user['motDePasse'])) {
                    $message = 'Mot de passe courant incorrect.';
                } elseif ($newPassword !== $confirmNewPassword) {
                    $message = 'Les nouveaux mots de passe ne correspondent pas.';
                } elseif (
                    strlen($newPassword) < 12 ||
                    !preg_match('/[A-Z]/', $newPassword) ||
                    !preg_match('/[a-z]/', $newPassword) ||
                    !preg_match('/[0-9]/', $newPassword) ||
                    !preg_match('/[\W_]/', $newPassword)
                ) {
                    $message = 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un symbole.';
                } else {
                    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                    if ($userModel->updatePassword($userId, $hash)) {
                        $message = 'Mot de passe mis à jour.';
                        $messageClass = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour du mot de passe.';
                    }
                }
            } elseif ($action === 'update_login') {
                $newLogin = trim($_POST['login'] ?? '');
                if (empty($newLogin)) {
                    $message = 'Le pseudo ne peut pas être vide.';
                } elseif (!$userModel->isLoginAvailableExceptUser($newLogin, $userId)) {
                    $message = 'Ce pseudo est déjà pris.';
                } else {
                    // Vérifier la dernière modification (4 mois)
                    $loginChangedAt = $userModel->getLoginChangedAt($userId);
                    $canChange = true;
                    if ($loginChangedAt) {
                        $last = new DateTime($loginChangedAt);
                        $now = new DateTime();
                        $interval = $now->diff($last);
                        $months = ($interval->y * 12) + $interval->m;
                        if ($months < 4) {
                            $canChange = false;
                            $remaining = 4 - $months;
                            $message = 'Le pseudo ne peut être modifié que toutes les 4 mois. Il reste ' . $remaining . ' mois.';
                        }
                    }

                    if ($canChange) {
                        if ($userModel->updateLogin($userId, $newLogin)) {
                            $_SESSION['user']['login'] = $newLogin;
                            $message = 'Pseudo mis à jour.';
                            $messageClass = 'success';
                        } else {
                            $message = 'Erreur lors de la mise à jour du pseudo.';
                        }
                    }
                }
            } elseif ($action === 'delete_account') {
                // Suppression du compte utilisateur
                if (empty($currentPassword) || !password_verify($currentPassword, $user['motDePasse'])) {
                    $message = 'Mot de passe courant incorrect.';
                } else {
                    // Si l'utilisateur est admin, vérifier qu'il existe au moins un autre admin
                    $isAdmin = (int)$user['admin'] === 1;
                    if ($isAdmin) {
                        $adminCount = $userModel->countAdmins();
                        if ($adminCount <= 1) {
                            $message = 'Impossible de supprimer ce compte administrateur : il doit rester au moins un administrateur.';
                        }
                    }

                    if (empty($message)) {
                        if ($userModel->deleteUser($userId)) {
                            // Détruire la session et rediriger
                            session_unset();
                            session_destroy();
                            header('Location: index.php');
                            exit;
                        } else {
                            $message = 'Erreur lors de la suppression du compte.';
                        }
                    }
                }
            }

            if (!empty($message)) {
                $_SESSION['flash_message'] = $message;
                $_SESSION['flash_message_class'] = $messageClass;
            }

            header('Location: profil.php');
            exit;
        }

        // Inclure la vue
        require_once __DIR__ . '/../views/header.php';
        require_once __DIR__ . '/../views/profil.php';
        require_once __DIR__ . '/../views/footer.php';
    }
}
?>