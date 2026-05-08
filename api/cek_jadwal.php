<?php
require_once "../config/Database.php";
require_once "../core/Booking.php";

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);

// Menangkap data dari request AJAX
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$id_lapangan = $_GET['id_lapangan'] ?? 1;

// List jam operasional (08:00 - 22:00)
$jam_operasional = [
    "08:00", "09:00", "10:00", "11:00", "12:00", "13:00", 
    "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00"
];

$hasil = [];

foreach ($jam_operasional as $jam) {
    // Memanggil fungsi OOP isSlotAvailable yang sudah kita buat sebelumnya
    $tersedia = $booking->isSlotAvailable($id_lapangan, $tanggal, $jam);
    
    $hasil[] = [
        "jam" => $jam,
        "status" => $tersedia ? "tersedia" : "terisi"
    ];
}

echo json_encode($hasil);
?>