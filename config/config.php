<?php
// config/config.php
define('DB_HOST', 'tcp:nomadix.database.windows.net,1433');
define('DB_NAME', 'Nomadix');
define('DB_USER', 'administrateur');
define('DB_PASS', 'Lypipo25jo');

function normalizeString($str) {
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