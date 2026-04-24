<?php
// Nomadix/views/admin.php
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../models/Database.php';

// Vérifier l'accès admin
AdminController::checkAdminAccess();

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/header.php';

$controller = new AdminController();

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'toggle_verify') {
        if (!isset($_POST['reviewId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        // Vérifier le token CSRF
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
        $page = $_POST['page'] ?? 'dashboard';
        header("Location: admin.php?page=" . urlencode($page) . "&success=1");
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
        $page = $_POST['page'] ?? 'reviews';
        header("Location: admin.php?page=" . urlencode($page) . "&success=deleted");
        exit;
    } elseif ($action === 'toggle_admin') {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        $csrfToken = $controller->generateCsrfToken();
        if (!hash_equals($csrfToken, $_POST['csrf_token'])) {
            die("Token CSRF invalide");
        }
        $userId = (int) $_POST['userId'];
        // Ne pas modifier son propre compte
        if ($userId === $_SESSION['user']['id']) {
            die("Vous ne pouvez pas modifier votre propre statut");
        }
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT admin FROM utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user) {
            $newAdminStatus = $user['admin'] ? 0 : 1;
            $stmt = $conn->prepare("UPDATE utilisateurs SET admin = ? WHERE id = ?");
            $stmt->execute([$newAdminStatus, $userId]);
        }
        header("Location: admin.php?page=users&success=1");
        exit;
    }
}

// Récupère les données pour l'affichage
$page = $_GET['page'] ?? 'dashboard';
$success = $_GET['success'] ?? null;
$conn = Database::getAdminConnection();
$stats = $controller->getStats($conn);
$allReviews = $controller->getAllReviews($conn);
$users = $controller->getUsers($conn);
$csrfToken = $controller->generateCsrfToken();
?>

<link rel="stylesheet" href="/Nomadix/public/css/admin.css">

<main class="admin-dashboard">
    <div class="admin-container">
        <!-- Navigation admin -->
        <aside class="admin-sidebar">
            <h2>Administration</h2>
            <nav>
                <ul>
                    <li><a href="admin.php?page=dashboard"
                            class="nav-link <?= ($page === 'dashboard') ? 'active' : '' ?>"><i
                                class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="admin.php?page=users"
                            class="nav-link <?= ($page === 'users') ? 'active' : '' ?>"><i
                                class="fas fa-users"></i> Utilisateurs</a></li>
                    <li><a href="admin.php?page=reviews"
                            class="nav-link <?= ($page === 'reviews') ? 'active' : '' ?>"><i
                                class="fas fa-star"></i> Avis</a></li>
                    <li><a href="profil.php" class="nav-link"><i class="fas fa-user"></i> Mon profil</a></li>
                    <li><a href="connexion.php?logout=1" class="nav-link logout"><i class="fas fa-sign-out-alt"></i>
                            Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <section class="admin-content">
            <!-- Messages de succès -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <?php
                    switch ($success) {
                        case '1':
                            echo 'Opération effectuée avec succès';
                            break;
                        case 'deleted':
                            echo 'Suppression effectuée avec succès';
                            break;
                        default:
                            echo 'Action effectuée';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($page === 'dashboard'): ?>
                <!-- Dashboard -->
                <h1>Tableau de bord d'administration</h1>

                <!-- Statistiques -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users" style="color: #3498db;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['totalUsers'] ?? 0) ?></h3>
                            <p>Utilisateurs</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-user-shield" style="color: #e74c3c;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['totalAdmins'] ?? 0) ?></h3>
                            <p>Administrateurs</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-star" style="color: #FFD43B;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['totalReviews'] ?? 0) ?></h3>
                            <p>Avis</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-map-marked-alt" style="color: #27ae60;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['totalDestinations'] ?? 0) ?></h3>
                            <p>Destinations</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-chart-bar" style="color: #9b59b6;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['averageRating'] ?? 0) ?>/5</h3>
                            <p>Note moyenne</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-calendar-alt" style="color: #1abc9c;"></i></div>
                        <div class="stat-content">
                            <h3><?= htmlspecialchars($stats['usersThisMonth'] ?? 0) ?></h3>
                            <p>Nouveaux ce mois</p>
                        </div>
                    </div>
                </div>

                <!-- Avis récents -->
                <section class="recent-reviews">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2><i class="fas fa-star"></i> Avis récents non vérifiés</h2>
                        <a href="admin.php?page=reviews" class="btn-small">Voir tous les avis</a>
                    </div>
                    <?php 
                    $unverifiedReviews = array_filter($allReviews ?? [], function ($review) {
                        return !($review['verified'] ?? false);
                    });
                    $recentUnverified = array_slice($unverifiedReviews, 0, 5);
                    ?>
                    <?php if (!empty($recentUnverified)): ?>
                        <div class="reviews-list">
                            <?php foreach ($recentUnverified as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <strong><?= htmlspecialchars($review['login'] ?? 'Anonyme') ?></strong>
                                        <span class="review-rating">
                                            <?= str_repeat('<i class="fas fa-star" style="color: #FFD43B;"></i>', $review['note'] ?? 0) ?>
                                        </span>
                                    </div>
                                    <div class="review-destination">
                                        Destination: <strong><?= htmlspecialchars($review['destinationNom'] ?? 'N/A') ?></strong>
                                    </div>
                                    <p class="review-text">
                                        <?= htmlspecialchars(substr($review['commentaire'] ?? '', 0, 100)) ?>...
                                    </p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                        <small class="review-date">
                                            <?= date('d/m/Y H:i', strtotime($review['dateAvis'] ?? 'now')) ?>
                                        </small>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <span style="color: <?= ($review['verified'] ?? false) ? '#27ae60' : '#e74c3c' ?>">
                                                <?= ($review['verified'] ?? false) ? '<i class="fas fa-check-circle"></i> Vérifié' : '<i class="fas fa-times-circle"></i> Non vérifié' ?>
                                            </span>
                                            <form method="POST" action="admin.php" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_verify">
                                                <input type="hidden" name="reviewId" value="<?= htmlspecialchars($review['id']) ?>">
                                                <input type="hidden" name="page" value="dashboard">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" class="btn-small">
                                                    <?= ($review['verified'] ?? false) ? 'Non vérifié' : 'Vérifié' ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Aucun avis non vérifié pour le moment.</p>
                    <?php endif; ?>
                </section>

                <!-- Utilisateurs récents -->
                <section class="recent-users">
                    <h2><i class="fas fa-users"></i> Utilisateurs récents</h2>
                    <p>Total: <strong><?= htmlspecialchars(count($users) ?? 0) ?></strong> utilisateurs</p>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Admin</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($users ?? [], 0, 5) as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($user['login'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                    <td>
                                        <?= ($user['admin'] ?? false) ? '<i class="fas fa-check-circle" style="color: #27ae60;"></i>' : '<i class="fas fa-times-circle" style="color: #e74c3c;"></i>' ?>
                                    </td>
                                    <td>
                                        <a href="admin.php?page=users" class="btn-small">Voir tout</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

            <?php elseif ($page === 'reviews'): ?>
                <!-- Page dédiée aux avis -->
                <section class="all-reviews">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2><i class="fas fa-star"></i>
                            <?= isset($_GET['show_all']) ? 'Tous les avis' : 'Avis non vérifiés' ?></h2>
                        <?php if (!isset($_GET['show_all'])): ?>
                            <a href="admin.php?page=reviews&show_all=1" class="btn-small">Voir tous les avis</a>
                        <?php else: ?>
                            <a href="admin.php?page=reviews" class="btn-small">Voir uniquement les non vérifiés</a>
                        <?php endif; ?>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Destination</th>
                                <th>Note</th>
                                <th>Vérifié</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $showAll = isset($_GET['show_all']);
                            if ($showAll) {
                                $reviewsToShow = $allReviews ?? [];
                            } else {
                                $reviewsToShow = array_filter($allReviews ?? [], function ($review) {
                                    return !($review['verified'] ?? false);
                                });
                            }
                            ?>
                            <?php if (!empty($reviewsToShow)): ?>
                                <?php foreach ($reviewsToShow as $review): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($review['id'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($review['login'] ?? 'Anonyme') ?></td>
                                        <td><?= htmlspecialchars($review['destinationNom'] ?? 'N/A') ?></td>
                                        <td>
                                            <?= str_repeat('<i class="fas fa-star" style="color: #FFD43B; font-size: 0.9em;"></i>', $review['note'] ?? 0) ?>
                                        </td>
                                        <td>
                                            <span style="color: <?= ($review['verified'] ?? false) ? '#27ae60' : '#e74c3c' ?>">
                                                <?= ($review['verified'] ?? false) ? '<i class="fas fa-check-circle"></i> Oui' : '<i class="fas fa-times-circle"></i> Non' ?>
                                            </span>
                                        </td>
                                        <td style="display: flex; gap: 5px;">
                                            <form method="POST" action="admin.php" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_verify">
                                                <input type="hidden" name="reviewId" value="<?= htmlspecialchars($review['id']) ?>">
                                                <input type="hidden" name="page" value="reviews">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" class="btn-small">
                                                    <?= ($review['verified'] ?? false) ? 'Non vérifié' : 'Vérifié' ?>
                                                </button>
                                            </form>
                                            <form method="POST" action="admin.php" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_review">
                                                <input type="hidden" name="reviewId" value="<?= htmlspecialchars($review['id']) ?>">
                                                <input type="hidden" name="page" value="reviews">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <button type="submit" class="btn-small" style="background-color: #e74c3c;"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">Aucun avis à afficher</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

            <?php elseif ($page === 'users'): ?>
                <!-- Page dédiée aux utilisateurs -->
                <section class="all-users">
                    <h2><i class="fas fa-users"></i> Tous les utilisateurs</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Admin</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($user['login'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                        <td>
                                            <?= ($user['admin'] ?? false) ? '<i class="fas fa-check-circle" style="color: #27ae60;"></i>' : '<i class="fas fa-times-circle" style="color: #e74c3c;"></i>' ?>
                                        </td>
                                        <td>
                                            <?php if ($user['id'] !== ($_SESSION['user']['id'] ?? null)): ?>
                                                <form method="POST" action="admin.php" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle_admin">
                                                    <input type="hidden" name="userId" value="<?= htmlspecialchars($user['id']) ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                    <button type="submit" class="btn-small">
                                                        <?= ($user['admin'] ?? false) ? 'Retirer admin' : 'Rendre admin' ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: #666;">(Vous)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px;">Aucun utilisateur</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>