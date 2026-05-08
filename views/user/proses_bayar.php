<?php
session_start();
require_once "../../config/Database.php";

header('Content-Type: application/json');

if (!isset($_POST['id_booking']) || !isset($_POST['metode'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

$id_booking = $_POST['id_booking'];
$metode = $_POST['metode'];

$database = new Database();
$db = $database->getConnection();

// Default value
$bukti_path = null;

// =======================
// UPLOAD FILE BUKTI
// =======================
if ($metode == 'qris') {

    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] != 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bukti transfer wajib diupload untuk QRIS'
        ]);
        exit;
    }

    $file = $_FILES['bukti'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format file harus JPG/PNG'
        ]);
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ukuran maksimal 2MB'
        ]);
        exit;
    }

    // buat folder kalau belum ada
    $folder = "../../uploads/bukti/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // nama unik file
    $nama_file = "bukti_" . time() . "." . $ext;
    $target = $folder . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal upload file'
        ]);
        exit;
    }

    $bukti_path = "uploads/bukti/" . $nama_file;
}

// =======================
// UPDATE DATABASE
// =======================
try {

    $query = "UPDATE booking 
                SET metode_pembayaran = :metode,
                    bukti_transfer = :bukti,
                    status_konfirmasi = 'menunggu'
                WHERE id_booking = :id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(':metode', $metode);
    $stmt->bindParam(':bukti', $bukti_path);
    $stmt->bindParam(':id', $id_booking);

    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Pembayaran berhasil dikirim'
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'status' => 'error',
        'message' => 'DB Error: ' . $e->getMessage()
    ]);
}