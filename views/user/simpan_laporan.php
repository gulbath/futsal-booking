<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/Database.php";

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi habis, silakan login ulang.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$id_user = $_SESSION['id_user'];
$judul = $_POST['judul'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$tgl_lapor = date('Y-m-d H:i:s');

if (empty($judul) || empty($deskripsi)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi.']);
    exit;
}

try {
    $query = "INSERT INTO laporan (id_user, judul, deskripsi, tgl_lapor, status) 
                VALUES (:id, :judul, :deskripsi, :tgl, 'pending')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_user);
    $stmt->bindParam(':judul', $judul);
    $stmt->bindParam(':deskripsi', $deskripsi);
    $stmt->bindParam(':tgl', $tgl_lapor);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}