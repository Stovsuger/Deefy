<?php
namespace iutnc\deefy\audio\tracks;
require_once 'AudioTrack.php';

class AlbumTrack extends AudioTrack {

    protected ?string $artiste = null;
    protected string $album;
    protected ?int $annee = null;
    protected int $numPiste;

    public function __construct(string $titre, string $nomFichier, string $album, int $numPiste) {
        parent::__construct($titre, $nomFichier);
        $this->album = $album;
        $this->numPiste = $numPiste;
    }



    public function setArtiste(?string $artiste): void {
        $this->artiste = $artiste;
    }

    public function setAnnee(?int $annee): void {
        $this->annee = $annee;
    }

    public function __toString(): string {
        $data = get_object_vars($this);
        return json_encode($data);
    }

}