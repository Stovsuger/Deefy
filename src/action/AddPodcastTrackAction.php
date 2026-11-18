<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action {

    public function execute(): string {

        if (!isset($_SESSION['playlist_id'])) {
            return Dispatcher::alert("Veuillez d'abord choisir ou créer une playlist avant d'ajouter une piste.", 'error');
        }
        $playlistId = (int)$_SESSION['playlist_id'];

        if ($this->http_method !== 'POST') {
            return $this->form();
        }

        $titre = trim((string)($_POST['titre'] ?? ''));
        $auteur = trim((string)($_POST['auteur'] ?? ''));
        if ($titre === '' || mb_strlen($titre) > 100 || mb_strlen($auteur) > 100) {
            return Dispatcher::alert("Titre obligatoire (100 caractères maximum, auteur compris).", 'error') . $this->form();
        }

        $file = $_FILES['userfile'] ?? null;
        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            $msg = ($file['error'] ?? null) === UPLOAD_ERR_INI_SIZE
                ? "Le fichier dépasse la taille maximale autorisée par le serveur."
                : "Erreur lors du transfert du fichier.";
            return Dispatcher::alert($msg, 'error') . $this->form();
        }

        // Extension (insensible à la casse) ET type MIME réel du contenu
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if ($ext !== 'mp3' || !in_array($mime, ['audio/mpeg', 'audio/mp3'], true)) {
            return Dispatcher::alert("Seuls les fichiers audio .mp3 sont autorisés.", 'error') . $this->form();
        }

        $filename = uniqid('', true) . '.mp3';
        if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../audio/' . $filename)) {
            return Dispatcher::alert("Échec du transfert du fichier.", 'error') . $this->form();
        }

        $track = new PodcastTrack($titre, $filename);
        if ($auteur !== '') {
            $track->setAuteur($auteur);
        }

        $repo = DeefyRepository::getInstance();
        $track = $repo->saveTrack($track);
        $repo->addTrackPlaylist($track->id, $playlistId);
        $playlist = $repo->findPlaylistById($playlistId);

        return Dispatcher::alert("Piste ajoutée avec succès !", 'success')
             . (new AudioListRenderer($playlist))->render(Renderer::COMPACT)
             . '<a class="inline-block mt-4 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" href="?action=add-track">Ajouter encore une piste</a>';
    }

    private function form(): string {
        return <<<HTML
        <h2 class="text-2xl font-semibold mb-4">Ajouter une piste</h2>
        <form method="post" action="?action=add-track" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="titre" class="block text-sm font-medium mb-1">Titre du podcast</label>
                <input type="text" id="titre" name="titre" maxlength="100" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="auteur" class="block text-sm font-medium mb-1">Auteur</label>
                <input type="text" id="auteur" name="auteur" maxlength="100"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="fichier" class="block text-sm font-medium mb-1">Fichier audio (.mp3)</label>
                <input type="file" id="fichier" name="userfile" accept=".mp3,audio/mpeg" required
                       class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Ajouter la piste</button>
        </form>
        HTML;
    }
}
