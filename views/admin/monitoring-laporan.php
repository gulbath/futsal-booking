<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/Database.php";
require_once "../../core/Laporan.php";

$database = new Database();
$db = $database->getConnection();
$laporanObj = new Laporan($db);

// Update status
if (isset($_GET['update_id'])) {
    $laporanObj->updateStatusLaporan($_GET['update_id'], 'done');
    header("Location: monitoring-laporan.php");
}

$semua_laporan = $laporanObj->listLaporanAdmin();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Laporan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            display: flex;
            font-family: 'Poppins', sans-serif;
            background: #F1F5F9;
        }

        .main-content {
            margin-left: 260px;
            padding: 35px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 22px;
            color: #0F172A;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px;
            background: #F8FAFC;
            font-size: 13px;
            color: #64748B;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: top;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .pending {
            background: #FEF9C3;
            color: #854D0E;
        }

        .done {
            background: #DCFCE7;
            color: #166534;
        }

        .btn {
            background: #0F172A;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 11px;
            display: inline-block;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<?php include '../layouts/sidebar.php'; ?>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h2>Monitoring Laporan Masalah</h2>

        <div style="text-align:right;">
            <strong><?= $_SESSION['nama'] ?></strong><br>
            <small style="color:#64748B;">Administrator</small>
        </div>
    </div>

    <div class="card">

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Masalah</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($semua_laporan as $row): ?>
                <tr>

                    <td style="font-size:13px; color:#64748B;">
                        <?= date('d M Y', strtotime($row['tgl_lapor'])) ?><br>
                        <?= date('H:i', strtotime($row['tgl_lapor'])) ?>
                    </td>

                    <td><strong><?= $row['nama'] ?></strong></td>

                    <td>
                        <div style="font-size:12px; color:#EF4444; font-weight:600;">
                            [<?= $row['fasilitas'] ?>]
                        </div>
                        <div><?= $row['judul'] ?></div>
                    </td>

                    <td style="max-width:250px; color:#64748B;">
                        <?= $row['deskripsi'] ?>
                    </td>

                    <td>
                        <?php if($row['status_laporan'] == 'done'): ?>
                            <span class="badge done">Selesai</span>
                        <?php else: ?>
                            <span class="badge pending">
                                <?= ucfirst($row['status_laporan']) ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($row['status_laporan'] !== 'done'): ?>
                            <a href="?update_id=<?= $row['id_laporan'] ?>" 
                                class="btn"
                                onclick="return confirm('Tandai sudah selesai?')">
                                Selesaikan
                            </a>
                        <?php else: ?>
                            <span style="color:#94A3B8; font-size:12px;">Tuntas</span>
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>

    </div>

</div>

</body>
</html>