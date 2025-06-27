<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Tonykaku\MoviesCrud\Models\Movie;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../.env');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../app/Views');
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../var/cache/twig',
    'debug' => true,
]);

$entityManager = require_once __DIR__ . "/../app/Config/bootstrap.php";

$route = $_SERVER['REQUEST_URI'];

switch ($route) {
    case '/':
        echo $twig->render('home.html.twig', ['message' => 'Bem-vindo ao CRUD de Filmes!']);
        break;

    case '/filmes':
    case '/filmes/listar':
        $movieRepository = $entityManager->getRepository(Movie::class);
        $moviesList = $movieRepository->findAll();

        echo $twig->render('list_movies.html.twig', [
            'movies' => $moviesList
        ]);
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo $twig->render('404.html.twig', ['message' => 'Página não encontrada.']);
        break;
}