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

    #[ORM\Column(type: 'string', unique: true)]
    private string $imdbId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $plot;

    #[ORM\Column(type: 'string')]
    private string $genre;

    #[ORM\Column(type: 'string')] 
    private string $ratings;

    #[ORM\OneToOne(targetEntity: Rated::class, mappedBy: 'movie', cascade: ['persist', 'remove'])]
    private ?Rated $rated = null;

    public function __construct() {}

    // Getters
    public function getId(): int { return $this->id; }
    public function getImdbId(): string { return $this->imdbId; }
    public function getTitle(): string { return $this->title; }
    public function getPlot(): string { return $this->plot; }
    public function getGenre(): string { return $this->genre; }
    public function getRatings(): string { return $this->ratings; }
    public function getRated(): ?Rated { return $this->rated; }

    public function setImdbId(string $imdbId): void { $this->imdbId = $imdbId; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setPlot(string $plot): void { $this->plot = $plot; }
    public function setGenre(string $genre): void { $this->genre = $genre; }
    
    public function setRatings(string $ratings): void { $this->ratings = $ratings; }
    public function setRated(?Rated $rated): void { $this->rated = $rated; }

    public function getPoster(): string {
        return "https://img.omdbapi.com/?i={$this->imdbId}&apikey=c7578ff5"; 
    }
}