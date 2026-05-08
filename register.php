<?php
// 1. Koneksi Database
require_once "config/Database.php";

$database = new Database();
$db = $database->getConnection();

$error = "";
$success = "";

// 2. Proses Registrasi saat Form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Disarankan password_hash($password, PASSWORD_DEFAULT) untuk keamanan di produksi

    try {
        // A. Cek apakah email sudah terdaftar
        $checkQuery = "SELECT email FROM users WHERE email = :email LIMIT 1";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            $error = "Email sudah terdaftar! Silakan gunakan email lain atau login.";
        } else {
            // B. Masukkan user baru ke database (default role: user)
            $query = "INSERT INTO users (nama, email, password, role) VALUES (:nama, :email, :password, 'user')";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password); // Pakai hash jika di produksi

            if ($stmt->execute()) {
                // Berhasil, arahkan ke login dengan pesan sukses
                header("Location: login.php?pesan=registrasi_berhasil");
                exit;
            } else {
                $error = "Gagal mendaftarkan akun. Silakan coba lagi.";
            }
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
    <title>Daftar Akun - Rent-it</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { background: #F8FAFC; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        h2 { color: #0F172A; margin-bottom: 25px; text-align: center; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #E2E8F0; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; background: #22C55E; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        button:hover { background: #16a34a; }
        .login-link { display: block; text-align: center; margin-top: 20px; font-size: 14px; color: #64748B; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Daftar Akun</h2>
        <form action="register.php" method="POST">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Daftar Sekarang</button>
        </form>
        <a href="login.php" class="login-link">Sudah punya akun? <span style="color: #22C55E;">Login</span></a>
    </div>
</body>
</html>