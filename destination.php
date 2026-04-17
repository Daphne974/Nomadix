<?php
// Nomadix/destination.php
require_once __DIR__ . '/config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $ville = $_GET['ville'] ?? '';
    if (empty($ville)) {
        die("Ville non spécifiée.");
    }

    $stmt = $conn->prepare("SELECT ville, pays, nom FROM destinations WHERE ville = ?");
    $stmt->execute([$ville]);
    $destination = $stmt->fetch();

    if (!$destination) {
        die("Destination non trouvée.");
    }

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