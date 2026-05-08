<?php
session_start();
// Proteksi login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php?pesan=wajib_login");
    exit;
}

require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

$id_user = $_SESSION['id_user'];

$query = "SELECT b.id_booking, b.tgl_main, b.jam_mulai, b.jam_selesai, b.harga, 
                b.status_booking, b.metode_pembayaran, l.nama_lapangan 
            FROM booking b 
            JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
            WHERE b.id_user = :id 
            ORDER BY b.id_booking DESC"; 

$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_user);
$stmt->execute();
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa - Marshal Futsal</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        
        body { 
            display: flex; 
            background: #F8FAFC; 
            margin: 0; 
            font-family: 'Poppins', sans-serif; 
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0F172A;
            color: white;
            padding: 30px 20px;
            box-sizing: border-box;
            position: fixed;
        }
        .sidebar h2 {
            color: #22C55E;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .sidebar .subtitle {
            font-size: 11px;
            color: #64748B;
            margin-bottom: 40px;
            display: block;
        }
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .nav-link {
            color: #94A3B8;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.3s;
            margin-bottom: 5px;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: #22C55E;
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 40px;
        }
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
        }
        
        .table-riwayat { width: 100%; border-collapse: collapse; font-size: 14px; }
        .table-riwayat th, .table-riwayat td { padding: 15px; text-align: left; border-bottom: 1px solid #F1F5F9; }
        .table-riwayat th { color: #64748B; font-weight: 500; background: #F8FAFC; }
        
        .badge-payment {
            font-size: 10px;
            font-weight: 600;
            color: #64748B;
            background: #F1F5F9;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Marshal Futsal</h2>
        <span class="subtitle">Panel Pelanggan</span>
        
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="booking.php" class="nav-link">Pesan Lapangan</a>
            <a href="riwayat.php" class="nav-link active">Riwayat Sewa</a>
            <a href="laporan.php" class="nav-link">Laporan Masalah</a>
            
            <div style="margin-top: 50px;">
                <p style="font-size: 11px; color: #475569; margin-left: 15px; margin-bottom: 10px; letter-spacing: 1px;">AKUN</p>
                <a href="../../logout.php" class="nav-link" style="color: #EF4444;">Keluar Sistem</a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <h2 style="font-weight: 600; color: #0F172A; margin-bottom: 25px;">Riwayat Sewa Lapangan</h2>
        
        <div class="card">
            <?php if (count($riwayat) > 0): ?>
                <table class="table-riwayat">
                    <thead>
                        <tr>
                            <th>Tanggal Main</th>
                            <th>Lapangan</th>
                            <th>Jam</th>
                            <th>Total Harga</th>
                            <th>Status & Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $row): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tgl_main'])) ?></td>
                            <td><b style="color: #0F172A;"><?= $row['nama_lapangan'] ?></b></td>
                            <td><?= date('H:i', strtotime($row['jam_mulai'])) ?> - <?= date('H:i', strtotime($row['jam_selesai'])) ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($row['status_booking'] == 'pending'): ?>
                                    <span style="background: #FEF9C3; color: #854D0E; padding: 5px; border-radius: 4px; font-size: 11px; font-weight: bold;">MENUNGGU BAYAR</span>
                                    <br>
                                    <a href="pembayaran.php?id=<?= $row['id_booking']; ?>" style="background: #22C55E; color: white; padding: 5px 10px; border-radius: 4px; display: inline-block; margin-top: 5px; text-decoration: none; font-size: 11px; font-weight: bold;">Pilih Pembayaran</a>
                                    <br>
                                    <a href="proses_batal.php?id=<?= $row['id_booking']; ?>" 
                                        onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" 
                                        style="color: #EF4444; font-size: 11px; text-decoration: none; font-weight: 600; margin-top: 5px; display: inline-block;">
                                        × Batalkan Pesanan
                                    </a>
                                <?php elseif ($row['status_booking'] == 'confirmed'): ?>
                                    <span style="background: #DCFCE7; color: #166534; padding: 5px; border-radius: 4px; font-size: 11px; font-weight: bold;">LUNAS / BERHASIL</span>
                                <?php else: ?>
                                    <span style="background: #FEE2E2; color: #991B1B; padding: 5px; border-radius: 4px; font-size: 11px; font-weight: bold;">DIBATALKAN</span>
                                <?php endif; ?>

                                <?php if (!empty($row['metode_pembayaran'])): ?>
                                    <br>
                                    <span class="badge-payment">Metode: <?= strtoupper($row['metode_pembayaran']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94A3B8;">
                    <p style="margin-bottom: 20px;">Anda belum pernah melakukan pemesanan lapangan.</p>
                    <a href="booking.php" style="background: #0F172A; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 13px;">Pesan Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>