<?php
// Nomadix/views/admin-users.php
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/header.php';

$controller = new AdminController();
$csrfToken = $controller->generateCsrfToken();
?>

<main class="admin-dashboard">
    <div class="admin-container">
        <!-- Navigation admin -->
        <aside class="admin-sidebar">
            <h2>Administration</h2>
            <nav>
                <ul>
                    <li><a href="admin.php?page=dashboard" class="nav-link">📊 Dashboard</a></li>
                    <li><a href="admin.php?page=users" class="nav-link active">👥 Utilisateurs</a></li>
                    <li><a href="admin.php?page=reviews" class="nav-link">⭐ Avis</a></li>
                    <li><a href="profil.php" class="nav-link">👤 Mon profil</a></li>
                    <li><a href="connexion.php?logout=1" class="nav-link logout">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <section class="admin-content">
            <h1>Gestion des utilisateurs</h1>

            <!-- Messages de succès -->
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✓ Opération effectuée avec succès
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

<style>
.admin-dashboard {
    display: flex;
    min-height: calc(100vh - 120px);
    background-color: #f5f5f5;
}

.admin-container {
    display: flex;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
}

.admin-sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.admin-sidebar h2 {
    padding: 0 20px 20px;
    border-bottom: 2px solid #34495e;
    margin-bottom: 20px;
}

.admin-sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.admin-sidebar .nav-link {
    display: block;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    transition: background-color 0.3s;
}

.admin-sidebar .nav-link:hover,
.admin-sidebar .nav-link.active {
    background-color: #3498db;
}

.admin-sidebar .nav-link.logout {
    color: #e74c3c;
    margin-top: 20px;
    border-top: 1px solid #34495e;
}

.admin-content {
    flex: 1;
    padding: 30px;
}

.admin-content h1 {
    margin-bottom: 30px;
    color: #2c3e50;
}

.alert {
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.table-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.admin-table th {
    background-color: #ecf0f1;
    padding: 15px;
    text-align: left;
    font-weight: bold;
    color: #2c3e50;
    border-bottom: 2px solid #bdc3c7;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #ecf0f1;
}

.admin-table tr:hover {
    background-color: #f9f9f9;
}

.badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
}

.badge-admin {
    background-color: #3498db;
    color: white;
}

.badge-user {
    background-color: #95a5a6;
    color: white;
}

.badge-current {
    background-color: #2ecc71;
    color: white;
}

.actions {
    text-align: center;
}

.btn-toggle,
.btn-delete {
    padding: 8px 12px;
    margin: 0 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
    transition: all 0.3s;
}

.btn-toggle {
    background-color: #3498db;
    color: white;
}

.btn-toggle:hover {
    background-color: #2980b9;
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
}

.btn-delete:hover {
    background-color: #c0392b;
}

@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        width: 100%;
    }
    
    .admin-table {
        font-size: 0.9em;
    }
    
    .admin-table td,
    .admin-table th {
        padding: 10px;
    }
    
    .btn-toggle,
    .btn-delete {
        padding: 6px 8px;
        margin: 2px;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
