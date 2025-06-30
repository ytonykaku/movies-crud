<?php 

namespace App\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use App\Models\Rated;
use App\Models\Movie;

class RatedController
{
    private EntityManagerInterface  $doctrine;

    public function __construct(EntityManagerInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function createAction(): void {
        $data = $_POST;

        if (empty($data['imdbId']) || empty($data['title'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['status' => 'error', 'message' => 'Dados insuficientes para criar a avaliação.']);
            exit();
        }

        $movieRepository = $this->doctrine->getRepository(Movie::class);
        $movie = $movieRepository->findOneBy(['imdbId' => $data['imdbId']]);

/*O certo mesmo ia ser fazer um movieController para poder realizar essa operação
Mas como nesse contexto em específico, as duas coisas estão diretamente associadas, farei assim*/

        if (!$movie) {
            $movie = new Movie();
            $movie->setImdbId($data['imdbId']);
            $movie->setTitle($data['title']);
            $movie->setPlot($data['plot'] ?? '');
            $movie->setGenre($data['genre'] ?? '');
            $movie->setRatings($data['imdbRating'] ?? 'N/A');
            $this->doctrine->persist($movie);
        }

        if ($movie->getRated() !== null) {
            header("HTTP/1.1 409 Conflict");
            echo json_encode(['status' => 'error', 'message' => 'Este filme já foi avaliado.']);
            exit();
        }

        $rated = new Rated();
        $rated->setRate($data['rate'] ?? 0);
        $rated->setDescription($data['description'] ?? '');
        $rated->setMovie($movie);

        $this->doctrine->persist($rated);
        $this->doctrine->flush();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Filme salvo com sucesso!']);
        exit();
    }

     public function updateAction(): void {
        $data = $_POST;

        if (empty($data['id'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['status' => 'error', 'message' => 'ID da avaliação não fornecido.']);
            exit();
        }

        $rated = $this->doctrine->getRepository(Rated::class)->find($data['id']);
        $rated->setRate($data['rate'] ?? $rated->getRate());
        $rated->setDescription($data['description'] ?? $rated->getDescription());

        $this->doctrine->flush();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Filme atualizado com sucesso!']);
        exit();
    }

    public function deleteAction(): void {
        $id = $_POST['id'] ?? null;

        $rated = $this->doctrine->getRepository(Rated::class)->find($id);

        if ($rated) {
            $rated->setIsDeleted(true);
            $this->doctrine->flush();
            $message = 'Filme removido com sucesso';
            $status = 'success';
        } else {
            $message = 'Filme não encontrado';
            $status = 'error';
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }

    public function restoreAction(): void {
        $id = $_POST['id'];

        $rated = $this->doctrine->getRepository(Rated::class)->find($id);

if ($rated) {
            $rated->setIsDeleted(false);
            $this->doctrine->flush();
            $message = 'Filme restaurado com sucesso';
            $status = 'success';
        } else {
            $message = 'Filme não encontrado';
            $status = 'error';
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }



}