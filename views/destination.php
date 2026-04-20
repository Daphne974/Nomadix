<?php
// Nomadix/views/destination.php
// Les variables suivantes doivent être définies par le contrôleur :
// $destination, $allAvis, $noteMoyenne, $userAvis, $message, $messageClass
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($destination['nom']) ?> - <?= htmlspecialchars($destination['pays']) ?> | Nomadix</title>
    <link rel="stylesheet" href="/Nomadix/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php if (isset($message) && isset($messageClass)): ?>
        <div class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <header>
        <?php if (isset($_SESSION["user"])): ?>
            <div class="nav-buttons">
                <form method="post">
                    <button type="submit" name="deconnectetoi" class="button1" onclick="return confirm('Es-tu sûr de vouloir te déconnecter ?')">Se déconnecter</button>
                </form>
            </div>
        <?php else: ?>
            <div class="nav-buttons">
                <a href="connexion.php" class="button2">Se connecter</a>
                <a href="inscription.php" class="button1">S'inscrire</a>
            </div>
        <?php endif; ?>
    </header>

    <div class="nav-main">
        <h1><a href="index.php" style="text-decoration: none;">Nomadix</a></h1>
    </div>

    <div class="destination-panels" style="background-image: url('/Nomadix/public/images/<?= normalizeString($destination['ville']) ?>.jpg');">
        <div class="destination-container">
            <h1 class="destination-title">
                <?= htmlspecialchars($destination['nom']) ?>, <?= htmlspecialchars($destination['pays']) ?>
            </h1>
            <h2 class="destination-note">
                <?php if ($noteMoyenne === null): ?>
                    Aucun avis.
                <?php else: ?>
                    <?php
                    $moyenneArrondi = round($noteMoyenne * 2) / 2;
                    for ($i = 1; $i <= 5; $i++):
                        if ($moyenneArrondi >= $i): ?>
                            <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                        <?php elseif ($moyenneArrondi >= $i - 0.5): ?>
                            <i class="fa-regular fa-star-half-stroke" style="color: #FFD43B;"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-star" style="color: #FFFFFF;"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                    (<?= htmlspecialchars(number_format($noteMoyenne, 1)) ?>/5)
                <?php endif; ?>
            </h2>
            <p class="destination-description">
                <?= nl2br(htmlspecialchars($destination['description'])) ?>
            </p>
        </div>

        <div class="donner-avis">
            <h1>Donnez votre avis !</h1>
            <?php if (!isset($_SESSION["user"])): ?>
                <h2>Veuillez vous connecter pour mettre un avis</h2>
                <div class="boutons">
                    <button onclick="window.location.href='connexion.php'">Se connecter</button>
                    <button onclick="window.location.href='inscription.php'">S'inscrire</button>
                </div>
            <?php else: ?>
                <form action="" method="post" class="avis-form">
                    <div class="evaluation">
                        <input type="radio" name="note" id="star5" value="5" <?= ($userAvis && $userAvis['note'] == 5) ? 'checked' : '' ?>>
                        <label for="star5">&#9733;</label>
                        <input type="radio" name="note" id="star4" value="4" <?= ($userAvis && $userAvis['note'] == 4) ? 'checked' : '' ?>>
                        <label for="star4">&#9733;</label>
                        <input type="radio" name="note" id="star3" value="3" <?= ($userAvis && $userAvis['note'] == 3) ? 'checked' : '' ?>>
                        <label for="star3">&#9733;</label>
                        <input type="radio" name="note" id="star2" value="2" <?= ($userAvis && $userAvis['note'] == 2) ? 'checked' : '' ?>>
                        <label for="star2">&#9733;</label>
                        <input type="radio" name="note" id="star1" value="1" <?= ($userAvis && $userAvis['note'] == 1) ? 'checked' : '' ?>>
                        <label for="star1">&#9733;</label>
                    </div>
                    <textarea name="commentaire" placeholder="Partagez votre expérience..." maxlength="1000"><?= htmlspecialchars($userAvis['commentaire'] ?? '') ?></textarea>
                    <?php if ($userAvis): ?>
                        <div class="boutons_modifetsupp">
                            <button name="ok" type="submit" class="envoyer" onclick="return confirm('Es-tu sûr de vouloir modifier ton commentaire ?')">Modifier</button>
                            <button name="supprimer_avis" type="submit" class="supp_avis" onclick="return confirm('Es-tu sûr de vouloir supprimer ton commentaire ?')">Supprimer</button>
                        </div>
                    <?php else: ?>
                        <button name="ok" type="submit" class="envoyer">Envoyer</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="avis-wrapper">
        <button class="nav-button left" onclick="scrollAvis(-1)">&#139;</button>
        <div class="avis-container" id="avis-container">
            <?php if (empty($allAvis)): ?>
                <div class="avis-card">
                    <h3>Aucun avis pour le moment.</h3>
                    <p>Soyez le premier à en laisser un ! 😉</p>
                </div>
            <?php else: ?>
                <?php foreach ($allAvis as $avis): ?>
                    <div class="avis-card">
                        <h3><?= htmlspecialchars($avis['login']) ?></h3>
                        <p><?= htmlspecialchars($avis['dateAvis']) ?></p>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $avis['note']): ?>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-star" style="color: #FFF;"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <p style="font-size: 16px; margin-top: 10px;"><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button class="nav-button right" onclick="scrollAvis(1)">&#155;</button>
    </div>

    <form action="index.php" method="get">
        <button type="submit" class="home-button">Retour à l'accueil</button>
    </form>

    <footer>
        <p>&copy; 2026 Nomadix - Tous droits réservés</p>
    </footer>

    <script>
        function scrollAvis(direction) {
            const container = document.getElementById("avis-container");
            const cardWidth = 320;
            container.scrollBy({
                left: direction * cardWidth,
                behavior: "smooth"
            });
        }
    </script>
</body>
</html>