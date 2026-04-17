<?php
// Nomadix/views/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($currentPage === 'inscription.php') {
            echo "Inscription - Nomadix";
        } elseif ($currentPage === 'connexion.php') {
            echo "Connexion - Nomadix";
        } else {
            echo "Nomadix";
        }
        ?>
    </title>
    <!-- Lien vers le CSS -->
    <link rel="stylesheet" href="/Nomadix/public/css/style.css">
    <!-- Lien vers le JS -->
    <script src="/Nomadix/public/js/script.js" defer></script>
</head>
<body>
    <header>
        <?php
        if (isset($_SESSION["user"])) {
            echo "<div class=\"nav-buttons\">
                <form action=\"index.php\" method=\"post\" class=\"deconnection\">
                    <a href=\"profil.php\" class=\"button4\">Profil</a>
                    <button type=\"submit\" name=\"deconnectetoi\" class=\"button3\" onclick=\"return confirm('Es-tu sûr de vouloir te déconnecter ?')\">Se déconnecter</button>
                </form>
            </div>";
        } else {
            echo "<div class=\"nav-buttons\">
                <a href=\"connexion.php\" class=\"button2\">Se connecter</a>
                <a href=\"inscription.php\" class=\"button1\">S'inscrire</a>
            </div>";
        }
        ?>
    </header>