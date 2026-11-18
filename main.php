<?php
require_once 'vendor/autoload.php';

use iutnc\deefy\loader\Psr4ClassLoader;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\lists\Album;
use iutnc\deefy\render\AlbumTrackRenderer;
use iutnc\deefy\render\PodcastTrackRenderer;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\action\DisplayAllPlaylistsAction;



//$loader = new Psr4ClassLoader("iutnc\\deefy\\", __DIR__ . "/src/classes/");
//$loader->register();

session_start();

DeefyRepository::setConfig(__DIR__ . '/db.config.ini');

$dispatcher = new Dispatcher();
$dispatcher->addRoute('default', DefaultAction::class)
           ->addRoute('all-playlists', DisplayAllPlaylistsAction::class)
           ->addRoute('playlist', DisplayPlaylistAction::class)
           ->addRoute('add-playlist', AddPlaylistAction::class)
           ->addRoute('add-track', AddPodcastTrackAction::class);
$dispatcher->run();
