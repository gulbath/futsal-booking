<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <style>
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #0F172A;
            padding: 30px 20px;
            position: fixed;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar h2 {
            color: #22C55E;
            font-size: 22px;
            font-weight: 600;
            margin: 0;
        }

        .subtitle {
            font-size: 12px;
            color: #64748B;
            margin-bottom: 35px;
        }

        .nav-link {
            display: block;
            padding: 12px 14px;
            margin-bottom: 6px;
            border-radius: 10px;
            color: #94A3B8;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.25s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #22C55E;
        }

        .nav-link.active {
            background: rgba(34,197,94,0.1);
            color: #22C55E;
            font-weight: 500;
        }

        .akun {
            margin-top: auto;
        }

        .logout {
            color: #EF4444;
        }
    </style>

    <h2>Marshal Futsal</h2>
    <span class="subtitle">Panel Kendali Admin</span>

    <a href="../admin/dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
        Dashboard
    </a>

    <a href="../admin/monitoring-pesanan.php" class="nav-link <?= ($current_page == 'monitoring-pesanan.php') ? 'active' : '' ?>">
        Daftar Pesanan
    </a>

    <a href="../admin/kelola-lapangan.php" class="nav-link <?= ($current_page == 'kelola-lapangan.php') ? 'active' : '' ?>">
        Kelola Lapangan
    </a>

    <a href="../admin/monitoring-keuangan.php" class="nav-link <?= ($current_page == 'monitoring-keuangan.php') ? 'active' : '' ?>">
        Keuangan
    </a>

    <a href="../admin/monitoring-laporan.php" class="nav-link <?= ($current_page == 'monitoring-laporan.php') ? 'active' : '' ?>">
        Laporan Masalah
    </a>

    <div class="akun">
        <p style="font-size:11px; color:#475569; margin:20px 0 10px;">AKUN</p>

        <a href="../../logout.php" class="nav-link logout">
            Keluar Sistem
        </a>
    </div>

</div>