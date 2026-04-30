<?php
// Nomadix/views/profil.php
// Variables disponibles: $user
?>

<main>
    <div class="forms">
        <h2>Profil de <?= htmlspecialchars($user['login'] ?? 'Utilisateur') ?></h2>
        
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
    </div>
</main>
