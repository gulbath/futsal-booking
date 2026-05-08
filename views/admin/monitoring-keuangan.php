<?php
session_start();

// PROTEKSI ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/Database.php";
require_once "../../core/Monitoring.php";

$database = new Database();
$db = $database->getConnection();

$monitor = new Monitoring($db);

/* =========================
   TOTAL PEMASUKAN
========================= */

$total_pemasukan = $monitor->getTotalPendapatan();

/* =========================
   GRAFIK PEMASUKAN
   FIX ERROR tgl_bayar
=========================

Karena di tabel pembayaran tidak ada kolom tgl_bayar,
maka kita gunakan created_at
*/

$query_grafik = "
SELECT 
    MONTHNAME(created_at) as bulan,
    SUM(total_bayar) as total
FROM pembayaran
WHERE status_pembayaran = 'lunas'
AND YEAR(created_at) = YEAR(CURDATE())
GROUP BY MONTH(created_at)
ORDER BY MONTH(created_at) ASC
";

$stmt = $db->prepare($query_grafik);
$stmt->execute();

$data_grafik = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$values = [];

foreach ($data_grafik as $row) {

    $labels[] = $row['bulan'];
    $values[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Monitoring Keuangan</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    display:flex;
    font-family:'Poppins', sans-serif;
    background:#F1F5F9;
}

/* =========================
   MAIN CONTENT
========================= */

.main-content{
    flex:1;
    margin-left:260px;
    padding:40px;
    min-height:100vh;
    background:#F8FAFC;
}

/* =========================
   HEADER
========================= */

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.header h2{
    font-size:28px;
    color:#0F172A;
    font-weight:700;
}

.admin-info{
    text-align:right;
}

.admin-info strong{
    color:#0F172A;
    font-size:15px;
}

.admin-info small{
    color:#64748B;
}

/* =========================
   GRID CARD
========================= */

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    border-radius:18px;
    padding:24px;
    box-shadow:0 6px 20px rgba(0,0,0,0.04);
}

.stat-title{
    color:#64748B;
    font-size:14px;
    margin-bottom:12px;
}

.stat-value{
    font-size:34px;
    font-weight:700;
    color:#22C55E;
}

.year{
    color:#0F172A;
}

/* =========================
   CHART
========================= */

.chart-title{
    font-size:18px;
    font-weight:600;
    margin-bottom:20px;
    color:#0F172A;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px){

    .main-content{
        margin-left:0;
        padding:25px;
    }

    .grid{
        grid-template-columns:1fr;
    }

    .header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

}

</style>

</head>

<body>

<!-- SIDEBAR -->
<?php include '../layouts/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- HEADER -->
    <div class="header">

        <h2>Monitoring Keuangan</h2>

        <div class="admin-info">
            <strong><?= $_SESSION['nama']; ?></strong><br>
            <small>Administrator</small>
        </div>

    </div>

    <!-- CARD -->
    <div class="grid">

        <!-- TOTAL PEMASUKAN -->
        <div class="card">

            <div class="stat-title">
                Total Pemasukan (Lunas)
            </div>

            <div class="stat-value">
                Rp <?= number_format($total_pemasukan, 0, ',', '.'); ?>
            </div>

        </div>

        <!-- TAHUN -->
        <div class="card">

            <div class="stat-title">
                Tahun Aktif
            </div>

            <div class="stat-value year">
                <?= date('Y'); ?>
            </div>

        </div>

    </div>

    <!-- GRAFIK -->
    <div class="card">

        <div class="chart-title">
            Grafik Pendapatan Bulanan
        </div>

        <canvas id="pemasukanChart" height="100"></canvas>

    </div>

</div>

<script>

const ctx = document.getElementById('pemasukanChart').getContext('2d');

new Chart(ctx, {

    type: 'line',

    data: {

        labels: <?= json_encode($labels) ?>,

        datasets: [{

            label: 'Pendapatan',

            data: <?= json_encode($values) ?>,

            borderColor: '#22C55E',

            backgroundColor: 'rgba(34,197,94,0.12)',

            fill: true,

            tension: 0.4,

            borderWidth: 3,

            pointBackgroundColor: '#22C55E',

            pointRadius: 5

        }]
    },

    options: {

        responsive: true,

        plugins: {

            legend: {
                display: false
            }

        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {
                    callback: function(value){
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }

            }

        }

    }

});

</script>

</body>
</html>