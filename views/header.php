<?php
// Nomadix/views/header.php
// Initialiser la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gérer la déconnexion
if (isset($_POST['deconnectetoi'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($currentPage === 'inscription.php') {
            echo "Inscription - Nomadix";
        } elseif ($currentPage === 'connexion.php') {
            echo "Connexion - Nomadix";
        } elseif ($currentPage === 'admin.php') {
            echo "Administration - Nomadix";
        } else {
            echo "Nomadix - Découvrez le monde";
        }
        ?>
    </title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/script.js" defer></script>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <?php if(isset($_SESSION["user"])): ?>
                    <a href="index.php"><h1>Nomadix</h1></a>
                <?php else: ?>
                    <a href="index.php"><h1>Bienvenue sur Nomadix</h1></a>
                    <p>Découvrez nos destinations et <a href="inscription.php" style="color: rgb(152, 0, 207);">créez votre compte</a> dès maintenant !</p>
                <?php endif; ?>
            </div>
            <nav class="nav-buttons">
                <?php 
                $currentPage = basename($_SERVER['PHP_SELF']);
                if (isset($_SESSION["user"])): ?>
                    <span class="user-info">👤 <?= htmlspecialchars($_SESSION["user"]["login"]) ?></span>
                    <a href="profil.php" class="button4">Profil</a>
                    <?php if ((int)$_SESSION['user']['admin'] === 1): ?>
                        <a href="admin.php" class="button-admin">Admin</a>
                    <?php endif; ?>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="deconnectetoi" class="button3" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter?')">Déconnexion</button>
                    </form>
                <?php elseif ($currentPage === 'connexion.php'): ?>
                    <a href="index.php" class="button1">Accueil</a>
                    <a href="inscription.php" class="button1">Inscription</a>
                <?php elseif ($currentPage === 'inscription.php'): ?>
                    <a href="index.php" class="button1">Accueil</a>
                    <a href="connexion.php" class="button2">Connexion</a>
                <?php else: ?>
                    <a href="connexion.php" class="button2">Se connecter</a>
                    <a href="inscription.php" class="button1">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    </header>