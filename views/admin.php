<?php
// Nomadix/views/admin.php
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
                    <li><a href="admin.php?page=dashboard" class="nav-link active">📊 Dashboard</a></li>
                    <li><a href="admin.php?page=users" class="nav-link">👥 Utilisateurs</a></li>
                    <li><a href="admin.php?page=reviews" class="nav-link">⭐ Avis</a></li>
                    <li><a href="profil.php" class="nav-link">👤 Mon profil</a></li>
                    <li><a href="connexion.php?logout=1" class="nav-link logout">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <section class="admin-content">
            <h1>Tableau de bord d'administration</h1>

            <!-- Messages de succès -->
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✓ Opération effectuée avec succès
            </div>
            <?php endif; ?>

            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['totalUsers']) ?></h3>
                        <p>Utilisateurs</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🔐</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['totalAdmins']) ?></h3>
                        <p>Administrateurs</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['totalReviews']) ?></h3>
                        <p>Avis</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">🗺️</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['totalDestinations']) ?></h3>
                        <p>Destinations</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['averageRating']) ?>/5</h3>
                        <p>Note moyenne</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <h3><?= htmlspecialchars($stats['usersThisMonth']) ?></h3>
                        <p>Nouveaux ce mois</p>
                    </div>
                </div>
            </div>

            <!-- Avis récents -->
            <section class="recent-reviews">
                <h2>⭐ Avis récents</h2>
                <?php if (!empty($recentReviews)): ?>
                <div class="reviews-list">
                    <?php foreach ($recentReviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <strong><?= htmlspecialchars($review['login'] ?? 'Anonyme') ?></strong>
                            <span class="review-rating"><?= str_repeat('⭐', $review['note']) ?></span>
                        </div>
                        <div class="review-destination">
                            Destination: <strong><?= htmlspecialchars($review['destinationNom'] ?? 'N/A') ?></strong>
                        </div>
                        <p class="review-text"><?= htmlspecialchars(substr($review['commentaire'] ?? '', 0, 100)) ?>...</p>
                        <small class="review-date"><?= date('d/m/Y H:i', strtotime($review['dateAvis'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p>Aucun avis pour le moment.</p>
                <?php endif; ?>
            </section>

            <!-- Utilisateurs récents -->
            <section class="recent-users">
                <h2>👥 Utilisateurs</h2>
                <p>Total: <strong><?= count($users) ?></strong> utilisateurs</p>
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
                        <?php foreach (array_slice($users, 0, 5) as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['login']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['admin'] ? '✓' : '✗' ?></td>
                            <td>
                                <a href="admin.php?page=users" class="btn-small">Voir tout</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
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

.admin-sidebar nav li {
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

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-icon {
    font-size: 2.5em;
    min-width: 60px;
}

.stat-content h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.8em;
}

.stat-content p {
    margin: 5px 0 0;
    color: #7f8c8d;
}

.recent-reviews,
.recent-users {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.recent-reviews h2,
.recent-users h2 {
    margin-top: 0;
    color: #2c3e50;
}

.reviews-list {
    display: grid;
    gap: 15px;
}

.review-item {
    padding: 15px;
    background-color: #f9f9f9;
    border-left: 4px solid #3498db;
    border-radius: 4px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.review-rating {
    color: #f39c12;
}

.review-destination {
    font-size: 0.9em;
    color: #7f8c8d;
    margin: 5px 0;
}

.review-text {
    margin: 8px 0;
    color: #333;
}

.review-date {
    color: #95a5a6;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.admin-table th {
    background-color: #ecf0f1;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    color: #2c3e50;
    border-bottom: 2px solid #bdc3c7;
}

.admin-table td {
    padding: 12px;
    border-bottom: 1px solid #ecf0f1;
}

.admin-table tr:hover {
    background-color: #f9f9f9;
}

.btn-small {
    padding: 6px 12px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.85em;
    transition: background-color 0.3s;
}

.btn-small:hover {
    background-color: #2980b9;
}

@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
