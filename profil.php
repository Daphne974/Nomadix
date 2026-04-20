<?php
// Nomadix/profil.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/views/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit;
}

try {
    $userModel = new UserModel();
    $userId = is_array($_SESSION['user']) ? $_SESSION['user']['id'] : $_SESSION['user'];
    $user = $userModel->getUserById($userId);

    if (!$user) {
        die("Utilisateur non trouvé.");
    }
} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>

<main>
    <div class="forms">
        <h2>Profil de <?= htmlspecialchars($user['login']) ?></h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Compte créé le :</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
        <form action="index.php" method="get">
            <button type="submit" class="home-button">Retour à l'accueil</button>
        </form>
    </div>
</main>

<?php
require_once __DIR__ . '/views/footer.php';
?>