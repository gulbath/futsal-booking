<?php
session_start();

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak");
}

require_once "../../config/Database.php";

$id = $_GET['id'] ?? null;
$aksi = $_GET['aksi'] ?? null;

if (!$id || !$aksi) {
    die("Data tidak lengkap");
}

$database = new Database();
$db = $database->getConnection();

if ($aksi == 'terima') {

    $query = "UPDATE booking 
                SET status_konfirmasi='diterima',
                    status_booking='confirmed'
                WHERE id_booking=:id";

} else {

    $query = "UPDATE booking 
                SET status_konfirmasi='ditolak'
                WHERE id_booking=:id";
}

$stmt = $db->prepare($query);
$stmt->execute(['id'=>$id]);

header("Location: monitoring-pesanan.php");