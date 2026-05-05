<?php

return function (Router $router): void {
    $router->match(['GET', 'POST'], '/', function (): void {
        require_once __DIR__ . '/../controllers/HomeController.php';
        (new HomeController())->index();
    });

    $router->match(['GET', 'POST'], '/connexion', function (): void {
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->login();
    });

    $router->match(['GET', 'POST'], '/inscription', function (): void {
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->register();
    });

    $router->match(['GET', 'POST'], '/destination', function (): void {
        require_once __DIR__ . '/../controllers/DestinationController.php';
        (new DestinationController())->showDestination();
    });

    $router->match(['GET', 'POST'], '/profil', function (): void {
        require_once __DIR__ . '/../controllers/HomeController.php';
        (new HomeController())->showProfile();
    });

    $router->match(['GET', 'POST'], '/admin', function (): void {
        require_once __DIR__ . '/../controllers/AdminController.php';
        AdminController::checkAdminAccess();
        (new AdminController())->handleAdmin();
    });

    $router->match(['GET', 'POST'], '/logout', function (): void {
        require __DIR__ . '/../logout.php';
    });

    $router->get('/403', function (): void {
        http_response_code(403);
        require __DIR__ . '/../403.php';
    });

    $router->get('/404', function (): void {
        http_response_code(404);
        require __DIR__ . '/../404.php';
    });
};
