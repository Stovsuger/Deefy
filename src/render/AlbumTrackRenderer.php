<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

require_once 'AudioTrackRenderer.php';
require_once 'Html.php';

class AlbumTrackRenderer extends AudioTrackRenderer {

    public function __construct(AlbumTrack $albumTrack) {
        parent::__construct($albumTrack);
    }

    protected function compactRender(): string {
        return '<div class="border border-slate-200 rounded-lg p-3"><p class="font-medium mb-2">'
            . (int)$this->track->numPiste . '. ' . Html::e($this->track->titre)
            . ' (' . Html::e($this->track->album) . ')</p><audio class="w-full" controls src="audio/'
            . Html::e($this->track->nomFichier) . '"></audio></div>';
    }

    protected function longRender(): string {
        $html = '<div class="border border-slate-200 rounded-lg p-3">';
        $html .= '<h3 class="font-semibold">' . Html::e($this->track->titre) . '</h3>';
        $html .= '<p class="text-sm text-slate-500">Album : ' . Html::e($this->track->album) . ' (piste ' . (int)$this->track->numPiste . ')</p>';
        $html .= '<audio class="w-full" controls src="audio/' . Html::e($this->track->nomFichier) . '"></audio>';
        return $html . '</div>';
    }
}
