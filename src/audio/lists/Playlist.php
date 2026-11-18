<?php
namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

require_once 'AudioList.php';

class Playlist extends AudioList {

    public function ajouterPiste(AudioTrack $track): void {
        $this->track[] = $track;
        $this->nbPistes++;
        if ($track->duree !== null) {
            $this->dureeTotale = $this->dureeTotale + $track->duree;
        }    
    }




    public function supprimerPiste(int $ind): void {
        if (isset($this->track[$ind])) {
            $rem = $this->track[$ind];

            if ($rem->duree !== null) {
                $this->dureeTotale = $this->dureeTotale - $rem->duree;
            }

            unset($this->track[$ind]);
            $this->nbPistes--;
        }
    }



    public function ajouterListe(array $new): void {
        foreach ($new as $track) {
            if (!in_array($track, $this->track, true)) {
                $this->ajouterPiste($track);
            }
        }
    }

}