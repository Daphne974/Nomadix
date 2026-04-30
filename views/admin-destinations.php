<?php
// Nomadix/views/admin-destinations.php
require_once __DIR__ . '/header.php';

// $destinations, $csrfToken, $success expected
?>

<link rel="stylesheet" href="/Nomadix/public/css/admin.css">

<main class="admin-dashboard">
    <div class="admin-container">
        <?php require_once __DIR__ . '/nav-admin.php'; ?>

        <section class="admin-content">
            <h1>Gestion des destinations</h1>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">✓ <?php echo ($success === 'deleted') ? 'Destination supprimée' : 'Action effectuée'; ?></div>
            <?php endif; ?>

            <!-- Formulaire ajout / édition -->
            <div class="destination-form">
                <h2><?= isset($editDestination) && $editDestination ? 'Modifier la destination' : 'Ajouter une destination' ?></h2>
                <form method="POST" action="admin.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <?php if (isset($editDestination) && $editDestination): ?>
                        <input type="hidden" name="action" value="update_destination">
                        <input type="hidden" name="destinationId" value="<?= htmlspecialchars($editDestination['id']) ?>">
                    <?php else: ?>
                        <input type="hidden" name="action" value="create_destination">
                    <?php endif; ?>

                    <div class="destination-form-grid">
                        <div class="form-left">
                            <div class="form-row">
                                <label>Nom</label>
                                <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($editDestination['nom'] ?? '') ?>" required>
                            </div>
                            <div class="form-row" style="margin-top:8px;">
                                <label>Pays</label>
                                <input type="text" name="pays" placeholder="Pays" value="<?= htmlspecialchars($editDestination['pays'] ?? '') ?>" required>
                                <label>Ville</label>
                                <input type="text" name="ville" placeholder="Ville" value="<?= htmlspecialchars($editDestination['ville'] ?? '') ?>">
                            </div>
                            <div style="margin-top:10px;">
                                <label>Description</label>
                                <textarea name="description" placeholder="Description" rows="4"><?= htmlspecialchars($editDestination['description'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="form-right">
                            <div>
                                <label>Image de couverture</label>
                                <input id="image-url-input" type="text" name="image_url" placeholder="Image URL" value="<?= htmlspecialchars($editDestination['image'] ?? '') ?>">
                                <div class="image-preview" style="margin-top:8px;">
                                    <img id="image-url-preview" src="<?= htmlspecialchars($editDestination['image'] ?? '') ?>" alt="Aperçu URL" class="small-image" <?= empty($editDestination['image']) ? 'style="display:none;"' : '' ?>>
                                    <div id="image-url-error" style="color:#e74c3c;font-size:0.9em;display:none;">URL invalide</div>
                                </div>
                            </div>
                            <div style="margin-top:10px;">
                                <label>Téléverser une image de fond</label>
                                <input id="image-file-input" type="file" name="image_file" accept="image/*">
                                <div class="image-preview" style="margin-top:8px;">
                                    <img id="image-file-preview" src="" alt="Aperçu upload" class="small-image" style="display:none;">
                                </div>
                            </div>

                            <?php if (!empty($editDestination['image'])): ?>
                                <div class="image-preview" id="existing-image-block">
                                    <label>Aperçu actuel</label>
                                    <div>
                                        <img id="existing-image-preview" src="<?= htmlspecialchars($editDestination['image']) ?>" alt="Aperçu" class="small-image">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-actions">
                                <button type="submit" class="btn-small"><?= isset($editDestination) && $editDestination ? 'Enregistrer' : 'Ajouter' ?></button>
                                <?php if (isset($editDestination) && $editDestination): ?>
                                    <a href="admin.php?page=destinations" class="btn-cancel">Annuler</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Pays</th>
                            <th>Ville</th>
                            <th>Image de fond</th>
                            <th>Image de couverture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($destinations)): ?>
                            <?php foreach ($destinations as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['id']) ?></td>
                                    <td><?= htmlspecialchars($d['nom']) ?></td>
                                    <td><?= htmlspecialchars($d['pays']) ?></td>
                                    <td><?= htmlspecialchars($d['ville'] ?? '') ?></td>
                                    <td><?php if (!empty($d['image'])): ?><img src="<?= htmlspecialchars($d['image']) ?>" alt="" style="max-width:80px;"><?php endif; ?></td>
                                    <td><img src="public/images/<?php echo normalizeString($d['ville']); ?>.jpg" alt="<?= htmlspecialchars($d['ville']) ?>" style="max-width:80px;"></td>
                                    <td class="actions">
                                        <a href="admin.php?page=destinations&edit=<?= htmlspecialchars($d['id']) ?>" class="btn-small">Modifier</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette destination et ses avis ?');">
                                            <input type="hidden" name="action" value="delete_destination">
                                            <input type="hidden" name="destinationId" value="<?= htmlspecialchars($d['id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit" class="btn-delete">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;padding:20px;">Aucune destination</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>

<script>
// Preview image from URL input
(function(){
    const input = document.getElementById('image-url-input');
    const preview = document.getElementById('image-url-preview');
    const err = document.getElementById('image-url-error');
    const filePreview = document.getElementById('image-file-preview');
    const existingBlock = document.getElementById('existing-image-block');
    if (!input || !preview) return;

    function showUrl(url) {
        if (!url) {
            preview.style.display = 'none';
            err.style.display = 'none';
            preview.src = '';
            // restore file preview hidden state if no url and no file
            return;
        }
        preview.style.display = 'inline-block';
        err.style.display = 'none';
        preview.src = url;
    }

    preview.addEventListener('error', function(){
        preview.style.display = 'none';
        err.style.display = 'block';
    });

    preview.addEventListener('load', function(){
        err.style.display = 'none';
        preview.style.display = 'inline-block';
    });

    input.addEventListener('input', function(){ showUrl(input.value.trim()); });
})();

// Preview selected file and prioritize it over URL/existing preview
(function(){
    const fileIn = document.getElementById('image-file-input');
    const filePrev = document.getElementById('image-file-preview');
    const urlPrev = document.getElementById('image-url-preview');
    const urlErr = document.getElementById('image-url-error');
    const existing = document.getElementById('existing-image-block');
    if (!fileIn || !filePrev) return;

    fileIn.addEventListener('change', function(){
        const f = fileIn.files && fileIn.files[0];
        if (!f) {
            filePrev.style.display = 'none';
            filePrev.src = '';
            // restore url or existing preview
            const urlVal = document.getElementById('image-url-input')?.value || '';
            if (urlVal.trim()) {
                if (urlPrev) { urlPrev.src = urlVal; urlPrev.style.display = 'inline-block'; urlErr.style.display = 'none'; }
            } else if (existing) {
                existing.style.display = 'block';
            }
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e){
            filePrev.src = e.target.result;
            filePrev.style.display = 'inline-block';
            // when a file is selected, hide the existing image preview (upload takes precedence)
            if (existing) existing.style.display = 'none';
            if (urlErr) urlErr.style.display = 'none';
        };
        reader.readAsDataURL(f);
    });
})();
</script>
