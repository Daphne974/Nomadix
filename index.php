<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Router.php';

$router = new Router();
$routes = require __DIR__ . '/config/routes.php';
$routes($router);

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $basePath);
