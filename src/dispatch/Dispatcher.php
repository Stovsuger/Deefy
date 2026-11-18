<?php

namespace iutnc\deefy\dispatch;

class Dispatcher {

    protected string $action;
    protected array $actions = [];

    public function __construct() {
        $a = $_GET['action'] ?? 'default';
        $this->action = is_string($a) ? $a : 'default';
    }

    public function addRoute(string $action, string $action_class) : Dispatcher {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run() : void {
        if (isset($this->actions[$this->action])) {
            try {
                $class = $this->actions[$this->action];
                $html = (new $class())->execute();
            } catch (\Throwable $e) {
                http_response_code(500);
                $html = self::alert("Une erreur est survenue. Veuillez réessayer.", 'error');
            }
        } else {
            http_response_code(404);
            $html = self::alert("Page introuvable.", 'error');
        }

        $this->renderPage($html);
    }

    /** Encadré de message (info, success, error). */
    public static function alert(string $message, string $type = 'info') : string {
        $colors = [
            'info'    => 'bg-sky-50 text-sky-800 border-sky-200',
            'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'error'   => 'bg-rose-50 text-rose-800 border-rose-200',
        ];
        $c = $colors[$type] ?? $colors['info'];
        return '<p class="border rounded-lg px-4 py-3 mb-4 ' . $c . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    private function renderPage(string $html) : void {
        echo <<<END
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deefy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <header class="bg-indigo-700 text-white shadow">
        <div class="max-w-4xl mx-auto px-4 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold tracking-tight"><a href="?action=default">Deefy</a></h1>
            <nav>
                <ul class="flex flex-wrap gap-x-5 gap-y-1 text-sm font-medium">
                    <li><a class="hover:underline" href="?action=default">Accueil</a></li>
                    <li><a class="hover:underline" href="?action=all-playlists">Mes playlists</a></li>
                    <li><a class="hover:underline" href="?action=playlist">Playlist courante</a></li>
                    <li><a class="hover:underline" href="?action=add-playlist">Créer une playlist</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            {$html}
        </div>
    </main>
</body>
</html>
END;
    }
}
