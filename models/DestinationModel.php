<?php
// Nomadix/models/DestinationModel.php
require_once __DIR__ . '/Database.php';

class DestinationModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function searchDestinations($recherche = '') {
        $conn = $this->db->getConnection();
        $sql = "SELECT ville, pays, nom FROM destinations";
        $params = [];

        if (!empty($recherche)) {
            $sql .= " WHERE ville LIKE :recherche OR pays LIKE :recherche OR nom LIKE :recherche";
            $params[':recherche'] = "%$recherche%";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>