<?php
session_start();
require_once "../../config/Database.php";

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

$id_booking = $_GET['id'];

$database = new Database();
$db = $database->getConnection();

$query = "SELECT b.*, l.nama_lapangan 
            FROM booking b 
            JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
            WHERE b.id_booking = :id";

$stmt = $db->prepare($query);
$stmt->execute(['id' => $id_booking]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$total_bayar = $data['harga'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins';
    background: #F1F5F9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card {
    width:420px;
    background:white;
    padding:30px;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    text-align:center;
}

.detail {
    background:#F8FAFC;
    padding:18px;
    border-radius:12px;
    margin-bottom:20px;
}

.detail p {
    font-size:13px;
    color:#64748B;
}

.detail strong {
    display:block;
    font-size:16px;
    margin-bottom:8px;
}

.price {
    font-size:22px;
    font-weight:600;
    color:#22C55E;
}

.method-container {
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

.method-option input {
    display:none;
}

.method-box {
    flex:1;
    padding:12px;
    border:2px solid #E2E8F0;
    border-radius:10px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}

.method-option input:checked + .method-box {
    border-color:#22C55E;
    background:#F0FDF4;
}

#qrisArea {
    display:none;
    margin-top:15px;
}

.qr-img {
    width:200px;
    margin:10px auto;
    display:block;
}

.upload-box {
    margin-top:15px;
    text-align:left;
}

.upload-box label {
    font-size:13px;
    color:#64748B;
    display:block;
    margin-bottom:5px;
}

.btn {
    width:100%;
    margin-top:20px;
    background:#0F172A;
    color:white;
    padding:14px;
    border:none;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn:disabled {
    background:#94A3B8;
    cursor:not-allowed;
}
</style>
</head>

<body>

<div class="card">

    <h2>Pembayaran</h2>

    <div class="detail">
        <p>Lapangan</p>
        <strong><?= $data['nama_lapangan'] ?></strong>

        <p>Total Tagihan</p>
        <div class="price">
            Rp <?= number_format($total_bayar,0,',','.') ?>
        </div>
    </div>

    <!-- FORM AJAX -->
    <form id="formBayar" enctype="multipart/form-data">

        <input type="hidden" name="id_booking" value="<?= $id_booking ?>">

        <div class="method-container">
            <label class="method-option">
                <input type="radio" name="metode" value="cash" onclick="showQR(false)" required>
                <div class="method-box">Cash</div>
            </label>

            <label class="method-option">
                <input type="radio" name="metode" value="qris" onclick="showQR(true)">
                <div class="method-box">QRIS (DANA)</div>
            </label>
        </div>

        <div id="qrisArea">
            <h3>Scan QRIS</h3>
            <img src="../../assets/uploads/qrcode.jpeg" class="qr-img">
            <p style="font-size:12px; color:#64748B;">
                Scan QR lalu upload bukti pembayaran
            </p>
        </div>

        <div class="upload-box">
            <label>Upload Bukti Pembayaran</label>
            <input type="file" name="bukti" id="buktiFile">
        </div>

        <button class="btn" id="btnBayar">Konfirmasi Pembayaran</button>

    </form>

</div>

<script>
function showQR(show) {
    document.getElementById('qrisArea').style.display = show ? 'block' : 'none';
}

// AJAX SUBMIT
document.getElementById("formBayar").addEventListener("submit", async function(e){
    e.preventDefault();

    const metode = document.querySelector('input[name="metode"]:checked');
    const file = document.getElementById("buktiFile").files[0];

    if (!metode) {
        alert("Pilih metode pembayaran!");
        return;
    }

    if (metode.value === "qris" && !file) {
        alert("Upload bukti pembayaran terlebih dahulu!");
        return;
    }

    const btn = document.getElementById("btnBayar");
    btn.disabled = true;
    btn.innerText = "Memproses...";

    const formData = new FormData(this);

    try {
        const response = await fetch("proses_bayar.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if(result.status === "success"){
            alert(result.message);
            window.location.href = "riwayat.php";
        } else {
            alert(result.message);
        }

    } catch (error) {
        alert("Server error!");
    }

    btn.disabled = false;
    btn.innerText = "Konfirmasi Pembayaran";
});
</script>

</body>
</html>