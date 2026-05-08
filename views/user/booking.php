<?php
session_start();
// Proteksi login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php?pesan=wajib_login");
    exit;
}

require_once "../../config/Database.php";
require_once "../../core/Lapangan.php";

$database = new Database();
$db = $database->getConnection();
$lapanganObj = new Lapangan($db);
$list_lapangan = $lapanganObj->readAll();

// --- LOGIKA AMBIL JADWAL TERISI ---
$tanggal_pilih = $_GET['tanggal'] ?? date('Y-m-d');

$query_jadwal = "SELECT b.*, l.nama_lapangan 
                    FROM booking b 
                    JOIN lapangan l ON b.id_lapangan = l.id_lapangan 
                    WHERE b.tgl_main = :tgl 
                    AND b.status_booking IN ('pending', 'confirmed')
                    ORDER BY b.jam_mulai ASC";

$stmt_jadwal = $db->prepare($query_jadwal);
$stmt_jadwal->bindParam(':tgl', $tanggal_pilih);
$stmt_jadwal->execute();
$jadwal_terisi = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Lapangan - Marshal Futsal</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        /* Konten Utama */
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
            margin-bottom: 25px;
        }
        .input-control { 
            width: 100%; 
            padding: 12px; 
            border-radius: 8px; 
            border: 1px solid #E2E8F0; 
            margin-top: 8px; 
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .btn-booking { 
            width: 100%; 
            background: #0F172A; 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: 0.3s; 
        }
        .btn-booking:hover { background: #1E293B; }
        
        .table-jadwal { width: 100%; border-collapse: collapse; font-size: 14px; }
        .table-jadwal th, .table-jadwal td { padding: 15px; text-align: left; border-bottom: 1px solid #F1F5F9; }
        .badge-booked { 
            background: #FEE2E2; 
            color: #991B1B; 
            padding: 5px 10px; 
            border-radius: 6px; 
            font-size: 11px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Marshal Futsal</h2>
        <span class="subtitle">Panel Pelanggan</span>
        
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="booking.php" class="nav-link active">Pesan Lapangan</a>
            <a href="riwayat.php" class="nav-link">Riwayat Sewa</a>
            <a href="laporan.php" class="nav-link">Laporan Masalah</a>
            
            <div style="margin-top: 50px;">
                <p style="font-size: 11px; color: #475569; margin-left: 15px; margin-bottom: 10px; letter-spacing: 1px;">AKUN</p>
                <a href="../../logout.php" class="nav-link logout">
                Keluar Sistem
            </a>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <h2 style="font-weight: 600; color: #0F172A; margin-bottom: 25px;">Pesan Lapangan Futsal</h2>
        
        <div class="card">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="font-weight: 500; font-size: 14px; color: #475569;">Pilih Lapangan:</label>
                    <select id="select_lapangan" class="input-control">
                        <?php foreach($list_lapangan as $lap): ?>
                            <option value="<?= $lap['id_lapangan'] ?>"><?= $lap['nama_lapangan'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-weight: 500; font-size: 14px; color: #475569;">Pilih Tanggal:</label>
                    <input type="date" id="select_tanggal" class="input-control" 
                            value="<?= $tanggal_pilih ?>" 
                            min="<?= date('Y-m-d') ?>"
                            onchange="window.location.href='booking.php?tanggal=' + this.value">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="font-weight: 500; font-size: 14px; color: #475569;">Jam Mulai:</label>
                    <select id="jam_mulai" class="input-control" onchange="updateJamSelesai()">
                        <?php for($i=8; $i<=21; $i++): $h = str_pad($i, 2, "0", STR_PAD_LEFT).":00"; ?>
                            <option value="<?= $h ?>"><?= $h ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label style="font-weight: 500; font-size: 14px; color: #475569;">Jam Selesai:</label>
                    <select id="jam_selesai" class="input-control"></select>
                </div>
            </div>

            <button onclick="cekKetersediaan()" class="btn-booking">
                Cek Ketersediaan & Hitung Harga
            </button>

            <div id="result_area" style="margin-top: 30px; display: none;">
                <div id="status_info" class="card" style="background: #F8FAFC; text-align: center; border: 2px dashed #E2E8F0; box-shadow: none;"></div>
            </div>
        </div>

        <div class="card">
            <h3 style="font-size: 16px; color: #0F172A; margin-top: 0; margin-bottom: 20px; font-weight: 600;">
                Jadwal Terisi - <?= date('d M Y', strtotime($tanggal_pilih)) ?>
            </h3>
            
            <?php if (count($jadwal_terisi) > 0): ?>
                <table class="table-jadwal">
                    <thead>
                        <tr style="background: #F8FAFC; color: #64748B;">
                            <th>Lapangan</th>
                            <th>Jam Operasional</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_terisi as $j): ?>
                        <tr>
                            <td><b style="color: #0F172A;"><?= $j['nama_lapangan'] ?></b></td>
                            <td style="color: #475569;"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                            <td><span class="badge-booked">SUDAH DI-BOOKING</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94A3B8; font-style: italic; font-size: 14px;">
                    Belum ada jadwal terisi untuk tanggal ini.
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
    function updateJamSelesai() {
        const mulai = parseInt(document.getElementById('jam_mulai').value);
        const selectSelesai = document.getElementById('jam_selesai');
        selectSelesai.innerHTML = "";
        
        for(let i = mulai + 1; i <= 23; i++) {
            let h = i.toString().padStart(2, '0') + ":00";
            let option = document.createElement('option');
            option.value = h;
            option.text = h;
            selectSelesai.appendChild(option);
        }
    }

    async function cekKetersediaan() {
        const lap = document.getElementById('select_lapangan').value;
        const tgl = document.getElementById('select_tanggal').value;
        const mulai = document.getElementById('jam_mulai').value;
        const selesai = document.getElementById('jam_selesai').value;

        try {
            const response = await fetch(`../../api/cek_range_jadwal.php?id_lapangan=${lap}&tanggal=${tgl}&mulai=${mulai}&selesai=${selesai}`);
            const data = await response.json();
            const resArea = document.getElementById('result_area');
            const statusInfo = document.getElementById('status_info');
            resArea.style.display = 'block';

            if(data.available) {
                statusInfo.innerHTML = `
                    <h4 style="color: #16a34a; margin-bottom: 10px; font-weight: 600;">Lapangan Tersedia!</h4>
                    <p style="margin-bottom: 20px; color: #475569; font-size: 14px;">Durasi: <b>${data.durasi} Jam</b> | Total: <b style="color: #0F172A;">Rp ${data.total_harga}</b></p>
                    <button onclick="prosesBooking()" class="btn-booking" style="background: #22C55E;">Konfirmasi Booking</button>
                `;
            } else {
                statusInfo.innerHTML = `
                    <h4 style="color: #dc2626; margin: 0; font-weight: 600;">Maaf, jadwal ini sudah terisi.</h4>
                    <p style="color: #64748B; font-size: 13px; margin-top: 8px;">Silakan pilih jam atau tanggal lain.</p>
                `;
            }
        } catch (error) {
            Swal.fire('Error', 'Gagal mengecek jadwal.', 'error');
        }
    }

    async function prosesBooking() {
        const lap = document.getElementById('select_lapangan').value;
        const tgl = document.getElementById('select_tanggal').value;
        const mulai = document.getElementById('jam_mulai').value;
        const selesai = document.getElementById('jam_selesai').value;

        const formData = new FormData();
        formData.append('id_lapangan', lap);
        formData.append('tgl_main', tgl);
        formData.append('jam_mulai', mulai);
        formData.append('jam_selesai', selesai);

        try {
            const response = await fetch('simpan_booking.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.status === 'success') {
                Swal.fire({ title: 'Berhasil!', text: 'Pesanan Anda telah diterima.', icon: 'success' })
                .then(() => { window.location.reload(); });
            } else {
                Swal.fire('Gagal', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error Sistem', 'Terjadi kesalahan saat memproses data.', 'error');
        }
    }

    window.onload = updateJamSelesai;
</script>

</body>
</html>