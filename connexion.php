<?php
// Nomadix/connexion.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Database.php';

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Initialiser les variables
$message = '';
$messageClass = '';

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = trim($_POST['login'] ?? '');
        $motDePasse = $_POST['motDePasse'] ?? '';

        // Validation des champs
        if (empty($login) || empty($motDePasse)) {
            $message = "Veuillez remplir tous les champs.";
            $messageClass = "error";
        } else {
            $conn = Database::getClientConnection();
            
            // Récupérer l'utilisateur par login
            $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();

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
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Nomadix</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/script.js" defer></script>
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .auth-container h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .auth-container label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        
        .auth-container input[type="text"],
        .auth-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .auth-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .auth-container input[type="submit"]:hover {
            background-color: #2980b9;
        }
        
        .form-divider {
            text-align: center;
            margin: 20px 0;
            color: #7f8c8d;
        }
        
        .register-link {
            text-align: center;
        }
        
        .register-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-buttons">
            <a href="index.php" class="button1">🏠 Accueil</a>
        </div>
    </header>

    <main>
        <?php if (!empty($message)): ?>
            <div class="message <?= htmlspecialchars($messageClass) ?>">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="auth-container">
            <h2>Se connecter</h2>
            <form method="POST">
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" required>

                <label for="motDePasse">Mot de passe:</label>
                <input type="password" id="motDePasse" name="motDePasse" required>

                <input type="submit" value="Se connecter">
            </form>
            
            <div class="form-divider">ou</div>
            
            <div class="register-link">
                <p>Pas encore de compte? <a href="inscription.php">S'inscrire</a></p>
            </div>
        </div>
    </main>

    <footer>&copy; 2026 Nomadix - Tous droits réservés</footer>
</body>
</html>
