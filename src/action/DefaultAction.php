<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action {

    public function execute() : string {
        return <<<HTML
        <h2 class="text-2xl font-semibold mb-2">Bienvenue sur Deefy</h2>
        <p class="text-slate-600 mb-6">Gérez vos playlists et ajoutez-y vos podcasts.</p>
        <div class="flex flex-wrap gap-3">
            <a class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" href="?action=all-playlists">Mes playlists</a>
            <a class="px-4 py-2 rounded-lg border border-indigo-600 text-indigo-700 hover:bg-indigo-50" href="?action=add-playlist">Créer une playlist</a>
        </div>
        HTML;
    }

}
