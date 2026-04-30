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
        header("Location: admin-users.php?success=1");
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
        header("Location: admin-users.php?success=deleted");
        exit;
    }
}

// Récupère les utilisateurs
$conn = Database::getAdminConnection();
$users = $controller->getUsers($conn);
$csrfToken = $controller->generateCsrfToken();
$success = $_GET['success'] ?? null;
?>

<link rel="stylesheet" href="/Nomadix/public/css/admin.css">

<main class="admin-dashboard">
    <div class="admin-container">
        <?php require_once __DIR__ . '/nav-admin.php'; ?>

        <!-- Contenu principal -->
        <section class="admin-content">
            <h1>Gestion des utilisateurs</h1>

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

            <!-- Tableau des utilisateurs -->
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
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
