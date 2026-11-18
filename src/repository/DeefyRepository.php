<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository {

    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [ ];







    

    private function __construct(array $conf) {
        $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }








    public static function getInstance(){
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file) {
    $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = [ 'dsn'=> "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8",'user'=> $conf['username'],'pass'=> $conf['password'] ];
    }







    public function findAllPlaylist(): array { 
        $sql = "select * from playlist";
        $stmt = $this->pdo->query($sql);
        $playlists = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pl = new Playlist($row['nom']);
            $pl->setId((int)$row['id']);
            $playlists[] = $pl;
        }

        return $playlists;
    }






    public function saveEmptyPlaylist(Playlist $pk, ?int $owner_id = null): Playlist {
        $query = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['nom' => $pk->nom]);
        $pk->setID($this->pdo->lastInsertId());
        if ($owner_id !== null) {
            $query = "INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)";
            try {
                $stmt = $this->pdo->prepare($query);
                $stmt->execute(['id_user' => $owner_id, 'id_pl' => $pk->id]);
            } catch (\PDOException $e) {
                throw new \Exception("Error executing query : " . $e->getMessage());
            }
        }
        return $pk;
    }






    public function saveTrack(AudioTrack $track) {
        if ($track instanceof PodcastTrack) {
            $sql = "INSERT INTO track (titre, genre, duree, filename, type, auteur_podcast) VALUES (:titre, :genre, :duree, :filename, 'P', :auteur)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titre' => $track->titre,
                ':genre' => $track->genre ?? null,
                ':duree' => $track->duree ?? null,
                ':filename' => $track->nomFichier,
                ':auteur' => $track->auteur ?? null
            ]);
        } else if ($track instanceof AlbumTrack) {
            $sql = "INSERT INTO track (titre, genre, duree, filename, type, artiste_album, titre_album, annee_album, numero_album) VALUES (:titre, :genre, :duree, :filename, 'A', :artiste, :album, :annee, :numero)";
        
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':titre'    => $track->titre,
                ':genre'    => $track->genre ?? null,
                ':duree'    => $track->duree ?? null,
                ':filename' => $track->nomFichier,
                ':artiste'  => $track->artiste ?? null,
                ':album'    => $track->album ?? null,
                ':annee'    => $track->annee ?? null,
                ':numero'   => $track->numero ?? null
            ]);
        }
        $id = (int)$this->pdo->lastInsertId();
        $track->setId($id);

        return $track;
    }





    public function addTrackPlaylist(int $idTrack, int $idPlaylist) {
        $stmt1 = $this->pdo->prepare("select count(*)+1 from playlist2track where id_pl = :id_pl");
        $stmt1->execute([':id_pl' => $idPlaylist]);
        $pos = (int)$stmt1->fetchColumn();

        $sql = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste)";
        $stmt2 = $this->pdo->prepare($sql);
        $stmt2->execute([
            ':id_pl'    => $idPlaylist,
            ':id_track' => $idTrack,
            ':no_piste' => $pos
        ]);
    }





    public function findPlaylistById(int $id): PlayList {
        $query = "SELECT * FROM playlist p WHERE p.id = :id";
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception("Error executing query : " . $e->getMessage());
        }

        if ($row === false) {
            throw new \Exception("Playlist $id not found");
        }

        $playlist = new PlayList($row['nom'], []);
        $playlist->setID($row['id']);

        $query_tracks = "SELECT * FROM track t, playlist2track p WHERE t.id = p.id_track AND p.id_pl = :id";
        try {
            $stmt = $this->pdo->prepare($query_tracks);
            $stmt->execute(['id' => $id]);
            $rows = $stmt->fetchAll();
        } catch (\PDOException $e) {
            throw new \Exception("Error executing query : " . $e->getMessage());
        }

        foreach ($rows as $row) {
            switch ($row['type']) {
                case 'A':
                    $titreAlbum = $row['titre_album'] ?? '';
                    $numeroAlbum = isset($row['numero_album']) ? (int)$row['numero_album'] : 0;
                    $track = new AlbumTrack($row['titre'], $row['filename'], $titreAlbum, $numeroAlbum);
                    $track->setID((int)$row['id']);
                    if (!empty($row['artiste_album'])) {
                        $track->setArtiste($row['artiste_album']);
                    }
                    if (!empty($row['annee_album'])) {
                        $track->setAnnee((int)$row['annee_album']);
                    }
                    if (!empty($row['duree'])) {
                        $track->setDuree((int)$row['duree']);
                    }
                    if (!empty($row['genre'])) {
                        $track->setGenre($row['genre']);
                    }
                    break;
                case 'P':
                    $track = new PodcastTrack($row['titre'], $row['filename']);
                    $track->setID($row['id']);
                    if (!empty($row['auteur_podcast'])) {
                        $track->setAuteur($row['auteur_podcast']);
                    }
                    if (!empty($row['date_podcast'])) {
                        $track->setDate($row['date_podcast']);
                    }
                    if (!empty($row['duree'])) {
                        $track->setDuree((int)$row['duree']);
                    }
                    if (!empty($row['genre'])) {
                        $track->setGenre($row['genre']);
                    }
                    break;
            }
            $playlist->ajouterPiste($track);
        }
        return $playlist;
    }

}
