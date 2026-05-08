<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php?pesan=wajib_login");
    exit;
}

require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

$id_user = $_SESSION['id_user'];

/* TOTAL BOOKING */
$query_booking = "SELECT COUNT(*) as total 
                  FROM booking 
                  WHERE id_user = :id";

$stmt_b = $db->prepare($query_booking);
$stmt_b->bindParam(':id', $id_user);
$stmt_b->execute();

$total_booking = $stmt_b->fetch(PDO::FETCH_ASSOC)['total'];

/* LAPORAN */
$query_laporan = "SELECT COUNT(*) as total 
                  FROM laporan 
                  WHERE id_user = :id 
                  AND status = 'pending'";

$stmt_l = $db->prepare($query_laporan);
$stmt_l->bindParam(':id', $id_user);
$stmt_l->execute();

$laporan_pending = $stmt_l->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Pelanggan</title>

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins', sans-serif;
    background:#EEF2F7;
    display:flex;
}

/* ======================
   SIDEBAR
====================== */

/* ======================
   SIDEBAR
====================== */

.sidebar{
    width:260px;
    min-height:100vh;
    background:#061133;
    padding:40px 26px;
    position:fixed;
    left:0;
    top:0;
}

.logo h2{
    color:#22E55E;
    font-size:28px;
    font-weight:700;
    line-height:1.1;
    margin-bottom:6px;
}

.logo p{
    color:#7F8AA3;
    font-size:14px;
    font-weight:400;
}

/* MENU */

.nav-menu{
    margin-top:60px;
}

.nav-link{
    display:block;
    text-decoration:none;
    color:#D8DEEA;
    padding:18px 24px;
    border-radius:16px;
    margin-bottom:12px;
    font-size:15px;
    font-weight:500;
    transition:0.3s;
}

.nav-link:hover{
    background:#172443;
    color:#22E55E;
}

.nav-link.active{
    background:#172443;
    color:#22E55E;
    font-weight:600;
}

.akun{
    margin-top:90px;
}

.akun p{
    color:#52607D;
    font-size:13px;
    letter-spacing:2px;
    margin-left:8px;
    margin-bottom:16px;
}

.logout{
    color:#FF4B4B !important;
}

/* ======================
   MAIN CONTENT
====================== */

.main-content{
    margin-left:260px;
    width:100%;
    padding:28px 32px;
}

/* HEADER */

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.header h1{
    font-size:28px;
    color:#0F172A;
    font-weight:700;
}

.user-info{
    text-align:right;
}

.user-info h3{
    font-size:14px;
    color:#0F172A;
    font-weight:600;
    margin-bottom:2px;
}

.user-info span{
    color:#64748B;
    font-size:12px;
}

/* HERO */

.hero{
    background:linear-gradient(135deg,#16A34A,#22C55E);
    border-radius:22px;
    padding:30px 34px;
    margin-bottom:18px;
    position:relative;
    overflow:hidden;
}

.hero::after{
    content:'';
    width:180px;
    height:180px;
    background:rgba(255,255,255,0.10);
    border-radius:50%;
    position:absolute;
    right:-55px;
    top:-55px;
}

.hero h2{
    color:white;
    font-size:22px;
    font-weight:700;
    margin-bottom:12px;
    position:relative;
    z-index:2;
}

.hero p{
    color:white;
    font-size:13px;
    line-height:1.8;
    max-width:560px;
    position:relative;
    z-index:2;
}

.btn-booking{
    display:inline-block;
    margin-top:18px;
    background:white;
    color:#16A34A;
    text-decoration:none;
    padding:11px 22px;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
    position:relative;
    z-index:2;
    transition:0.3s;
}

.btn-booking:hover{
    transform:translateY(-2px);
}

/* STAT CARD */

.stats{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
    margin-bottom:16px;
}

.card{
    background:white;
    border-radius:20px;
    padding:24px;
    box-shadow:0 4px 10px rgba(0,0,0,0.03);
}

.card-title{
    color:#64748B;
    font-size:13px;
    font-weight:500;
    margin-bottom:14px;
}

.card-number{
    font-size:42px;
    font-weight:700;
    color:#0F172A;
    line-height:1;
    margin-bottom:12px;
}

.green{
    color:#22C55E;
    font-size:13px;
    font-weight:600;
}

.red{
    color:#FF4B4B;
    font-size:13px;
    font-weight:600;
}

/* KETERANGAN */

.info{
    background:white;
    border-radius:20px;
    padding:24px;
    box-shadow:0 4px 10px rgba(0,0,0,0.03);
}

.info h3{
    font-size:18px;
    margin-bottom:12px;
    color:#0F172A;
    font-weight:700;
}

.info p{
    color:#475569;
    line-height:1.8;
    font-size:13px;
}

/* RESPONSIVE */

@media(max-width:900px){

    body{
        flex-direction:column;
    }

    .sidebar{
        width:100%;
        position:relative;
        min-height:auto;
    }

    .main-content{
        margin-left:0;
        padding:24px;
    }

    .stats{
        grid-template-columns:1fr;
    }

    .header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }

}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        <h2>Marshal Futsal</h2>
        <p>Panel Pelanggan</p>
    </div>

    <div class="nav-menu">

        <a href="dashboard.php" class="nav-link active">
            Dashboard
        </a>

        <a href="booking.php" class="nav-link">
            Pesan Lapangan
        </a>

        <a href="riwayat.php" class="nav-link">
            Riwayat Sewa
        </a>

        <a href="laporan.php" class="nav-link">
            Laporan Masalah
        </a>

        <div class="akun">

            <p>AKUN</p>

            <a href="../../logout.php" class="nav-link logout">
                Keluar Sistem
            </a>

        </div>

    </div>

</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- HEADER -->
    <div class="header">

        <h1>Dashboard</h1>

        <div class="user-info">
            <h3><?= $_SESSION['nama']; ?></h3>
            <span>Pelanggan</span>
        </div>

    </div>

    <!-- HERO -->
    <div class="hero">

        <h2>
            Selamat Datang,
            <?= explode(' ', $_SESSION['nama'])[0]; ?>
        </h2>

        <p>
            Booking lapangan favoritmu sekarang dan nikmati
            pengalaman bermain futsal yang lebih nyaman,
            cepat, dan modern bersama Marshal Futsal.
        </p>

        <a href="booking.php" class="btn-booking">
            Mulai Booking
        </a>

    </div>

    <!-- STATISTIK -->
    <div class="stats">

        <div class="card">

            <div class="card-title">
                Total Riwayat Booking
            </div>

            <div class="card-number">
                <?= $total_booking; ?>
            </div>

            <div class="green">
                Pesanan terdaftar
            </div>

        </div>

        <div class="card">

            <div class="card-title">
                Laporan Masalah Aktif
            </div>

            <div class="card-number">
                <?= $laporan_pending; ?>
            </div>

            <div class="red">
                Menunggu perbaikan
            </div>

        </div>

    </div>

    <!-- KETERANGAN -->
    <div class="info">

        <h3>Keterangan</h3>

        <p>
            Pastikan Anda datang 15 menit sebelum waktu bermain dimulai
            agar dapat melakukan pemanasan terlebih dahulu.
            Simpan bukti pembayaran dan tunjukkan riwayat booking
            kepada kasir saat tiba di lokasi Marshal Futsal.
        </p>

    </div>

</div>

</body>
</html>