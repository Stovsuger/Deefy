<?php
namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

require_once 'Renderer.php';

abstract class AudioTrackRenderer implements Renderer {
    
    protected AudioTrack $track;



    
    public function __construct(AudioTrack $track) {
        $this->track = $track;
    }

    abstract protected function compactRender(): string;
    abstract protected function longRender(): string;





    public function render(int $selector): string {
        $res = "";
        if ($selector === Renderer::COMPACT) {
            $res = $this->compactRender();
        } else if ($selector === Renderer::LONG) {
            $res = $this->longRender();
        }
        return $res;
    }

    
}