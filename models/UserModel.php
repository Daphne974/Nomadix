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

    public function registerUser($login, $email, $motDePasseHache, $avatar = null)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("INSERT INTO utilisateurs (login, email, motDePasse, avatar, admin) VALUES (?, ?, ?, ?, 0)");
        return $stmt->execute([$login, $email, $motDePasseHache, $avatar]);
    }

    public function updateAvatar($id, $avatarPath)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE utilisateurs SET avatar = ? WHERE id = ?");
        return $stmt->execute([$avatarPath, $id]);
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

    public function emailExistsExceptUser($email, $userId)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id <> ?");
        $stmt->execute([$email, $userId]);
        return $stmt->fetch() !== false;
    }

    public function updateEmail($id, $email)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE utilisateurs SET email = ? WHERE id = ?");
        return $stmt->execute([$email, $id]);
    }

    public function updatePassword($id, $motDePasseHache)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE utilisateurs SET motDePasse = ? WHERE id = ?");
        return $stmt->execute([$motDePasseHache, $id]);
    }

    public function getLoginChangedAt($id)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT login_changed_at FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $row['login_changed_at'] : null;
    }

    public function isLoginAvailableExceptUser($login, $userId)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id <> ?");
        $stmt->execute([$login, $userId]);
        return $stmt->fetch() === false;
    }

    public function updateLogin($id, $login)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("UPDATE utilisateurs SET login = ?, login_changed_at = NOW() WHERE id = ?");
        return $stmt->execute([$login, $id]);
    }

    public function deleteUser($id)
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAdmins()
    {
        $conn = Database::getAdminConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM utilisateurs WHERE admin = 1");
        $stmt->execute([]);
        $row = $stmt->fetch();
        return $row ? (int)$row['cnt'] : 0;
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