<?php
session_start();
require_once "../../config/Database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login dahulu.']);
    exit;
}

$db = (new Database())->getConnection();

$u = $_SESSION['id_user'];
$l = $_POST['id_lapangan'];
$t = $_POST['tgl_main'];
$m = $_POST['jam_mulai'];
$s = $_POST['jam_selesai'];

try {
    // 1. Ambil harga dari tabel lapangan (menggunakan kolom 'harga')
    $sql_lap = "SELECT harga FROM lapangan WHERE id_lapangan = :l";
    $stmt_lap = $db->prepare($sql_lap);
    $stmt_lap->execute([':l' => $l]);
    $lapangan = $stmt_lap->fetch(PDO::FETCH_ASSOC);

    if (!$lapangan) {
        echo json_encode(['status' => 'error', 'message' => 'Data lapangan tidak ditemukan.']);
        exit;
    }

    // 2. Hitung durasi dan total bayar
    $awal = strtotime($m);
    $akhir = strtotime($s);
    $durasi = ($akhir - $awal) / 3600;
    $total_harga = ($durasi > 0) ? ($durasi * $lapangan['harga']) : 0;

    // 3. Simpan ke tabel booking
    $sql = "INSERT INTO booking (id_user, id_lapangan, tgl_main, jam_mulai, jam_selesai, harga, status_booking) 
            VALUES (:u, :l, :t, :m, :s, :harga, 'pending')";
    
    $stmt = $db->prepare($sql);
    $exec = $stmt->execute([
        ':u' => $u,
        ':l' => $l,
        ':t' => $t,
        ':m' => $m,
        ':s' => $s,
        ':harga' => $total_harga
    ]);

    if ($exec) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}