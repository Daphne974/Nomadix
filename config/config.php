<?php
// Nomadix/config/config.php

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion pour l'affichage public (lecture seule)
define('DB_HOST_CLIENT', '172.20.0.103');
define('DB_USER_CLIENT', 'nomadix');
define('DB_PASS_CLIENT', 'MotDePasseSecurise123!');
define('DB_NAME_CLIENT', 'Nomadix');
define('ROLE_USER', 0);

// Connexion pour admin
define('DB_HOST_ADMIN', '172.20.0.103');
define('DB_USER_ADMIN', 'administrateur');
define('DB_PASS_ADMIN', 'OMGUn4dminTresDoue^^');
define('DB_NAME_ADMIN', 'Nomadix');
define('ROLE_ADMIN', 1);

/**
 * Normalise une chaîne
 */
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

/**
 * Génère un token CSRF
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Nettoie et valide les entrées utilisateur
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est admin
 */
function isAdminUser() {
    return isset($_SESSION['user']) && (int)$_SESSION['user']['admin'] === 1;
}

/**
 * Génère une URL complète avec le chemin de base de l'application
 */
function siteUrl($path = '/') {
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($basePath === '/' || $basePath === '') {
        $basePath = '';
    }
    return $basePath . '/' . ltrim($path, '/');
}
?>
