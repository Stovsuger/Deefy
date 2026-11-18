<?php
namespace iutnc\deefy\render;

/** Utilitaire d'échappement HTML (protection XSS à l'affichage). */
class Html {
    public static function e(?string $s): string {
        return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
