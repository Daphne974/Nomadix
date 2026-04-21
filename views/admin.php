<?php
// Nomadix/views/admin.php
require_once __DIR__ . '/header.php';
?>

<main>
    <h1>Panneau d'administration</h1>

    <section>
        <h2>Liste des utilisateurs</h2>
        <table>
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
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['admin'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <a href="admin.php?action=toggle_admin&id=<?= $user['id'] ?>">Basculer admin</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
</style>

<?php
require_once __DIR__ . '/footer.php';
?>