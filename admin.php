<?php
// Nomadix/admin.php
require_once __DIR__ . '/controllers/AdminController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['is_admin']) || !$_SESSION['user']['is_admin']) {
    header("HTTP/1.0 403 Forbidden");
    die("Accès refusé. Réservé aux administrateurs.");
}

// Nomadix/admin.php
if (isset($_GET['action']) && $_GET['action'] === 'toggle_admin') {
    $controller->toggleAdmin();
    exit;
}

$controller = new AdminController();
$controller->showAdminPanel();
?>