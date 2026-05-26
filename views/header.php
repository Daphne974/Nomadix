<?php
// Nomadix/views/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['deconnectetoi'])) {
    session_destroy();
    header("Location: " . siteUrl('/'));
    exit;
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath !== '' && $basePath !== '/' && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}
$routePath = '/' . trim($requestPath, '/');
$currentPage = match ($routePath) {
    '/inscription' => 'inscription.php',
    '/connexion' => 'connexion.php',
    '/admin' => 'admin.php',
    '/profil' => 'profil.php',
    '/403' => '403.php',
    '/404' => '404.php',
    default => basename($_SERVER['PHP_SELF']),
};
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
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
    <link rel="stylesheet" href="<?= htmlspecialchars(siteUrl('/public/css/style.css')) ?>">
    <script src="<?= htmlspecialchars(siteUrl('/public/js/script.js')) ?>" defer></script>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <?php if ($currentPage === '404.php' || $currentPage === '403.php'): ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>">
                        <h1>Nomadix</h1>
                    </a>
                <?php elseif (isset($_SESSION["user"])): ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>">
                        <h1>Nomadix</h1>
                    </a>
                    <?php if ($currentPage === 'index.php'): ?>
                        <p>Heureux de te revoir <?= htmlspecialchars($_SESSION["user"]["login"]) ?>!</p>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>">
                        <h1>Bienvenue sur Nomadix</h1>
                    </a>
                    <p>Découvrez nos destinations et <a href="<?= htmlspecialchars(siteUrl('/inscription')) ?>" style="color: rgb(152, 0, 207);">créez votre compte</a> dès maintenant !</p>
                <?php endif; ?>
            </div>
            <nav class="nav-buttons">
                <?php if (isset($_SESSION["user"])): ?>
                    <span class="user-info"><?= htmlspecialchars($_SESSION["user"]["login"]) ?></span>
                    <a href="<?= htmlspecialchars(siteUrl('/profil')) ?>" class="button4">Profil</a>
                    <?php if ((int) $_SESSION['user']['admin'] === 1): ?>
                        <a href="<?= htmlspecialchars(siteUrl('/admin')) ?>" class="button4">Admin</a>
                    <?php endif; ?>
                    <form method="post" action="<?= htmlspecialchars(siteUrl('/logout')) ?>" style="display:inline;">
                        <button type="submit" name="deconnectetoi" class="button3"
                            onclick="return confirm('Etes-vous sur de vouloir vous deconnecter?')">Deconnexion</button>
                    </form>
                <?php elseif ($currentPage === 'connexion.php'): ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>" class="button1">Accueil</a>
                    <a href="<?= htmlspecialchars(siteUrl('/inscription')) ?>" class="button1">Inscription</a>
                <?php elseif ($currentPage === 'inscription.php'): ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>" class="button1">Accueil</a>
                    <a href="<?= htmlspecialchars(siteUrl('/connexion')) ?>" class="button2">Connexion</a>
                <?php elseif ($currentPage === '404.php' || $currentPage === '403.php'): ?>
                    <a href="<?= htmlspecialchars(siteUrl('/')) ?>" class="button1">Accueil</a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars(siteUrl('/connexion')) ?>" class="button2">Se connecter</a>
                    <a href="<?= htmlspecialchars(siteUrl('/inscription')) ?>" class="button1">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
