<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

require_once 'Renderer.php';
require_once 'Html.php';
require_once 'AlbumTrackRenderer.php';
require_once 'PodcastTrackRenderer.php';

class AudioListRenderer implements Renderer {

    private AudioList $liste;

    public function __construct(AudioList $liste) {
        $this->liste = $liste;
    }

    public function render(int $selector): string {
        $html = '<h2 class="text-2xl font-semibold mb-4">' . Html::e($this->liste->nom) . '</h2>';

        $html .= '<div class="space-y-3">';
        foreach ($this->liste->track as $track) {
            if ($track instanceof AlbumTrack) {
                $html .= (new AlbumTrackRenderer($track))->render(Renderer::COMPACT);
            } elseif ($track instanceof PodcastTrack) {
                $html .= (new PodcastTrackRenderer($track))->render(Renderer::COMPACT);
            }
        }
        $html .= '</div>';

        $html .= '<p class="mt-4 text-sm text-slate-500">Nombre de pistes : ' . (int)$this->liste->nbPistes . '</p>';

        return $html;
    }

}
