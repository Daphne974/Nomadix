<?php
// Nomadix/connexion.php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

$controller = new AuthController();
$controller->login();
?>
