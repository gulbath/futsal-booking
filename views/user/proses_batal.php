<?php
session_start();
require_once "../../config/Database.php";

if (isset($_GET['id']) && isset($_SESSION['id_user'])) {
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk mengubah status menjadi cancelled
    $query = "UPDATE booking SET status_booking = 'cancelled' 
                WHERE id_booking = :id AND id_user = :user_id AND status_booking = 'pending'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':user_id', $_SESSION['id_user']);

    if ($stmt->execute()) {
        header("Location: riwayat.php?status=dibatalkan");
    } else {
        header("Location: riwayat.php?status=error");
    }
}
?>