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
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Insère un nouvel utilisateur en base de données.
     */
    public function registerUser($login, $email, $motDePasseHache) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO utilisateurs (login, email, motDePasse) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $login, $email, $motDePasseHache);
        return $stmt->execute();
    }

    /**
     * Récupère un utilisateur par son login.
     */
    public function getUserByLogin($login) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}