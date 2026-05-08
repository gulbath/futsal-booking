<?php
class Pembayaran {
    private $conn;
    private $table_name = "pembayaran";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi untuk memproses pembayaran awal
    public function prosesBayar($id_booking, $metode, $bukti = null) {
        // Jika cash, status langsung pending tanpa bukti. 
        // Jika transfer, simpan nama file bukti.
        $status = ($metode == 'cash') ? 'pending' : 'pending'; 
        
        $query = "INSERT INTO " . $this->table_name . " 
                    SET id_booking=:id_booking, metode_bayar=:metode, 
                        bukti_bayar=:bukti, status_pembayaran=:status";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_booking", $id_booking);
        $stmt->bindParam(":metode", $metode);
        $stmt->bindParam(":bukti", $bukti);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }

    // Fungsi Admin: Validasi pembayaran menjadi Lunas
    public function konfirmasiLunas($id_pembayaran) {
        $query = "UPDATE " . $this->table_name . " 
                    SET status_pembayaran='lunas' 
                    WHERE id_pembayaran=:id_pembayaran";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_pembayaran", $id_pembayaran);
        
        return $stmt->execute();
    }
}
?>