<?php
session_start();
session_destroy(); // Menghapus "kunci" login di server
header("Location: login.php"); // Kembali ke halaman login
exit;