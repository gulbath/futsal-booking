<?php
session_start();
require_once "../../config/Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_booking = $_POST['id_booking'] ?? null;

    if (!$id_booking || !isset($_FILES['bukti'])) {
        die("Data tidak lengkap");
    }

    $file = $_FILES['bukti'];
    $namaFile = time() . "_" . basename($file['name']);
    $targetDir = "../../assets/uploads/bukti/";
    $targetFile = $targetDir . $namaFile;

    // Validasi sederhana
    $allowed = ['jpg','jpeg','png'];
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Format file harus JPG/PNG");
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {

        $database = new Database();
        $db = $database->getConnection();

        $query = "UPDATE booking 
                    SET bukti_transfer = :bukti,
                    status_konfirmasi = 'menunggu'
                    WHERE id_booking = :id";

        $stmt = $db->prepare($query);
        $stmt->execute([
            'bukti' => "assets/uploads/bukti/" . $namaFile,
            'id' => $id_booking
        ]);

        header("Location: riwayat.php?success=1");
        exit;

    } else {
        echo "Upload gagal";
    }
}