<?php
namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;


class AudioList {

    protected int $id;
    protected string $nom;
    protected ?int $nbPistes = 0;
    protected ?int $dureeTotale = 0;
    protected array $track = [];


    public function __construct(string $nom, array $track = []) {
        $this->nom = $nom;
        $this->track = $track;
        $this->nbPistes = count($track);
        for ($i = 0 ; $i < $this->nbPistes ; $i++) {
            if ($this->track[$i]->duree !== null) {
                 $this->dureeTotale = $this->dureeTotale + $this->track[$i]->duree;
            }
        }
               
    }

    public function __get(string $name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function setId(int $id) {
        $this->id = $id;
    }

}