<?php
// Nomadix/views/profil.php
// Variables disponibles: $user
?>

<main>
    <div class="profil-wrapper">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash <?= htmlspecialchars($_SESSION['flash_message_class'] ?? '') ?>">
                <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_message_class']); ?>
        <?php endif; ?>

        <div class="profil-card">
            <div class="profil-top">
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
                <div class="profil-info">
                    <p><strong>Login :</strong> <?= htmlspecialchars($user['login'] ?? 'N/A') ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
                    <?php if (isset($user['dateCreation'])): ?>
                        <p><strong>Compte créé le :</strong> <?= date('d/m/Y H:i', strtotime($user['dateCreation'])) ?></p>
                    <?php endif; ?>
                    <?php if (isset($user['admin']) && $user['admin']): ?>
                        <p><strong>Statut :</strong> <span style="color: #ff0000;">Administrateur</span></p>
                    <?php else: ?>
                        <p><strong>Statut :</strong> <span>Utilisateur</span></p>
                    <?php endif; ?>
                </div>

                <div class="profil-forms">
                    <div>
                        <h3>Choisir un avatar</h3>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            <?php
                            $avatarDir = __DIR__ . '/../public/profil';
                            if (is_dir($avatarDir)) {
                                $files = array_values(array_filter(scandir($avatarDir), function($f) use ($avatarDir) {
                                    return is_file($avatarDir . '/' . $f) && preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f);
                                }));
                                foreach ($files as $f):
                                    $path = 'public/profil/' . $f;
                                    $avatarUrl = siteUrl('/' . $path);
                            ?>
                                <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>" style="margin:0;">
                                    <input type="hidden" name="action" value="update_avatar">
                                    <input type="hidden" name="avatar" value="<?= htmlspecialchars($path) ?>">
                                    <button type="submit" style="border:0;background:none;padding:0;cursor:pointer;">
                                        <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" style="width:60px;height:60px;border-radius:8px;object-fit:cover;border:2px solid #eee;">
                                    </button>
                                </form>
                            <?php endforeach; }
                            ?>
                        </div>
                    </div>
                    <div class="forms-grid">
                        <div>
                            <h3>Changer l'e-mail</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>">
                                <input type="hidden" name="action" value="update_email">
                                <label>Nouveau e-mail:<br><input type="email" name="email" required></label>
                                <label>Mot de passe actuel:<br><input type="password" name="currentPassword" required></label>
                                <button type="submit">Mettre à jour l'e-mail</button>
                            </form>
                        </div>

                        <div>
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
                        <div>
                            <h3>Changer le pseudo (1 fois tous les 4 mois)</h3>
                            <form method="post" action="<?= htmlspecialchars(siteUrl('/profil')) ?>">
                                <input type="hidden" name="action" value="update_login">
                                <label>Nouveau pseudo:<br><input type="text" name="login" required></label>
                                <button type="submit">Mettre à jour le pseudo</button>
                            </form>
                        </div>

                        <div>
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
