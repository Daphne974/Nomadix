<?php
// Nomadix/views/profil.php
// Variables disponibles: $user
?>

<main class="profil-page">
    <div class="profil-wrapper">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash <?= htmlspecialchars($_SESSION['flash_message_class'] ?? '') ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_message_class']); ?>
        <?php endif; ?>

        <div class="profil-card">
            <div class="profil-top profil-hero">
                <div class="profil-avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= htmlspecialchars(siteUrl('/' . $user['avatar'])) ?>" alt="Avatar" style="width:86px;height:86px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <?= htmlspecialchars(strtoupper(substr($user['login'] ?? 'U', 0, 2))) ?>
                    <?php endif; ?>
                </div>
                <div class="profil-meta">
                    <div class="name"><?= htmlspecialchars($user['login'] ?? 'Utilisateur') ?></div>
                    <div class="small"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                </div>
            </div>

            <div class="profil-body">
                <div class="profil-info-panel">
                    <div class="profil-info-card">
                        <span>Login</span>
                        <strong><?= htmlspecialchars($user['login'] ?? 'N/A') ?></strong>
                    </div>
                    <div class="profil-info-card">
                        <span>Email</span>
                        <strong><?= htmlspecialchars($user['email'] ?? 'N/A') ?></strong>
                    </div>
                    <?php if (isset($user['dateCreation'])): ?>
                        <div class="profil-info-card">
                            <span>Compte créé le</span>
                            <strong><?= date('d/m/Y H:i', strtotime($user['dateCreation'])) ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="profil-info-card">
                        <span>Statut</span>
                        <?php if (isset($user['admin']) && $user['admin']): ?>
                            <strong class="profil-role admin">Administrateur</strong>
                        <?php else: ?>
                            <strong class="profil-role user">Utilisateur</strong>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profil-forms">
                    <div class="profil-panel">
                        <h3>Choisir un avatar</h3>
                        <div class="avatar-grid">
                            <?php
                            $avatarDir = __DIR__ . '/../public/profil';
                            if (is_dir($avatarDir)) {
                                $files = array_values(array_filter(scandir($avatarDir), function ($f) use ($avatarDir) {
                                    return is_file($avatarDir . '/' . $f) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f);
                                }));
                                foreach ($files as $f):
                                    $path = 'public/profil/' . $f;
                                    $avatarUrl = siteUrl('/' . $path);
                            ?>
                                <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>" style="margin:0;">
                                    <input type="hidden" name="action" value="update_avatar">
                                    <input type="hidden" name="avatar" value="<?= htmlspecialchars($path) ?>">
                                    <button type="submit" class="avatar-choice">
                                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" style="width:60px;height:60px;border-radius:12px;object-fit:cover;">
                                    </button>
                                </form>
                            <?php endforeach; }
                            ?>
                        </div>
                    </div>

                    <div class="forms-grid">
                        <div class="profil-panel">
                            <h3>Changer l'e-mail</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>">
                                <input type="hidden" name="action" value="update_email">
                                <label>Nouveau e-mail:<br><input type="email" name="email" required></label>
                                <label>Mot de passe actuel:<br><input type="password" name="currentPassword" required></label>
                                <button type="submit">Mettre à jour l'e-mail</button>
                            </form>
                        </div>

                        <div class="profil-panel">
                            <h3>Changer le mot de passe</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>">
                                <input type="hidden" name="action" value="update_password">
                                <label>Mot de passe actuel:<br><input type="password" name="currentPassword" required></label>
                                <label>Nouveau mot de passe:<br><input type="password" name="newPassword" required></label>
                                <label>Confirmer nouveau mot de passe:<br><input type="password" name="confirmNewPassword" required></label>
                                <small>Au moins 12 caractères, une majuscule, une minuscule, un chiffre et un symbole.</small>
                                <button type="submit">Mettre à jour le mot de passe</button>
                            </form>
                        </div>
                    </div>

                    <div class="forms-grid">
                        <div class="profil-panel">
                            <h3>Changer le pseudo (1 fois tous les 4 mois)</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>">
                                <input type="hidden" name="action" value="update_login">
                                <label>Nouveau pseudo:<br><input type="text" name="login" required></label>
                                <button type="submit">Mettre à jour le pseudo</button>
                            </form>
                        </div>

                        <div class="profil-panel danger-panel">
                            <h3>Supprimer mon compte</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>" onsubmit="return confirm('Cette action est irreversible. Confirmer la suppression de votre compte ?');">
                                <input type="hidden" name="action" value="delete_account">
                                <label>Mot de passe actuel:<br><input type="password" name="currentPassword" required></label>
                                <button type="submit" class="danger">Supprimer mon compte</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
