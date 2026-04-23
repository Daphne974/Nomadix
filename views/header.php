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
                <a href="index.php"><h1>🌍 Nomadix</h1></a>
            </div>
            <nav class="nav-buttons">
                <?php if (isset($_SESSION["user"])): ?>
                    <span class="user-info">👤 <?= htmlspecialchars($_SESSION["user"]["login"]) ?></span>
                    <a href="profil.php" class="button4">Profil</a>
                    <?php if ((int)$_SESSION['user']['admin'] === 1): ?>
                        <a href="admin.php" class="button-admin">Admin</a>
                    <?php endif; ?>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="deconnectetoi" class="button3" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter?')">Déconnexion</button>
                    </form>
                <?php else: ?>
                    <a href="connexion.php" class="button2">Se connecter</a>
                    <a href="inscription.php" class="button1">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    </header>