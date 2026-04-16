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
        if (!empty($recherche)) {
            $recherche_escaped = mysqli_real_escape_string($conn, $recherche);
            $sql .= " WHERE ville LIKE '%$recherche_escaped%'
                      OR pays LIKE '%$recherche_escaped%'
                      OR nom LIKE '%$recherche_escaped%'";
        }
        $result = mysqli_query($conn, $sql);
        $destinations = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $destinations[] = $row;
            }
        }
        mysqli_close($conn);
        return $destinations;
    }
}