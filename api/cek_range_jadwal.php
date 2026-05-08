<?php
require_once "../config/Database.php";
header('Content-Type: application/json');

$db = (new Database())->getConnection();

$id_lap = $_GET['id_lapangan'];
$tgl = $_GET['tanggal'];
$mulai = $_GET['mulai'];
$selesai = $_GET['selesai'];

// Cek apakah ada booking yang jamnya bersinggungan
$query = "SELECT COUNT(*) FROM booking 
            WHERE id_lapangan = :id_lap 
            AND tgl_main = :tgl 
            AND status_booking != 'dibatalkan'
            AND (
            (jam_mulai < :selesai AND jam_selesai > :mulai)
        )";

$stmt = $db->prepare($query);
$stmt->execute([
    ':id_lap' => $id_lap,
    ':tgl' => $tgl,
    ':mulai' => $mulai,
    ':selesai' => $selesai
]);

$is_booked = $stmt->fetchColumn() > 0;

// Ambil harga lapangan
$q_harga = $db->prepare("SELECT harga FROM lapangan WHERE id_lapangan = ?");
$q_harga->execute([$id_lap]);
$harga_per_jam = $q_harga->fetchColumn(); // Hasil query disimpan di $harga_per_jam

// Hitung durasi
$durasi = strtotime($selesai) - strtotime($mulai);
$jam = $durasi / 3600;

// PERBAIKAN: Gunakan variabel $harga_per_jam di sini
echo json_encode([
    "available" => !$is_booked,
    "durasi" => $jam,
    "total_harga" => number_format($jam * $harga_per_jam, 0, ',', '.')
]);