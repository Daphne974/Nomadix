<?php
// Nomadix/destination.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Database.php';
require_once __DIR__ . '/models/DestinationModel.php';

// Vérifier si le paramètre 'ville' est présent
if (!isset($_GET['ville']) || empty($_GET['ville'])) {
    header("HTTP/1.0 404 Not Found");
    include __DIR__ . '/404.php';
    exit;
}

try {
    $ville = $_GET['ville'];
    $destinationModel = new DestinationModel();
    $destinations = $destinationModel->searchDestinations($ville);

    if (empty($destinations)) {
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/404.php';
        exit;
    }
    $destination = $destinations[0];

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($destination['ville']) ?> - Nomadix</title>
    <link rel="stylesheet" href="/Nomadix/public/css/style.css">
    <script src="/Nomadix/public/js/script.js" defer></script>
</head>
<body>
    <?php require_once __DIR__ . '/views/header.php'; ?>

    <main>
        <h1><?= htmlspecialchars($destination['ville']) ?> - <?= htmlspecialchars($destination['pays']) ?></h1>
        <p><?= htmlspecialchars($destination['nom']) ?></p>
        <img src="/Nomadix/public/images/<?= normalizeString($destination['ville']) ?>.jpg" alt="<?= htmlspecialchars($destination['ville']) ?>">
    </main>

    <?php require_once __DIR__ . '/views/footer.php'; ?>
</body>
</html>