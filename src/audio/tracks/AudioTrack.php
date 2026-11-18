<?php
namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;


class AudioTrack {

    protected int $id;
    protected string $titre;
    protected ?string $genre = null;
    protected ?int $duree = null;
    protected string $nomFichier;

    public function __construct(string $titre, string $nomFichier) {
        $this->titre = $titre;
        $this->nomFichier = $nomFichier;
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function setGenre(string $genre):void {
        $this->genre = $genre;
    }

    public function setDuree(int $duree):void {
        if ($duree < 0) {
            throw new InvalidPropertyValueException("La durée ne peut pas être négative");
        }
        $this->duree = $duree;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }






    public function __toString(): string {
        $res = $this->titre . "\n" . $this->nomFichier . "\n";
        return $res;
    }


}