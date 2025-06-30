<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$container = require_once __DIR__ . '/../app/Config/bootstrap.php';

$entityManager = $container['entityManager'];
$twig = $container['twig'];
$omdbService = $container['omdbService'];

$pageController = new App\Controllers\PageController($entityManager, $twig);
$omdbApiController = new App\Controllers\OmdbApiController($omdbService);
$ratedController = new App\Controllers\RatedController($entityManager);

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = strtok($requestUri, '?');

switch ($requestPath) {
    case '/':
        $pageController->indexAction();
        break;
    
    case '/api/omdb/search':
        $omdbApiController->searchAction();
        break;
    
    case '/api/omdb/details':
        $omdbApiController->detailsAction();
        break;
    
    case '/api/rated-movies/create':
        $ratedController->createAction();
        break;

    case '/api/rated-movies/update':
        $ratedController->updateAction();
        break;

    case '/api/rated-movies/restore':
        $ratedController->restoreAction();
        break;
    
    case '/api/rated-movies/delete':
        $ratedController->deleteAction();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Página Não Encontrada</h1>";
        break;
}