<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use App\Services\OmdbApiService;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$entityManager = require_once __DIR__ . '/doctrine.php';

$loader = new FilesystemLoader(__DIR__ . '/../Views');
$twig = new Environment($loader, [
    'cache' => false, 
]);

$omdbService = new OmdbApiService($_ENV['OMDB_API_KEY']);

return [
    'entityManager' => $entityManager,
    'twig' => $twig,
    'omdbService' => $omdbService,
];