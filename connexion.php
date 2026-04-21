<?php
// Nomadix/connexion.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Database.php'; // <-- Ajoute cette ligne

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = $_POST['login'] ?? '';
        $motDePasse = $_POST['motDePasse'] ?? '';

        // Utilise Database::getReadConnection() au lieu de créer une nouvelle connexion
        $conn = Database::getClientConnection(); // <-- Modifie cette ligne

        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($motDePasse, $user['motDePasse'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = $user;
            // Ajoute le rôle admin dans la session
            $_SESSION['user']['is_admin'] = ($user['admin'] == 1); // <-- Ajoute cette ligne
            header("Location: index.php");
            exit;
        } else {
            $message = "Identifiants incorrects.";
            $messageClass = "error";
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
    <title>Connexion - Nomadix</title>
    <link rel="stylesheet" href="/Nomadix/public/css/style.css">
    <script src="/Nomadix/public/js/script.js" defer></script>
</head>
<body>
    <header>
        <div class="nav-buttons">
            <a href="inscription.php" class="button1">S'inscrire</a>
        </div>
    </header>

    <main>
        <?php if (isset($message) && isset($messageClass)): ?>
            <div id="flashMessage" class="message <?= $messageClass ?>">
                <span class="close-btn" onclick="closeFlashMessage()">&times;</span>
                <?= htmlspecialchars($message) ?>
                <div class="progress-bar"></div>
            </div>
        <?php endif; ?>

        <div class="forms">
            <form method="POST">
                <h2>Connexion</h2>
                <label>Login :</label>
                <input type="text" name="login" required><br><br>
                <label>Mot de passe :</label>
                <input type="password" name="motDePasse" required><br><br>
                <input type="submit" value="Se connecter"><br><br>
            </form>
            <form action="inscription.php" method="get">
                <button type="submit" class="connect-button">Aller s'inscrire</button>
            </form>
        </div>
    </main>

    <footer>&copy; 2026 Nomadix - Tous droits réservés</footer>
</body>
</html>