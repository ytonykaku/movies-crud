<?php

namespace App\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use App\Models\Rated;
use Twig\Environment;

class PageController{

    private EntityManagerInterface  $doctrine;
    private Environment $twig;

    public function __construct(EntityManagerInterface $doctrine, Environment $twig) {
        $this->doctrine = $doctrine;
        $this->twig = $twig;
    }

    public function indexAction(): void {
        $ratedMovies = $this->doctrine->getRepository(Rated::class)->findAll();

        echo $this->twig->render('pages/index.html.twig', [
            'ratedMovies' => $ratedMovies,
        ]);
    }
}