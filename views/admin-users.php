<?php
// Nomadix/views/admin-users.php
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

    if ($action === 'toggle_admin') {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        $csrfToken = $controller->generateCsrfToken();
        if (!hash_equals($csrfToken, $_POST['csrf_token'])) {
            die("Token CSRF invalide");
        }
        $userId = (int) $_POST['userId'];
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
        header("Location: " . siteUrl('/admin') . "?page=users&success=1");
        exit;
    } elseif ($action === 'delete_user') {
        if (!isset($_POST['userId']) || !isset($_POST['csrf_token'])) {
            die("Paramètres manquants");
        }
        $csrfToken = $controller->generateCsrfToken();
        if (!hash_equals($csrfToken, $_POST['csrf_token'])) {
            die("Token CSRF invalide");
        }
        $userId = (int) $_POST['userId'];
        if ($userId === $_SESSION['user']['id']) {
            die("Vous ne pouvez pas supprimer votre propre compte");
        }
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        header("Location: " . siteUrl('/admin') . "?page=users&success=deleted");
        exit;
    }
}

// Récupère les utilisateurs
$conn = Database::getAdminConnection();
$users = $controller->getUsersByDate($conn, "DESC");
$csrfToken = $controller->generateCsrfToken();
$success = $_GET['success'] ?? null;
$search = trim($_GET['q'] ?? '');
$tri = $_GET['tri'] ?? 'date_desc';
$page = 'users';
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
                    switch($success) {
                        case '1':
                            echo 'Opération effectuée avec succès';
                            break;
                        case 'deleted':
                            echo 'Utilisateur supprimé avec succès';
                            break;
                        default:
                            echo 'Action effectuée';
                    }
                ?>
            </div>
            <?php endif; ?>

            <div class="admin-list-toolbar">
                <h2>Tous les utilisateurs</h2>
                <form method="get" action="<?= htmlspecialchars(siteUrl('/admin')) ?>" class="admin-search-form">
                    <select name="tri" onchange="this.form.submit()">
                        <option value="date_desc" <?= ($tri ?? '') === 'date_desc' ? 'selected' : '' ?>>Date de création (décroissant)</option>
                        <option value="date_asc" <?= ($tri ?? '') === 'date_asc' ? 'selected' : '' ?>>Date de création (croissant)</option>
                        <option value="note_az" <?= ($tri ?? '') === 'note_az' ? 'selected' : '' ?>>Nombre de notes (croissant)</option>
                        <option value="note_za" <?= ($tri ?? '') === 'note_za' ? 'selected' : '' ?>>Nombre de notes (décroissant)</option>
                        <option value="nom_az" <?= ($tri ?? '') === 'nom_az' ? 'selected' : '' ?>>Nom A-Z</option>
                        <option value="nom_za" <?= ($tri ?? '') === 'nom_za' ? 'selected' : '' ?>>Nom Z-A</option>
                    </select>
                    <input type="hidden" name="page" value="users">
                    <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher un utilisateur">
                    <button type="submit" class="btn-small">Rechercher</button>
                    <?php if ($search !== ''): ?>
                        <a href="<?= htmlspecialchars(siteUrl('/admin')) ?>?page=users" class="btn-small2">Effacer</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Avis</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($tri === 'date_asc') {
                            $users = $controller->getUsersByDate($conn, "ASC");
                        } elseif ($tri === 'note_az') {
                            $users = $controller->getUsersByNote($conn, "ASC");
                        } elseif ($tri === 'note_za') {
                            $users = $controller->getUsersByNote($conn, "DESC");
                        } elseif ($tri === 'nom_az') {
                            $users = $controller->getUsersByNom($conn, "ASC");
                        } elseif ($tri === 'nom_za') {
                            $users = $controller->getUsersByNom($conn, "DESC");
                        } else {
                            $users = $controller->getUsersByDate($conn, "DESC");
                        }
                        $usersToShow = $users ?? [];
                        if ($search !== '') {
                            $needle = function_exists('mb_strtolower') ? mb_strtolower($search, 'UTF-8') : strtolower($search);
                            $usersToShow = array_filter($usersToShow, function ($user) use ($needle) {
                                $haystack = implode(' ', array_filter([
                                    $user['login'] ?? '',
                                    $user['email'] ?? '',
                                ]));
                                $haystack = function_exists('mb_strtolower') ? mb_strtolower($haystack, 'UTF-8') : strtolower($haystack);
                                return strpos($haystack, $needle) !== false;
                            });
                        }
                        ?>
                        <?php foreach ($usersToShow as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['nb_avis']) ?></td>
                            <td><?= htmlspecialchars($user['login']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['admin']): ?>
                                    <span class="badge badge-admin">Administrateur</span>
                                <?php else: ?>
                                    <span class="badge badge-user">Utilisateur</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($user['dateCreation'])) ?></td>
                            <td class="actions">
                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_admin">
                                        <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button type="submit" class="btn-toggle">
                                            <?= $user['admin'] ? 'Révoquer admin' : 'Promouvoir admin' ?>
                                        </button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <button type="submit" class="btn-delete">Supprimer</button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-current">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
