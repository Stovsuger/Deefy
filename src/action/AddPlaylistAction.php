<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action {

    public function execute(): string {
        if ($this->http_method === 'POST') {
            $nom = trim((string)($_POST['nom'] ?? ''));
            if ($nom === '' || mb_strlen($nom) > 100) {
                return Dispatcher::alert("Le nom de la playlist est obligatoire (100 caractères maximum).", 'error') . $this->form();
            }

            // Donnée stockée brute : l'échappement se fait à l'affichage (Html::e).
            $repo = DeefyRepository::getInstance();
            $playlist = $repo->saveEmptyPlaylist(new Playlist($nom));
            $_SESSION['playlist_id'] = (int)$playlist->id;

            return Dispatcher::alert("Playlist créée.", 'success')
                 . (new AudioListRenderer($playlist))->render(Renderer::COMPACT)
                 . '<a class="inline-block mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" href="?action=add-track">Ajouter une piste</a>';
        }

        return $this->form();
    }

    private function form(): string {
        return <<<HTML
        <h2 class="text-2xl font-semibold mb-4">Créer une playlist</h2>
        <form method="post" action="?action=add-playlist" class="space-y-4">
            <div>
                <label for="nom" class="block text-sm font-medium mb-1">Nom de la playlist</label>
                <input type="text" id="nom" name="nom" maxlength="100" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Créer</button>
        </form>
        HTML;
    }

}
