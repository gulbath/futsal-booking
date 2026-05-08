<?php
class Lapangan {
    private $conn;
    private $table_name = "lapangan";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id_lapangan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama, $harga) {
        // Tipe rumput dihilangkan dari query
        $query = "INSERT INTO " . $this->table_name . " 
                    SET nama_lapangan=:nama, harga_per_jam=:harga";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':harga', $harga);
        return $stmt->execute();
    }

    public function update($id, $nama, $harga, $kondisi) {
        // Tipe rumput dihilangkan dari query update
        $query = "UPDATE " . $this->table_name . " 
                    SET nama_lapangan=:nama, harga_per_jam=:harga, status_kondisi=:kondisi 
                    WHERE id_lapangan=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':harga', $harga);
        $stmt->bindParam(':kondisi', $kondisi);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_lapangan = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>