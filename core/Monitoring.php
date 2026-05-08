<?php
class Monitoring {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Monitoring Keuangan: Total Pemasukan
    public function getTotalPendapatan() {
        $query = "SELECT SUM(total_bayar) as total FROM pembayaran WHERE status_pembayaran='lunas'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // 2. Monitoring Penggunaan: Lapangan paling sering dibooking
    public function getTerlaris() {
        $query = "SELECT l.nama_lapangan, COUNT(b.id_booking) as total_main 
                    FROM lapangan l 
                    LEFT JOIN booking b ON l.id_lapangan = b.id_lapangan 
                    GROUP BY l.id_lapangan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Monitoring Real-Time: Status Lapangan Saat Ini
    public function getStatusSekarang() {
        $jam_sekarang = date('H:i:s');
        $tgl_sekarang = date('Y-m-d');

        $query = "SELECT l.nama_lapangan, 
                    CASE 
                    WHEN b.id_booking IS NOT NULL AND b.status_booking = 'confirmed' THEN 'Sedang digunakan'
                    WHEN b.id_booking IS NOT NULL AND b.status_booking = 'pending' THEN 'Akan digunakan'
                    ELSE 'Tersedia'
                    END as status_label
                    FROM lapangan l
                    LEFT JOIN booking b ON l.id_lapangan = b.id_lapangan 
                    AND b.tgl_main = :tgl 
                    AND :jam BETWEEN b.jam_mulai AND b.jam_selesai";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tgl", $tgl_sekarang);
        $stmt->bindParam(":jam", $jam_sekarang);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>