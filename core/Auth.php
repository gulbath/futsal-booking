<?php
class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fungsi login
    public function login($email, $password) {
        // Ambil data user berdasarkan email
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verifikasi password
            // Catatan: Jika saat register menggunakan password_hash, gunakan password_verify di sini
            if ($password === $user['password']) {
                // Set session
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                return $user;
            }
        }
        return false;
    }

    // Fungsi cek apakah sudah login (Proteksi Halaman)
    public static function checkLogin() {
        if (!isset($_SESSION['id_user'])) {
            header("Location: /futsal-booking/login.php");
            exit;
        }
    }

    // Fungsi cek role admin
    public static function checkAdmin() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: /futsal-booking/views/user/booking.php");
            exit;
        }
    }
}
?>