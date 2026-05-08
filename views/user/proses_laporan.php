<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

// Ambil data dari form
$id_user   = $_SESSION['id_user'];
$fasilitas = $_POST['fasilitas']; // Diambil dari <select name="fasilitas">
$judul     = $_POST['judul'];     // Diambil dari <input name="judul">
$deskripsi = $_POST['detail_laporan']; // Sesuaikan name di HTML dengan variabel ini

try {
    // Sesuaikan nama kolom dengan gambar: fasilitas, judul, deskripsi, tgl_lapor, status_laporan
    $query = "INSERT INTO laporan (id_user, fasilitas, judul, deskripsi, tgl_lapor, status_laporan) 
                VALUES (:id_user, :fasilitas, :judul, :deskripsi, NOW(), 'pending')";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':id_user'   => $id_user,
        ':fasilitas' => $fasilitas,
        ':judul'     => $judul,
        ':deskripsi' => $deskripsi
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Laporan berhasil terkirim!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
}
?>