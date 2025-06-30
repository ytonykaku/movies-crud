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
        $allRatedMovies = $this->doctrine->getRepository(Rated::class)->findAll();

        $activeMovies = [];
        $deletedMovies = [];

        foreach ($allRatedMovies as $rated) {
            if ($rated->getIsDeleted()) {
                $deletedMovies[] = $rated;
            } else {
                $activeMovies[] = $rated;
            }
        }

        echo $this->twig->render('pages/index.html.twig', [
            'activeMovies' => $activeMovies,
            'deletedMovies' => $deletedMovies,
        ]);
    }
}