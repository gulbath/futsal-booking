<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php?pesan=wajib_login");
    exit;
}
require_once "../../config/Database.php";

$database = new Database();
$db = $database->getConnection();

// Ambil riwayat laporan user ini (opsional, agar user tahu status laporannya)
$query = "SELECT * FROM laporan WHERE id_user = :id ORDER BY tgl_lapor DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['id_user']);
$stmt->execute();
$riwayat_laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lapor Masalah - Rent-it</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body { background: #F8FAFC; font-family: 'Poppins', sans-serif; margin: 0; display: flex; }
        .main-content { flex: 1; padding: 40px; max-width: 900px; margin: auto; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .btn-kirim { width: 100%; background: #0F172A; color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-kirim:hover { background: #1E293B; }
        .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

<div class="main-content">
    <h2 style="color: #0F172A; margin-bottom: 20px;">Laporan Masalah Fasilitas</h2>

    <div class="card">
        <p style="font-size: 14px; color: #64748B; margin-bottom: 20px;">
            Temukan kendala saat bermain? Laporkan di bawah ini agar tim teknis kami segera memperbaikinya.
        </p>
        
        <form id="formLaporan">
    <div class="form-group" style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Fasilitas yang Bermasalah</label>
        <select name="fasilitas" class="form-control" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px;">
            <option value="Lantai / Interlock">Lantai / Interlock</option>
            <option value="Lampu Lapangan">Lampu Lapangan</option>
            <option value="Jaring Gawang">Jaring Gawang</option>
            <option value="Lainnya">Lainnya</option>
        </select>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
    <label>Judul Masalah</label>
    <input type="text" name="judul" class="form-control" style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" placeholder="Masukkan judul masalah">
</div>

    <div class="form-group" style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Detail Laporan</label>
        <textarea name="detail_laporan" class="form-control" rows="4" 
                    style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; box-sizing: border-box;" 
                    placeholder="Jelaskan kendala secara detail..."></textarea>
    </div>

    <button type="button" id="btnKirim" style="width: 100%; background: #0F172A; color: white; padding: 15px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
        Kirim Laporan
    </button>
</form>
    </div>

    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 15px;">Riwayat Laporan Anda</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="text-align: left; background: #F8FAFC;">
                    <th style="padding: 10px; border-bottom: 2px solid #E2E8F0;">Tanggal</th>
                    <th style="padding: 10px; border-bottom: 2px solid #E2E8F0;">Judul</th>
                    <th style="padding: 10px; border-bottom: 2px solid #E2E8F0;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($riwayat_laporan as $row): ?>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #F1F5F9;"><?= date('d/m/Y', strtotime($row['tgl_lapor'])) ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #F1F5F9;"><?= $row['judul'] ?></td>
                    <td style="padding: 10px; border-bottom: 1px solid #F1F5F9;">
                        <?php if($row['status'] == 'pending'): ?>
                            <span class="badge" style="background: #FEF9C3; color: #854D0E;">Menunggu</span>
                        <?php else: ?>
                            <span class="badge" style="background: #DCFCE7; color: #166534;">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('btnKirim').addEventListener('click', async function() {
    const form = document.getElementById('formLaporan');
    const formData = new FormData(form);

const btnKirim = document.getElementById('btnKirim');
btnKirim.addEventListener('click', function() {
    const formData = new FormData(document.getElementById('formLaporan'));

    fetch('proses_laporan.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire('Berhasil!', data.message, 'success')
            .then(() => location.reload()); // Refresh agar form bersih
        } else {
            Swal.fire('Gagal', data.message, 'error');
        }
    });
});

    // Validasi sederhana
    if (!formData.get('judul') || !formData.get('detail_laporan')) {
        Swal.fire('Peringatan', 'Harap isi semua kolom laporan.', 'warning');
        return;
    }

    try {
        const response = await fetch('proses_laporan.php', {
            method: 'POST',
            body: formData
        });
        
        // Pastikan response adalah JSON
        const result = await response.json();

        if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonColor: '#0F172A'
            }).then(() => {
                location.reload(); // Refresh halaman agar input bersih
            });
        } else {
            Swal.fire('Gagal', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan sistem. Pastikan file proses_laporan.php sudah benar.', 'error');
    }
});
</script>
</body>
</html>