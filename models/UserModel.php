<?php
// Nomadix/models/UserModel.php
require_once __DIR__ . '/Database.php';

class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Vérifie si un email existe déjà en base de données.
     */
    public function emailExists($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Insère un nouvel utilisateur en base de données.
     */
    public function registerUser($login, $email, $motDePasseHache) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO utilisateurs (login, email, motDePasse) VALUES (?, ?, ?)");
        return $stmt->execute([$login, $email, $motDePasseHache]);
    }

    /**
     * Récupère un utilisateur par son login.
     */
    public function getUserByLogin($login) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch();
    }
}
?>