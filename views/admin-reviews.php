<?php
// Nomadix/views/admin-reviews.php
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/header.php';

// Vérifier l'accès admin
AdminController::checkAdminAccess();

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new AdminController();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'toggle_verify') {
        if (!isset($_POST['reviewId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        $csrfToken = $controller->generateCsrfToken();
        if (!hash_equals($csrfToken, $_POST['csrf_token'])) {
            die("Token CSRF invalide");
        }
        $reviewId = (int) $_POST['reviewId'];
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT verified FROM avis WHERE id = ?");
        $stmt->execute([$reviewId]);
        $review = $stmt->fetch();
        if ($review) {
            $newVerifiedStatus = $review['verified'] ? 0 : 1;
            $stmt = $conn->prepare("UPDATE avis SET verified = ? WHERE id = ?");
            $stmt->execute([$newVerifiedStatus, $reviewId]);
        }
        header("Location: admin-reviews.php?success=1");
        exit;
    } elseif ($action === 'delete_review') {
        if (!isset($_POST['reviewId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        $csrfToken = $controller->generateCsrfToken();
        if (!hash_equals($csrfToken, $_POST['csrf_token'])) {
            die("Token CSRF invalide");
        }
        $reviewId = (int) $_POST['reviewId'];
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("DELETE FROM avis WHERE id = ?");
        $stmt->execute([$reviewId]);
        header("Location: admin-reviews.php?success=deleted");
        exit;
    }
}

// Récupère les avis
// ensure page is defined for nav-admin
$page = 'reviews';
$conn = Database::getAdminConnection();
$reviews = $controller->getAllReviews($conn);
$csrfToken = $controller->generateCsrfToken();
$success = $_GET['success'] ?? null;
?>

<link rel="stylesheet" href="/Nomadix/public/css/admin.css">

<main class="admin-dashboard">
    <div class="admin-container">
        <?php require_once __DIR__ . '/nav-admin.php'; ?>

        <!-- Contenu principal -->
        <section class="admin-content">
            <!-- Messages de succès -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✓ <?php
                    switch ($success) {
                        case '1':
                            echo 'Opération effectuée avec succès';
                            break;
                        case 'deleted':
                            echo 'Avis supprimé avec succès';
                            break;
                        default:
                            echo 'Action effectuée';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- Liste des avis -->
            <?php if (!empty($reviews)): ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-info">
                                    <h3><?= htmlspecialchars($review['destinationNom'] ?? 'Destination inconnue') ?></h3>
                                    <p class="review-user">
                                        Par: <strong><?= htmlspecialchars($review['login'] ?? 'Anonyme') ?></strong>
                                        (<?= htmlspecialchars($review['email'] ?? '-') ?>)
                                    </p>
                                </div>
                                <div class="review-rating">
                                    <?= str_repeat('⭐', (int) $review['note']) ?>
                                    <span>(<?= htmlspecialchars($review['note']) ?>/5)</span>
                                </div>
                            </div>

                                <div class="review-destination-image">
                                    <?php if (!empty($review['image'])): ?>
                                        <img src="<?= htmlspecialchars($review['image']) ?>" alt="Destination" class="small-image">
                                    <?php endif; ?>
                                </div>

                            <div class="review-content">
                                <p class="review-text">
                                    <?= htmlspecialchars($review['commentaire'] ?? '(Aucun commentaire)') ?>
                                </p>
                                <p class="review-date">
                                    <?= date('d/m/Y à H:i', strtotime($review['dateAvis'])) ?>
                                </p>
                            </div>

                            <div class="review-actions">
                                <?php if ($review['verified'] == false): ?>
                                    <form method="POST" action="admin.php" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_verify">
                                        <input type="hidden" name="reviewId" value="<?= $review['id'] ?>">
                                        <input type="hidden" name="page" value="reviews">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button type="submit" class="btn-verify">
                                            <?= ($review['verified'] ?? false) ? 'À vérifier' : 'Vérifier' ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="admin.php" style="display:inline;"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis?');">
                                    <input type="hidden" name="action" value="delete_review">
                                    <input type="hidden" name="reviewId" value="<?= $review['id'] ?>">
                                    <input type="hidden" name="page" value="reviews">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <button type="submit" class="btn-delete">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-reviews">
                    <p>Aucun avis pour le moment.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>