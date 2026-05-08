<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

$query = "SELECT b.*, l.nama_lapangan, u.nama AS nama_pelanggan 
          FROM booking b 
          JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
          JOIN users u ON b.id_user = u.id_user 
          ORDER BY b.id_booking DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$semua_pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Monitoring Pesanan</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    display:flex;
    font-family:'Poppins',sans-serif;
    background:#F1F5F9;
}

/* MAIN CONTENT */
.main-content{
    flex:1;
    margin-left:260px;
    padding:40px;
    min-height:100vh;
}

.page-title{
    font-size:42px;
    font-weight:700;
    color:#0F172A;
    margin-bottom:30px;
}

/* CARD */
.card{
    background:white;
    border-radius:20px;
    padding:25px;
    box-shadow:0 4px 20px rgba(0,0,0,0.04);
}

/* TABLE */
.table-wrapper{
    overflow-x:auto;
}

.monitoring-table{
    width:100%;
    border-collapse:collapse;
    min-width:1100px;
}

.monitoring-table thead th{
    padding:18px 14px;
    text-align:center;
    color:#64748B;
    font-size:14px;
    font-weight:600;
    border-bottom:2px solid #E2E8F0;
}

.monitoring-table tbody td{
    padding:20px 14px;
    text-align:center;
    vertical-align:middle;
    border-bottom:1px solid #F1F5F9;
    font-size:14px;
    color:#0F172A;
}

.monitoring-table tbody tr:hover{
    background:#F8FAFC;
}

/* CUSTOMER */
.customer-name{
    font-weight:600;
    margin-bottom:4px;
}

.booking-id{
    color:#94A3B8;
    font-size:12px;
}

/* BADGE */
.badge-status{
    display:inline-block;
    padding:8px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
}

.badge-success{
    background:#DCFCE7;
    color:#15803D;
}

.badge-pending{
    background:#FEF3C7;
    color:#B45309;
}

/* BUKTI */
.link-bukti{
    color:#2563EB;
    font-weight:600;
    text-decoration:none;
}

.link-bukti:hover{
    text-decoration:underline;
}

/* BUTTON */
.btn{
    border:none;
    padding:10px 16px;
    border-radius:10px;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
    transition:0.2s;
}

.btn:hover{
    transform:translateY(-1px);
}

.btn-success{
    background:#22C55E;
    color:white;
}

.btn-secondary{
    background:#94A3B8;
    color:white;
}

/* VERIFIKASI */
.verifikasi-action a{
    text-decoration:none;
    font-size:18px;
    margin:0 5px;
}

/* RESPONSIVE */
@media(max-width:768px){

    .main-content{
        margin-left:0;
        padding:20px;
    }

    .page-title{
        font-size:28px;
    }

}

</style>
</head>

<body>

<?php include '../layouts/sidebar.php'; ?>

<div class="main-content">

<h1 class="page-title">Monitoring Pesanan</h1>

<div class="card">

<div class="table-wrapper">

<table class="monitoring-table">

<thead>
<tr>
    <th>Pelanggan</th>
    <th>Lapangan</th>
    <th>Waktu</th>
    <th>Metode</th>
    <th>Status</th>
    <th>Bukti</th>
    <th>Konfirmasi</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach($semua_pesanan as $row): 

$isSelesai = ($row['status_booking'] == 'confirmed');

?>

<tr>

<td>
    <div class="customer-name">
        <?= $row['nama_pelanggan'] ?>
    </div>

    <div class="booking-id">
        #<?= $row['id_booking'] ?>
    </div>
</td>

<td>
    <?= $row['nama_lapangan'] ?>
</td>

<td>
    <?= date('d M Y', strtotime($row['tgl_main'])) ?>
    <br>
    <?= substr($row['jam_mulai'],0,5) ?> - 
    <?= substr($row['jam_selesai'],0,5) ?>
</td>

<td>
    <?= strtoupper($row['metode_pembayaran'] ?: '-') ?>
</td>

<td class="status-cell">

<?php if($isSelesai): ?>

<span class="badge-status badge-success">
    SELESAI
</span>

<?php else: ?>

<span class="badge-status badge-pending">
    PENDING
</span>

<?php endif; ?>

</td>

<td>

<?php if(!empty($row['bukti_transfer'])): ?>

<a class="link-bukti"
   href="../../<?= $row['bukti_transfer'] ?>" 
   target="_blank">

    Lihat Bukti

</a>

<?php else: ?>

<span style="color:#94A3B8;">
    Belum Upload
</span>

<?php endif; ?>

</td>

<td>

<?php if(($row['status_konfirmasi'] ?? '') == 'menunggu'): ?>

<div class="verifikasi-action">

<a href="verifikasi.php?id=<?= $row['id_booking'] ?>&aksi=terima">
    ✔
</a>

<a href="verifikasi.php?id=<?= $row['id_booking'] ?>&aksi=tolak">
    ✖
</a>

</div>

<?php else: ?>

<strong>
<?= strtoupper($row['status_konfirmasi'] ?? '-') ?>
</strong>

<?php endif; ?>

</td>

<td>

<?php if(!$isSelesai): ?>

<button class="btn btn-success"
onclick="ubahStatus(<?= $row['id_booking'] ?>, 'confirmed', this)">

Set Selesai

</button>

<?php else: ?>

<button class="btn btn-secondary"
onclick="ubahStatus(<?= $row['id_booking'] ?>, 'pending', this)">

Set Pending

</button>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
</div>
</div>

<script>

async function ubahStatus(id, statusBaru, btn){

const confirmResult = await Swal.fire({
    title:'Ubah status?',
    icon:'question',
    showCancelButton:true,
    confirmButtonText:'Ya',
    cancelButtonText:'Batal'
});

if(!confirmResult.isConfirmed) return;

const formData = new FormData();
formData.append('id_booking', id);
formData.append('status', statusBaru);

try{

const res = await fetch('update_status.php',{
    method:'POST',
    body:formData
});

const data = await res.json();

if(data.status === 'success'){

const row = btn.closest('tr');
const statusCell = row.querySelector('.status-cell');

if(statusBaru === 'confirmed'){

    statusCell.innerHTML = `
        <span class="badge-status badge-success">
            SELESAI
        </span>
    `;

    btn.innerText = 'Set Pending';
    btn.className = 'btn btn-secondary';

    btn.setAttribute(
        'onclick',
        `ubahStatus(${id}, 'pending', this)`
    );

}else{

    statusCell.innerHTML = `
        <span class="badge-status badge-pending">
            PENDING
        </span>
    `;

    btn.innerText = 'Set Selesai';
    btn.className = 'btn btn-success';

    btn.setAttribute(
        'onclick',
        `ubahStatus(${id}, 'confirmed', this)`
    );

}

Swal.fire({
    icon:'success',
    title:'Berhasil',
    timer:1200,
    showConfirmButton:false
});

}else{

Swal.fire(
    'Gagal',
    data.message,
    'error'
);

}

}catch(err){

Swal.fire(
    'Error',
    'Server error',
    'error'
);

}

}

</script>

</body>
</html>