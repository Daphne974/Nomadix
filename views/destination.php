<?php
// Nomadix/views/destination.php
// Les variables suivantes doivent être définies par le contrôleur :
// $destination, $allAvis, $noteMoyenne, $userAvis, $csrfToken

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les messages flash
$message = '';
$messageClass = '';
if (isset($_SESSION['flash_message']) && isset($_SESSION['flash_message_class'])) {
    $message = $_SESSION['flash_message'];
    $messageClass = $_SESSION['flash_message_class'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_message_class']);
}


require_once __DIR__ . '/header.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($destination['nom']) ?> - <?= htmlspecialchars($destination['pays']) ?> | Nomadix
    </title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .message {
            padding: 15px 20px;
            margin: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <?php if (!empty($message)): ?>
        <div class="message <?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="destination-panels" style="background-image: url('<?= htmlspecialchars($destination['image']) ?>');">
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
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
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
                    <textarea name="commentaire" placeholder="Partagez votre expérience..."
                        maxlength="1000"><?= htmlspecialchars($userAvis['commentaire'] ?? '') ?></textarea>
                    <?php if ($userAvis): ?>
                        <div class="boutons_modifetsupp">
                            <button name="ok" type="submit" class="envoyer"
                                onclick="return confirm('Êtes-vous sûr de vouloir modifier votre commentaire?')">Modifier</button>
                            <button name="supprimer_avis" type="submit" class="supp_avis"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre commentaire?')">Supprimer</button>
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

    <?php
    require_once __DIR__ . '/footer.php';
    ?>

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