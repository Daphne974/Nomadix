<?php
// models/DestinationModel.php
require_once __DIR__ . '/Database.php';

class DestinationModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function searchDestinations($recherche = '') {
        $conn = $this->db->getConnection();
        $sql = "SELECT ville, pays, nom FROM destinations";
        $destinations = [];

        if (!empty($recherche)) {
            $recherche_escaped = $conn->real_escape_string($recherche);
            $sql .= " WHERE ville LIKE '%$recherche_escaped%'
                      OR pays LIKE '%$recherche_escaped%'
                      OR nom LIKE '%$recherche_escaped%'";
        }

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $destinations[] = $row;
            }
        }

        return $destinations;
    }
}