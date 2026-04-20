<?php
// Nomadix/models/DestinationModel.php
require_once __DIR__ . '/Database.php';

class DestinationModel {
    public function searchDestinations($recherche = '') {
        $conn = Database::getClientConnection(); // Connexion en lecture seule
        $sql = "SELECT ville, pays, nom FROM destinations";

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
}
?>