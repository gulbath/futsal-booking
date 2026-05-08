<?php
// 1. Inisialisasi Session di baris paling atas
session_start();

require_once "config/Database.php";

$database = new Database();
$db = $database->getConnection();

$error = "";
$success = "";

// Cek pesan dari URL (misal setelah registrasi atau paksaan login)
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'wajib_login') {
        $error = "Silakan login terlebih dahulu untuk melakukan pemesanan.";
    } else if ($_GET['pesan'] == 'registrasi_berhasil') {
        $success = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
    }
}

// 2. PROTEKSI: Jika user sudah login, langsung arahkan ke halaman yang sesuai
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: views/admin/dashboard.php");
    } else {
        header("Location: views/user/booking.php");
    }
    exit;
}

// 3. Proses Login saat Form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifikasi Password
            if ($password == $user['password']) {
                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);

                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // 4. Pengalihan halaman berdasarkan Role
                if ($user['role'] == 'admin') {
                    header("Location: views/admin/dashboard.php");
                } else {
                    header("Location: views/user/booking.php");
                }
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email belum terdaftar! Silakan daftar akun baru terlebih dahulu.";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Marshal Futsal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background: #F8FAFC; 
            margin: 0; 
            font-family: 'Poppins', sans-serif; 
        }
        .login-card { 
            width: 100%; 
            max-width: 400px; 
            padding: 40px; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            box-sizing: border-box;
        }
        .login-card h2 {
            text-align: center;
            color: #0F172A;
            margin-bottom: 8px;
        }
        .login-card p.subtitle {
            text-align: center;
            color: #64748B;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .error-box {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #FECACA;
        }
        .success-box {
            background: #DCFCE7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #BBF7D0;
        }
        .input-group { margin-bottom: 20px; }
        .input-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            font-size: 14px; 
            color: #334155;
        }
        .input-group input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #E2E8F0; 
            border-radius: 8px; 
            box-sizing: border-box; 
            transition: 0.3s;
            outline: none;
        }
        .input-group input:focus { 
            border-color: #0F172A; 
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }
        .btn-login { 
            width: 100%; 
            padding: 14px; 
            background: #0F172A; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-login:hover { 
            background: #1E293B; 
            transform: translateY(-1px);
        }
        .register-link {
            text-align: center; 
            margin-top: 25px; 
            font-size: 14px; 
            color: #64748B;
            border-top: 1px solid #F1F5F9;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Marshal Login</h2>
        <p class="subtitle">Masuk untuk memesan lapangan</p>

        <?php if($error): ?>
            <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="success-box"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label>Alamat Email</label>
                <input type="email" name="email" required placeholder="nama@email.com">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn-login">Masuk ke Sistem</button>
        </form>

        <div class="register-link">
            Belum punya akun? <br> 
            <a href="register.php" style="color: #0F172A; font-weight: bold; text-decoration: none;">Daftar Sekarang</a>
        </div>
    </div>

</body>
</html>