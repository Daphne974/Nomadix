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

$avisTri = $_GET['tri_avis'] ?? 'best';
$triOptions = ['best', 'worst', 'recent', 'old'];
if (!in_array($avisTri, $triOptions, true)) {
    $avisTri = 'best';
}

$avisNoteFilter = isset($_GET['note_avis']) ? (int) $_GET['note_avis'] : 0;
if ($avisNoteFilter < 1 || $avisNoteFilter > 5) {
    $avisNoteFilter = 0;
}

$avisStats = [
    'total' => count($allAvis),
    'average' => 0,
    'counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
];
$totalNotes = 0;
foreach ($allAvis as $avisItem) {
    $noteItem = (int) ($avisItem['note'] ?? 0);
    if ($noteItem >= 1 && $noteItem <= 5) {
        $avisStats['counts'][$noteItem]++;
        $totalNotes += $noteItem;
    }
}
if ($avisStats['total'] > 0) {
    $avisStats['average'] = round($totalNotes / $avisStats['total'], 1);
}

$avisFiltres = array_values(array_filter($allAvis, function ($avisItem) use ($avisNoteFilter) {
    return $avisNoteFilter === 0 || (int) ($avisItem['note'] ?? 0) === $avisNoteFilter;
}));

usort($avisFiltres, function ($a, $b) use ($avisTri) {
    $noteA = (int) ($a['note'] ?? 0);
    $noteB = (int) ($b['note'] ?? 0);
    $dateA = strtotime($a['dateAvis'] ?? '') ?: 0;
    $dateB = strtotime($b['dateAvis'] ?? '') ?: 0;

    return match ($avisTri) {
        'worst' => [$noteA, -$dateA] <=> [$noteB, -$dateB],
        'recent' => $dateB <=> $dateA,
        'old' => $dateA <=> $dateB,
        default => [$noteB, $dateB] <=> [$noteA, $dateA],
    };
});

$avisUrl = function (string $tri, int $note = 0) use ($destination): string {
    $params = [
        'ville' => $destination['ville'],
        'tri_avis' => $tri,
    ];
    if ($note > 0) {
        $params['note_avis'] = $note;
    }
    return siteUrl('/destination') . '?' . http_build_query($params) . '#avis';
};

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
                <form action="" method="post" class="avis-form" onsubmit="return validateRating(this)">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div class="evaluation">
                        <input type="radio" name="note" id="star5" value="5" <?= ($userAvis && $userAvis['note'] == 5) ? 'checked' : '' ?> required>
                        <label for="star5">&#9733;</label>
                        <input type="radio" name="note" id="star4" value="4" <?= ($userAvis && $userAvis['note'] == 4) ? 'checked' : '' ?> required>
                        <label for="star4">&#9733;</label>
                        <input type="radio" name="note" id="star3" value="3" <?= ($userAvis && $userAvis['note'] == 3) ? 'checked' : '' ?> required>
                        <label for="star3">&#9733;</label>
                        <input type="radio" name="note" id="star2" value="2" <?= ($userAvis && $userAvis['note'] == 2) ? 'checked' : '' ?> required>
                        <label for="star2">&#9733;</label>
                        <input type="radio" name="note" id="star1" value="1" <?= ($userAvis && $userAvis['note'] == 1) ? 'checked' : '' ?> required>
                        <label for="star1">&#9733;</label>
                    </div>
                    <textarea name="commentaire" placeholder="Partagez votre expérience..."
                        maxlength="500"><?= htmlspecialchars($userAvis['commentaire'] ?? '') ?></textarea>
                    <small class="comment-limit">500 caracteres maximum.</small>
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

    <section class="avis-wrapper" id="avis">
        <div class="avis-section-header">
            <div>
                <h2>Avis des voyageurs</h2>
                <p><?= htmlspecialchars($avisStats['total']) ?> avis au total</p>
            </div>
        </div>

        <div class="avis-dashboard">
            <div class="avis-summary-card avis-summary-main">
                <span class="avis-summary-label">Note moyenne</span>
                <strong><?= htmlspecialchars(number_format((float) $avisStats['average'], 1)) ?>/5</strong>
                <span><?= htmlspecialchars($avisStats['total']) ?> avis</span>
            </div>
            <?php for ($note = 5; $note >= 1; $note--): ?>
                <?php
                $countNote = $avisStats['counts'][$note];
                $percentNote = $avisStats['total'] > 0 ? round(($countNote / $avisStats['total']) * 100) : 0;
                ?>
                <a href="<?= htmlspecialchars($avisUrl($avisTri, $note)) ?>" class="avis-summary-card <?= $avisNoteFilter === $note ? 'active' : '' ?>">
                    <span class="avis-summary-label"><?= $note ?> etoile<?= $note > 1 ? 's' : '' ?></span>
                    <strong><?= htmlspecialchars($countNote) ?></strong>
                    <span class="avis-bar"><span style="width: <?= htmlspecialchars($percentNote) ?>%;"></span></span>
                </a>
            <?php endfor; ?>
        </div>

        <div class="avis-toolbar">
            <div class="avis-filter-links">
                <a href="<?= htmlspecialchars($avisUrl($avisTri, 0)) ?>" class="<?= $avisNoteFilter === 0 ? 'active' : '' ?>">Tous</a>
                <?php for ($note = 5; $note >= 1; $note--): ?>
                    <a href="<?= htmlspecialchars($avisUrl($avisTri, $note)) ?>" class="<?= $avisNoteFilter === $note ? 'active' : '' ?>"><?= $note ?> etoiles</a>
                <?php endfor; ?>
            </div>
            <form method="get" action="<?= htmlspecialchars(siteUrl('/destination')) ?>" class="avis-sort-form">
                <input type="hidden" name="ville" value="<?= htmlspecialchars($destination['ville']) ?>">
                <?php if ($avisNoteFilter > 0): ?>
                    <input type="hidden" name="note_avis" value="<?= htmlspecialchars($avisNoteFilter) ?>">
                <?php endif; ?>
                <label for="tri_avis">Trier</label>
                <select id="tri_avis" name="tri_avis" onchange="this.form.submit()">
                    <option value="best" <?= $avisTri === 'best' ? 'selected' : '' ?>>Meilleurs avis</option>
                    <option value="worst" <?= $avisTri === 'worst' ? 'selected' : '' ?>>Moins bons avis</option>
                    <option value="recent" <?= $avisTri === 'recent' ? 'selected' : '' ?>>Plus recents</option>
                    <option value="old" <?= $avisTri === 'old' ? 'selected' : '' ?>>Plus anciens</option>
                </select>
            </form>
        </div>

        <div class="avis-table-wrap">
            <?php if (empty($avisFiltres)): ?>
                <div class="avis-empty-state">
                    <h3>Aucun avis trouve</h3>
                    <p>Changez le filtre ou consultez tous les avis.</p>
                </div>
            <?php else: ?>
                <table class="avis-table">
                    <thead>
                        <tr>
                            <th>Voyageur</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avisFiltres as $avis): ?>
                            <tr>
                                <td data-label="Voyageur">
                                    <div class="avis-user-cell">
                                        <?php if (!empty($avis['avatar'])): ?>
                                            <img src="<?= htmlspecialchars($avis['avatar']) ?>" alt="avatar" class="avis-avatar">
                                        <?php else: ?>
                                            <div class="avis-avatar avis-avatar-fallback"><?= htmlspecialchars(strtoupper(substr($avis['login'] ?? 'U', 0, 1))) ?></div>
                                        <?php endif; ?>
                                        <strong><?= htmlspecialchars_decode($avis['login'] ?? 'Utilisateur') ?></strong>
                                    </div>
                                </td>
                                <td data-label="Note">
                                    <span class="avis-note-badge"><?= htmlspecialchars($avis['note']) ?>/5</span>
                                </td>
                                <td data-label="Commentaire">
                                    <p class="avis-table-comment"><?= nl2br(htmlspecialchars_decode($avis['commentaire'] ?? '')) ?></p>
                                </td>
                                <td data-label="Date">
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($avis['dateAvis']))) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
    </div>

    <?php
    require_once __DIR__ . '/footer.php';
    ?>

</body>

</html>
