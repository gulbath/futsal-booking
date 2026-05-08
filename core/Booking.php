<?php
class Booking {
    private $conn;
    private $table_name = "booking";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi untuk Admin melihat semua daftar booking
    public function getAllBooking() {
        $query = "SELECT b.*, u.nama as nama_pelanggan, l.nama_lapangan 
                    FROM " . $this->table_name . " b
                    JOIN users u ON b.id_user = u.id_user
                    JOIN lapangan l ON b.id_lapangan = l.id_lapangan
                    ORDER BY b.tgl_main DESC, b.jam_mulai ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fungsi untuk update status booking (Konfirmasi/Batalkan)
    public function updateStatus($id_booking, $status) {
        $query = "UPDATE " . $this->table_name . " SET status_booking = :status WHERE id_booking = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id_booking);
        return $stmt->execute();
    }
}
?>