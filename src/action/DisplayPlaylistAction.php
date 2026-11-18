<?php

namespace iutnc\deefy\action;

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {

    public function execute(): string {
        $id = $_GET['id'] ?? ($_SESSION['playlist_id'] ?? null);

        if ($id === null) {
            return Dispatcher::alert("Aucune playlist courante : choisissez-en une dans « Mes playlists » ou créez-en une.");
        }
        if (!is_scalar($id) || !ctype_digit((string)$id)) {
            return Dispatcher::alert("Identifiant de playlist invalide.", 'error');
        }

        try {
            $pl = DeefyRepository::getInstance()->findPlaylistById((int)$id);
        } catch (\Exception $e) {
            return Dispatcher::alert("Playlist introuvable.", 'error');
        }

        $_SESSION['playlist_id'] = (int)$pl->id;
        $html = (new AudioListRenderer($pl))->render(Renderer::COMPACT);
        $html .= '<a class="inline-block mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" href="?action=add-track">Ajouter une piste</a>';

        return $html;
    }

}
