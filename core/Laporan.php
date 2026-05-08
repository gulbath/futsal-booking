<?php
class Laporan {
    private $conn;
    private $table_name = "laporan";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi untuk pelanggan mengirim laporan
    public function buatLaporan($id_user, $judul, $deskripsi) {
        $query = "INSERT INTO " . $this->table_name . " 
                    SET id_user=:id_user, judul_masalah=:judul, deskripsi=:deskripsi, status_laporan='diproses'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':deskripsi', $deskripsi);
        
        return $stmt->execute();
    }

    // Fungsi untuk Admin melihat semua laporan masuk
    public function listLaporanAdmin() {
        $query = "SELECT l.*, u.nama 
                    FROM " . $this->table_name . " l 
                    JOIN users u ON l.id_user = u.id_user 
                    ORDER BY l.tgl_lapor DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fungsi untuk Admin mengubah status laporan (misal dari diproses ke selesai)
    public function updateStatusLaporan($id_laporan, $status) {
        $query = "UPDATE " . $this->table_name . " 
                    SET status_laporan=:status 
                    WHERE id_laporan=:id_laporan";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id_laporan', $id_laporan);
        
        return $stmt->execute();
    }
}
?>