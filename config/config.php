<?php
// Nomadix/config/config.php

// Connexion pour l'affichage public (lecture seule)
define('DB_HOST_CLIENT', '172.20.0.103');
define('DB_USER_CLIENT', 'nomadix');
define('DB_PASS_CLIENT', 'MotDePasseSecurise123!');
define('DB_NAME_CLIENT', 'Nomadix');

// Connexion pour admin
define('DB_HOST_ADMIN', '172.20.0.103');
define('DB_USER_ADMIN', 'administrateur');
define('DB_PASS_ADMIN', 'OMGUn4dminTresDoue^^');
define('DB_NAME_ADMIN', 'Nomadix');

function normalizeString($str) {
    // Ta fonction existante
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding') && !mb_check_encoding($str, 'UTF-8')) {
        $str = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1, Windows-1252');
    }
    if (function_exists('mb_strtolower')) {
        $str = mb_strtolower($str, 'UTF-8');
    } else {
        $str = strtolower($str);
    }
    if (function_exists('transliterator_transliterate')) {
        $str = transliterator_transliterate('Any-Latin; Latin-ASCII;', $str);
    } else {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        if ($converted !== false) $str = $converted;
    }
    $str = preg_replace('/[^a-z0-9]+/', '', $str);
    return $str ?? '';
}
?>