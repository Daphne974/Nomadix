<?php
// views/home.php
require_once __DIR__ . '/header.php';
?>

<main>
    <?php if (!empty($message)): ?>
        <div id="flashMessage" class="message <?= $messageClass ?>">
            <span class="close-btn" onclick="closeFlashMessage()">&times;</span>
            <?= htmlspecialchars($message) ?>
            <div class="progress-bar"></div>
        </div>
    <?php endif; ?>

    <div class="nav-main">
        <?php if (!isset($_SESSION["user"])): ?>
            <h1>Bienvenue sur Nomadix</h1>
            <p>Découvrez nos destinations et <a href="inscription.php" style="color: rgb(152, 0, 207);">créez votre compte</a> dès maintenant !</p>
        <?php else: ?>
            <h1>Nomadix</h1>
        <?php endif; ?>
    </div>

    <div class="search">
        <form method="POST" action="index.php">
            Recherche : <input type="text" name="recherche" value="<?= htmlspecialchars($recherche ?? '') ?>">
            <input type="submit" value="Search!">
        </form>
    </div>

    <div class="destinations">
        <?php if (empty($destinations)): ?>
            <p style="text-align:center; color: rgb(97, 0, 132); font-size: 1.2em;">
                Aucune destination trouvée pour "<?= htmlspecialchars($recherche ?? '') ?>".
            </p>
        <?php endif; ?>
        <?php foreach ($destinations as $dest): ?>
            <?php
            $ville = $dest['ville'];
            $villeURL = normalizeString($ville);
            $imagePath = "public/images/" . $villeURL . ".jpg";
            ?>
            <a href="destination.php?ville=<?= urlencode($ville) ?>">
                <div class="destination">
                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($ville) ?>">
                    <div class="city-name">
                        <?= htmlspecialchars($ville) ?> - <?= htmlspecialchars($dest['pays']) ?> <br>
                        <?= htmlspecialchars($dest['nom']) ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php
require_once __DIR__ . '/footer.php';
?>