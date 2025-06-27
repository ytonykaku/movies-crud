<?php

namespace App\Controllers;

use App\Services\OmdbApiService;

class OmdbApiController {

    private OmdbApiService $omdbApiService;

    public function __construct(OmdbApiService $omdbApiService) {
        $this->omdbApiService = $omdbApiService;
    }
    
    public function searchAction(): void {
        $searchString = $_GET['query'] ?? '';
        $results = null;

        if (!empty($searchString)) {
            $results = $this->omdbApiService->searchMovies($searchString);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'results' => $results,
        ]);
        exit();
    }

    public function detailsAction(): void {
        $imdbId = $_GET['id'] ?? '';

        if (empty($imdbId)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'ID do filme não fornecido.']);
            exit();
        }

        $movieDetails = $this->omdbApiService->findById($imdbId);

        header('Content-Type: application/json');
        echo json_encode($movieDetails);
        exit();
    }
}