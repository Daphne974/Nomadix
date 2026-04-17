<?php
// Nomadix/connexion.php
require_once __DIR__ . '/config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = $_POST['login'] ?? '';
        $motDePasse = $_POST['motDePasse'] ?? '';

        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($motDePasse, $user['motDePasse'])) {
            session_start();
            $_SESSION['user'] = $user;
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