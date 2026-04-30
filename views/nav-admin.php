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
                    <li><a href="admin.php?page=destinations"
                            class="nav-link <?= ($page === 'destinations') ? 'active' : '' ?>"><i
                                class="fas fa-map-marker-alt"></i> Destinations</a></li>
                    <li><a href="admin.php?page=reviews"
                            class="nav-link <?= ($page === 'reviews') ? 'active' : '' ?>"><i
                                class="fas fa-star"></i> Avis</a></li>
                    <li><a href="profil.php" class="nav-link"><i class="fas fa-user"></i> Mon profil</a></li>
                    <li><a href="connexion.php?logout=1" class="nav-link logout"><i class="fas fa-sign-out-alt"></i>
                            Déconnexion</a></li>
                </ul>
            </nav>
        </aside>