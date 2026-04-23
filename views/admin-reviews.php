<?php
// Nomadix/views/admin-reviews.php
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
                    <li><a href="admin.php?page=users" class="nav-link">👥 Utilisateurs</a></li>
                    <li><a href="admin.php?page=reviews" class="nav-link active">⭐ Avis</a></li>
                    <li><a href="profil.php" class="nav-link">👤 Mon profil</a></li>
                    <li><a href="connexion.php?logout=1" class="nav-link logout">🚪 Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <section class="admin-content">
            <h1>Gestion des avis</h1>

            <!-- Messages de succès -->
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                ✓ Avis supprimé avec succès
            </div>
            <?php endif; ?>

            <!-- Liste des avis -->
            <div class="reviews-container">
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
                                    <?= str_repeat('⭐', (int)$review['note']) ?>
                                    <span>(<?= htmlspecialchars($review['note']) ?>/5)</span>
                                </div>
                            </div>

                            <div class="review-destination-image">
                                <?php if ($review['image']): ?>
                                    <img src="<?= htmlspecialchars($review['image']) ?>" alt="Destination" class="small-image">
                                <?php endif; ?>
                            </div>

                            <div class="review-content">
                                <p class="review-text">
                                    <?= htmlspecialchars($review['commentaire'] ?? '(Aucun commentaire)') ?>
                                </p>
                                <p class="review-date">
                                    📅 <?= date('d/m/Y à H:i', strtotime($review['dateAvis'])) ?>
                                </p>
                            </div>

                            <div class="review-actions">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis?');">
                                    <input type="hidden" name="action" value="delete_review">
                                    <input type="hidden" name="reviewId" value="<?= $review['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <button type="submit" class="btn-delete">🗑️ Supprimer</button>
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

.reviews-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 20px;
}

.reviews-list {
    display: grid;
    gap: 20px;
}

.review-card {
    border: 1px solid #ecf0f1;
    border-radius: 8px;
    padding: 20px;
    background-color: #f9f9f9;
    transition: all 0.3s;
}

.review-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    background-color: white;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.review-info h3 {
    margin: 0 0 10px;
    color: #2c3e50;
}

.review-user {
    margin: 0;
    color: #7f8c8d;
    font-size: 0.9em;
}

.review-rating {
    text-align: right;
    font-size: 1.2em;
}

.review-rating span {
    display: block;
    color: #f39c12;
    font-weight: bold;
    margin-top: 5px;
}

.review-destination-image {
    margin-bottom: 15px;
}

.small-image {
    max-width: 150px;
    max-height: 100px;
    border-radius: 4px;
}

.review-content {
    margin-bottom: 15px;
}

.review-text {
    margin: 0 0 10px;
    color: #333;
    line-height: 1.5;
    padding: 10px;
    background-color: white;
    border-left: 3px solid #3498db;
    border-radius: 2px;
}

.review-date {
    margin: 0;
    color: #95a5a6;
    font-size: 0.85em;
}

.review-actions {
    text-align: right;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}

.btn-delete {
    padding: 10px 15px;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-delete:hover {
    background-color: #c0392b;
}

.no-reviews {
    text-align: center;
    padding: 40px 20px;
    color: #7f8c8d;
}

@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        width: 100%;
    }
    
    .review-header {
        flex-direction: column;
    }
    
    .review-rating {
        text-align: left;
        margin-top: 10px;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
