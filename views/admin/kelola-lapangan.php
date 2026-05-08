<?php
session_start();

// Proteksi admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

require_once "../../config/Database.php";
require_once "../../core/Lapangan.php";

$database = new Database();
$db = $database->getConnection();
$lapanganObj = new Lapangan($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $lapanganObj->create($_POST['nama'], $_POST['harga']);
    } elseif (isset($_POST['edit'])) {
        $lapanganObj->update($_POST['id_lapangan'], $_POST['nama'], $_POST['harga'], $_POST['kondisi']);
    }
    header("Location: kelola-lapangan.php");
}

if (isset($_GET['hapus'])) {
    $lapanganObj->delete($_GET['hapus']);
    header("Location: kelola-lapangan.php");
}

$data_lapangan = $lapanganObj->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Lapangan</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            display: flex;
            font-family: 'Poppins', sans-serif;
            background: #F1F5F9;
        }

        .main-content {
            flex: 1;
            margin-left: 260px; 
            padding: 40px 40px 40px 60px; 
            background: #F8FAFC;
            min-height: 100vh;
            width: calc(100% - 260px);
            box-sizing: border-box;
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
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-control {
            flex: 1;
            min-width: 150px;
        }

        .form-control label {
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
            color: #64748B;
        }

        .form-control input,
        .form-control select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-primary {
            background: #22C55E;
            color: white;
        }

        .btn-reset {
            background: #E2E8F0;
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
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .baik {
            background: #DCFCE7;
            color: #166534;
        }

        .rusak {
            background: #FEE2E2;
            color: #991B1B;
        }

        .action-btn {
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
        }

        .edit {
            color: #3B82F6;
        }

        .delete {
            color: #EF4444;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<?php include '../layouts/sidebar.php'; ?>

<!-- MAIN -->
<div class="main-content">

    <div class="header">
        <h2>Kelola Lapangan</h2>

        <div style="text-align:right;">
            <strong><?= $_SESSION['nama'] ?></strong><br>
            <small style="color:#64748B;">Administrator</small>
        </div>
    </div>

    <!-- FORM -->
    <div class="card">
        <h3 id="form-title">Tambah / Edit Lapangan</h3>

        <form method="POST" class="form-group">
            <input type="hidden" name="id_lapangan" id="id_lapangan">

            <div class="form-control">
                <label>Nama Lapangan</label>
                <input type="text" name="nama" id="nama" required>
            </div>

            <div class="form-control">
                <label>Harga / Jam</label>
                <input type="number" name="harga" id="harga" required>
            </div>

            <div class="form-control">
                <label>Kondisi</label>
                <select name="kondisi" id="kondisi">
                    <option value="baik">Baik</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>

            <button type="submit" name="tambah" id="btn-submit" class="btn btn-primary">
                Simpan
            </button>

            <button type="button" onclick="resetForm()" class="btn btn-reset">
                Reset
            </button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($data_lapangan as $lap): ?>
                <tr>
                    <td><?= $lap['id_lapangan'] ?></td>
                    <td><strong><?= $lap['nama_lapangan'] ?></strong></td>
                    <td>Rp <?= number_format($lap['harga'], 0, ',', '.') ?></td>

                    <td>
                        <span class="badge <?= $lap['status_kondisi'] ?>">
                            <?= ucfirst($lap['status_kondisi']) ?>
                        </span>
                    </td>

                    <td>
                        <button class="action-btn edit" onclick='fillEdit(<?= json_encode($lap) ?>)'>
                            Edit
                        </button>
                        |
                        <a href="?hapus=<?= $lap['id_lapangan'] ?>" class="action-btn delete" onclick="return confirm('Hapus lapangan ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

<script>
function fillEdit(data) {
    document.getElementById('form-title').innerText = "Edit: " + data.nama_lapangan;
    document.getElementById('id_lapangan').value = data.id_lapangan;
    document.getElementById('nama').value = data.nama_lapangan;
    document.getElementById('harga').value = data.harga;
    document.getElementById('kondisi').value = data.status_kondisi;

    const btn = document.getElementById('btn-submit');
    btn.name = "edit";
    btn.innerText = "Update";
}

function resetForm() {
    document.getElementById('form-title').innerText = "Tambah / Edit Lapangan";
    document.getElementById('id_lapangan').value = "";
    document.getElementById('nama').value = "";
    document.getElementById('harga').value = "";

    const btn = document.getElementById('btn-submit');
    btn.name = "tambah";
    btn.innerText = "Simpan";
}
</script>

</body>
</html>