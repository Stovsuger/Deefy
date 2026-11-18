<?php
namespace iutnc\deefy\audio\lists;
require_once 'AudioList.php';

class Album extends AudioList {

    private ?string $artiste = null;
    private ?string $dateSortie = null;





    public function __construct(string $nom, array $track) {
        parent::__construct($nom, $track);
    }





    public function setArtiste(string $artiste): void {
        $this->artiste = $artiste;
    }

    public function setDateSortie(string $dateSortie): void {
        $this->dateSortie = $dateSortie;
    }

}