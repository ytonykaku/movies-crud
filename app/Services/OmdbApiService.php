<?php 

namespace App\Services;

class OmdbApiService {

    private string $apiKey;
    private const API_URL = 'http://www.omdbapi.com/';

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    public function searchMovies(string $searchString, int $page = 1): ?array {
        return $this->makeRequest([
            's' => $searchString,
            'page' => $page,
        ]);
    }

    public function findById(string $imdbId): ?array {
        return $this->makeRequest([
            'i' => $imdbId,
            'plot' => 'full'
        ]);
    }

    private function makeRequest(array $parameters): ?array {
        $queryParameters = array_merge(['apikey' => $this->apiKey], $parameters);
        $queryString = http_build_query($queryParameters);
        $fullUrl = self::API_URL . '?' . $queryString;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FAILONERROR => true,
        ]);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
             curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['Response']) && $data['Response'] === 'False') {
            return null;
        }

        return $data;

    }
}