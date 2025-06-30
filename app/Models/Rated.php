<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Movies;

#[ORM\Entity]
#[ORM\Table(name: 'rateds')]
class Rated{

    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Movie::class, inversedBy: 'rated')]
    #[ORM\JoinColumn(name: 'movie_id', referencedColumnName: 'id', unique: true, nullable: false)]
    private Movie $movie;

    #[ORM\Column(type: 'float')]
    private float $rate;

    #[ORM\Column(type: 'string')]
    private string $description;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private string $isDeleted;

    public function __construct() {}

    public function getId(): int {
        return $this->movie->getId();
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getRate(): float {
        return $this->rate;
    }

    public function setRate(float $rate): void {
        $this->rate = $rate;
    }

    public function getIsDeleted(): bool {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void {
        $this->isDeleted = $isDeleted;
    }

    public function getMovie(): Movie {
        return $this->movie;
    }

    public function setMovie(Movie $movie): void {
        $this->movie = $movie;
    }

}