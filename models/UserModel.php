<?php
// Nomadix/models/UserModel.php
require_once __DIR__ . '/Database.php';

class UserModel
{
    public function emailExists($email)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function registerUser($login, $email, $motDePasseHache)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("INSERT INTO utilisateurs (login, email, motDePasse) VALUES (?, ?, ?)");
        return $stmt->execute([$login, $email, $motDePasseHache]);
    }

    public function getUserByLogin($login)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch();
    }

    public function getUserById($id)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Nomadix/models/UserModel.php
    public function isAdmin($userId)
    {
        $conn = Database::getClientConnection();
        $stmt = $conn->prepare("SELECT admin FROM utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result && $result['admin'] == ROLE_ADMIN;
    }
}
?>