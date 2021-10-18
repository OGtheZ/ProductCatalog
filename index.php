<?php

use App\Views\View;

require_once "vendor/autoload.php";

session_start();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/products', 'App\Controllers\ProductsController@list');
    $r->addRoute('GET', '/products/create', 'App\Controllers\ProductsController@addForm');
    $r->addRoute('POST', '/products', 'App\Controllers\ProductsController@store');
    $r->addRoute('GET', '/products/{id}/edit', 'App\Controllers\ProductsController@editForm');
    $r->addRoute('POST', '/products/{id}/edit', 'App\Controllers\ProductsController@edit');
    $r->addRoute('GET', '/products/{id}/remove', 'App\Controllers\ProductsController@removeConfirmation');
    $r->addRoute('POST', '/products/{id}/remove', 'App\Controllers\ProductsController@remove');
    $r->addRoute('POST', '/products/category', 'App\Controllers\ProductsController@searchByCategory');

    $r->addRoute('GET', '/', 'App\Controllers\UsersController@login');
    $r->addRoute('GET', '/register', 'App\Controllers\UsersController@showRegisterForm');
    $r->addRoute('POST', '/register', 'App\Controllers\UsersController@register');
    $r->addRoute('POST', '/', 'App\Controllers\UsersController@authorize');
    $r->addRoute('POST', '/logout', 'App\Controllers\UsersController@logout');


});

$loader = new \Twig\Loader\FilesystemLoader('app/Views');
$templateLoader = new \Twig\Environment($loader, []);
$templateLoader->addGlobal('id', $_SESSION['id']);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = explode("@", $handler);
        $controller = new $controller;
        $response = $controller->$method($vars);

        if ($response instanceof View)
        {
            echo $templateLoader->render($response->getTemplate(), $response->getVariables());
        }
        break;
}
unset($_SESSION['errors']);