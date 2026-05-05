<?php
// Nomadix/views/login.php
require_once __DIR__ . '/header.php';
?>

<main>
    <?php
    if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_message_class'])) {
        echo '<div id="flashMessage" class="message ' . $_SESSION['flash_message_class'] . '">
            <span class="close-btn" onclick="closeFlashMessage()">&times;</span>
            ' . htmlspecialchars($_SESSION['flash_message']) . '
            <div class="progress-bar"></div>
        </div>';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_message_class']);
    }
    ?>

    <div class="forms">
        <form method="POST">
            <h2>Se connecter</h2>
            <label>Login :</label>
            <input type="text" name="login" required><br><br>
            <label>Mot de passe :</label>
            <input type="password" name="motDePasse" required>
            <input type="submit" value="Se connecter"><br><br>
        </form>
        <div class="register-link">
                <p>Pas encore de compte ? <a href="<?= htmlspecialchars(siteUrl('/inscription')) ?>">S'inscrire</a></p>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/footer.php';
?>
