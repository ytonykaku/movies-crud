<?php 

namespace App\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Tonykaku\MoviesCrud\Models\Rated;

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

        $rated = new Rated();
        $rated->setRate($data['rate'] ?? 0);
        $rated->setDescription($data['description'] ?? '');

        $this->doctrine->persist($rated);
        $this->doctrine->flush();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Filme salvo com sucesso!']);
        exit();
    }

     public function updateAction(): void {
        $data = $_POST;

        if (empty($data['imdbId']) || empty($data['title'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['status' => 'error', 'message' => 'Dados insuficientes para atualizar a avaliação.']);
            exit();
        }

        $rated = $this->doctrine->getRepository(Rated::class)->find($data['id']);
        $rated->setRate($data['rate'] ?? 0);
        $rated->setDescription($data['description'] ?? '');

        $this->doctrine->persist($rated);
        $this->doctrine->flush();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Filme atualizado com sucesso!']);
        exit();
    }

    public function deleteAction(): void {
        $id = $_POST['id'];

        $rated = $this->doctrine->getRepository(Rated::class)->find($id);

        if ($rated) {
            $rated->setIsDisable(true);
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
            $rated->doctrine->setIsDisable(null);
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