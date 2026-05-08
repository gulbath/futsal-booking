<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/Database.php";
require_once "../../core/Monitoring.php";

$database = new Database();
$db = $database->getConnection();
$monitor = new Monitoring($db);

$pendapatan = $monitor->getTotalPendapatan();
$status_lapangan = $monitor->getStatusSekarang();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Marshal Futsal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            font-family: 'Poppins', sans-serif;
            background: #F1F5F9;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 35px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #0F172A;
        }

        .user-info {
            text-align: right;
        }

        .user-info span {
            font-weight: 600;
            font-size: 14px;
        }

        .user-info small {
            color: #64748B;
        }

        /* CARD */
        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
        }

        .grid {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            flex: 1;
            border-left: 4px solid #22C55E;
        }

        .stat-card p {
            font-size: 13px;
            color: #64748B;
        }

        .stat-card h2 {
            margin-top: 10px;
            font-size: 20px;
            color: #0F172A;
        }

        /* STATUS BADGE */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .green {
            background: #DCFCE7;
            color: #166534;
        }

        .red {
            background: #FEE2E2;
            color: #991B1B;
        }

        .yellow {
            background: #FEF9C3;
            color: #854D0E;
        }

        /* INFO BOX */
        .info-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .info-box p {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<!-- SIDEBAR (DARI FILE TERPISAH) -->
<?php include '../layouts/sidebar.php'; ?>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h2>Dashboard Monitoring</h2>

        <div class="user-info">
            <span><?= $_SESSION['nama'] ?></span>
            <small>Administrator</small>
        </div>
    </div>

    <div class="grid">

        <div class="card stat-card">
            <p>Total Pemasukan</p>
            <h2>Rp <?= number_format($pendapatan, 0, ',', '.') ?></h2>
        </div>

        <?php foreach($status_lapangan as $lap): ?>
        <div class="card" style="flex:1;">
            <p><?= $lap['nama_lapangan'] ?></p>

            <div style="margin-top:12px;">
                <?php 
                    $class = "green";
                    if($lap['status_label'] == 'Sedang digunakan') $class = "red";
                    if($lap['status_label'] == 'Akan digunakan') $class = "yellow";
                ?>

                <span class="badge <?= $class ?>">
                    <?= $lap['status_label'] ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <div class="card info-box">
        <h3>Aktivitas Sistem</h3>
        <p>
            Gunakan menu <b>Daftar Pesanan</b> untuk mengelola booking secara real-time. 
            Monitoring juga membantu admin melihat kondisi lapangan serta laporan dari pengguna.
        </p>
    </div>

</div>

</body>
</html>