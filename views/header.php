<?php
// views/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Nomadix</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/script.js"></script>
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