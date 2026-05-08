<?php
session_start();
// Jika sudah login, langsung arahkan ke dashboard masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: views/admin/dashboard.php");
    } else {
        header("Location: views/user/booking.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rent-it Futsal - Booking Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), url('https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80');
            background-size: cover;
            color: white;
            text-align: center;
        }
        .btn-start {
            padding: 15px 30px;
            background: #22C55E;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-start:hover { transform: scale(1.1); background: #16a34a; }
    </style>
</head>
<body>
    <div class="hero">
        <h1 style="font-family: 'Poppins'; font-size: 3rem;">SISTEM BOOKING FUTSAL</h1>
        <p style="font-size: 1.2rem; max-width: 600px;">Pantau jadwal lapangan secara real-time dan pesan tempat Anda dengan mudah.</p>
        <a href="login.php" class="btn-start">Mulai Booking Sekarang</a>
    </div>
</body>
</html>