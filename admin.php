<?php
// Nomadix/admin.php
require_once __DIR__ . '/controllers/AdminController.php';

// Vérifie que l'utilisateur est admin
AdminController::checkAdminAccess();

$controller = new AdminController();
$controller->handleAdmin();
?>
