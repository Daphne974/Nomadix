<?php
// Nomadix/models/DestinationModel.php
require_once __DIR__ . '/Database.php';

class DestinationModel {
    public function searchDestinations($recherche = '') {
        $conn = Database::getClientConnection();
        $sql = "SELECT * FROM destinations";

        if (!empty($recherche)) {
            $sql .= " WHERE ville LIKE ? OR pays LIKE ? OR nom LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchParam = "%$recherche%";
            $stmt->execute([$searchParam, $searchParam, $searchParam]);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    public function getDestinationByVille($ville) {
        $conn = Database::getClientConnection();
        $stmt = $conn->prepare("SELECT * FROM destinations WHERE ville = ?");
        $stmt->execute([$ville]);
        return $stmt->fetch();
    }

    public function getDestinationById($id) {
        $conn = Database::getClientConnection();
        $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>