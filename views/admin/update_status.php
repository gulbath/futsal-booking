<?php
session_start();
header('Content-Type: application/json');

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak'
    ]);
    exit;
}

require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

// Ambil data POST
$id_booking = $_POST['id_booking'] ?? null;
$status_baru = $_POST['status'] ?? null;

// Validasi status ENUM database
$status_valid = ['pending', 'confirmed', 'selesai', 'dibatalkan'];

// Validasi input
if (!is_numeric($id_booking) || !in_array($status_baru, $status_valid)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak valid'
    ]);
    exit;
}

try {
    $query = "UPDATE booking 
                SET status_booking = :status 
                WHERE id_booking = :id";

    $stmt = $db->prepare($query);

    $stmt->bindParam(':status', $status_baru);
    $stmt->bindParam(':id', $id_booking, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'id' => $id_booking,
            'new_status' => $status_baru
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal update database'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error'
        // jangan tampilkan $e->getMessage() di production
    ]);
}

exit;