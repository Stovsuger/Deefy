<?php

namespace iutnc\deefy\action;

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\Html;
use iutnc\deefy\repository\DeefyRepository;

class DisplayAllPlaylistsAction extends Action {

    public function execute(): string {
        $playlists = DeefyRepository::getInstance()->findAllPlaylist();

        if (empty($playlists)) {
            return Dispatcher::alert("Aucune playlist enregistrée en base de données.")
                 . '<a class="text-indigo-700 hover:underline" href="?action=add-playlist">Créer la première playlist</a>';
        }

        $html = '<h2 class="text-2xl font-semibold mb-4">Mes playlists</h2><ul class="divide-y divide-slate-200 border border-slate-200 rounded-lg">';
        foreach ($playlists as $pl) {
            $nom = Html::e($pl->nom);
            $id = (int)$pl->id;
            $html .= "<li><a class=\"block px-4 py-3 hover:bg-indigo-50\" href=\"?action=playlist&amp;id={$id}\">{$nom}</a></li>";
        }
        return $html . '</ul>';
    }
}
