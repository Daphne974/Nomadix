<?php
// Nomadix/views/register.php
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
            <h2>Inscription</h2>
            <label>Login :</label>
            <input type="text" name="login" required><br><br>
            <label>Email :</label>
            <input type="email" name="email" required><br><br>
            <label>Mot de passe :</label>
            <input type="password" name="motDePasse" required><br><br>
            <label>Confirmer le mot de passe :</label>
            <input type="password" name="confirmerMotDePasse" required>
            <input type="submit" value="S'inscrire"><br><br>
        </form>
        <div class="register-link">
                <p>Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/footer.php';
?>