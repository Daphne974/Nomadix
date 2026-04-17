<?php
// Initialisation à la connexion à la base de données MySQL :
echo __FILE__;
$connexion = null;
if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.ini")) {
    $config = parse_ini_file(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.ini");
    if(!isset($config["host"]) || !isset($config["user"]) || !isset($config["password"]) || !isset($config["database"])) {
        die("Erreur : le fichier config.ini est incorrect. Paramètres attendus : host, user, password, database");
    }
    else {
        $connexion = mysqli_connect($config["host"], $config["user"], $config["password"], $config["database"]);
        if (mysqli_connect_errno()) {
            die("Erreur : la connexion à la base de données a échoué. Erreur MySQL : ". mysqli_connect_error());
        }
        else {
            
            mysqli_set_charset($connexion, 'utf8mb4');
        }
    }
}
else {
    die("Erreur : le fichier config.ini est introuvable à la racine du répertoire Nomadix");
}