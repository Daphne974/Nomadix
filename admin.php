<?php
// Nomadix/admin.php
require_once __DIR__ . '/controllers/AdminController.php';

// Vérifie que l'utilisateur est admin
AdminController::checkAdminAccess();

$controller = new AdminController();
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? null;

// Traiter les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    
    if ($action === 'toggle_admin') {
        $controller->toggleAdmin();
    } elseif ($action === 'delete_user') {
        $controller->deleteUser();
    } elseif ($action === 'delete_review') {
        $controller->deleteReview();
    }
}

// Afficher les pages
switch ($page) {
    case 'users':
        $controller->manageUsers();
        break;
    case 'reviews':
        $controller->manageReviews();
        break;
    case 'dashboard':
    default:
        $controller->showDashboard();
        break;
}
?>
