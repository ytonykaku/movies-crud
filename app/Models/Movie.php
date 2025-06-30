<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use App\Models\Rated; 

#[ORM\Entity]
#[ORM\Table(name: 'movies')]
class Movie{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $imdbId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string')]
    private string $plot;

    #[ORM\Column(type: 'string')]
    private string $genre;

    #[ORM\Column(type: 'float')]
    private float $ratings;

    #[ORM\OneToOne(targetEntity: Rated::class, mappedBy: 'movie', cascade: ['persist', 'remove'])]
    private ?Rated $rated = null;

    public function __construct() {}

    public function getId(): int {
        return $this->id;
    }

    public function gettitle(): string {
        return $this->title;
    }

    public function getplot(): string {
        return $this->plot;
    }

    public function getgenre(): string {
        return $this->genre;
    }

    public function getratings(): float {
        return $this->ratings;
    }

    public function getRated(): ?Rated {
        return $this->rated;
    }

    public function setRated(?Rated $rated): void {
        $this->rated = $rated;
    }

    public function getPoster(string $imdbId): string {
        return "https://img.omdbapi.com/?i={$imdbId}&apikey=c7578ff5";
    }
}