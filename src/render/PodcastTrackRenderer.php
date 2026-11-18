<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

require_once 'AudioTrackRenderer.php';
require_once 'Html.php';

class PodcastTrackRenderer extends AudioTrackRenderer {

    public function __construct(PodcastTrack $podcast) {
        parent::__construct($podcast);
    }

    protected function compactRender(): string {
        return '<div class="border border-slate-200 rounded-lg p-3"><p class="font-medium mb-2">'
            . Html::e($this->track->titre) . '</p><audio class="w-full" controls src="audio/'
            . Html::e($this->track->nomFichier) . '"></audio></div>';
    }

    protected function longRender(): string {
        $html = '<div class="border border-slate-200 rounded-lg p-3">';
        $html .= '<h3 class="font-semibold">' . Html::e($this->track->titre) . '</h3>';
        $html .= '<audio class="w-full" controls src="audio/' . Html::e($this->track->nomFichier) . '"></audio>';
        return $html . '</div>';
    }
}
