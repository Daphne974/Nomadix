<?php
// Nomadix/403.php
http_response_code(403);
require_once __DIR__ . '/views/header.php';
?>

<main>
    <div class="error-container">
        <h1>403 - Accès interdit</h1>
        <p>Désolé, vous n'avez pas la permission d'accéder à cette page.</p>
        <div class="error-actions">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="index.php" class="connect-button">Retour à l'accueil</a>
            <?php else: ?>
                <a href="connexion.php" class="connect-button">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    .error-container {
        text-align: center;
        padding: 50px 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .error-container h1 {
        color: rgb(97, 0, 132);
        font-size: 3em;
        margin-bottom: 20px;
    }

    .error-container p {
        color: rgb(152, 0, 207);
        font-size: 1.2em;
        margin-bottom: 30px;
    }

    .error-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    .error-actions a {
        text-decoration: none;
    }
</style>

<?php
require_once __DIR__ . '/views/footer.php';
?>