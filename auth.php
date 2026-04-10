<?php
session_start();
include 'koneksi.php';

$user_id = $_POST['user_id']; // Ini NIS
$password = $_POST['password'];

// Cek hanya ke satu tabel: siswa
$query = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$user_id'");
$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['role'] = $user['role']; // Mengambil role ('admin' atau 'siswa')
    $_SESSION['nis']  = $user['nis'];

    if ($user['role'] == 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: index.php");
    }
} else {
    echo "<script>alert('Login Gagal!'); window.location='login.php';</script>";
}
?>