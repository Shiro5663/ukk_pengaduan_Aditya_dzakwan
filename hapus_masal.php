<?php
session_start();
include 'koneksi.php';

// Pastikan yang akses adalah siswa yang login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['id_pilih']) && is_array($_POST['id_pilih'])) {
    $ids = $_POST['id_pilih'];

    foreach ($ids as $id) {
        // 1. Ambil nama foto dulu buat dihapus dari folder img
        $id = mysqli_real_escape_string($conn, $id);
        $q_foto = mysqli_query($conn, "SELECT foto FROM input_aspirasi WHERE id_pelaporan = '$id'");
        $data = mysqli_fetch_assoc($q_foto);

        if ($data && $data['foto'] && $data['foto'] != 'default.png') {
            $path = "img/" . $data['foto'];
            if (file_exists($path)) {
                unlink($path); // Hapus file fotonya
            }
        }

        // 2. Hapus data dari tabel database
        // Karena id_pelaporan biasanya punya relasi (foreign key) ke tabel aspirasi, 
        // kita hapus dulu di tabel aspirasi (tanggapan) baru di input_aspirasi.
        mysqli_query($conn, "DELETE FROM aspirasi WHERE id_pelaporan = '$id'");
        mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id'");
    }

    header("Location: index.php?status=deleted");
} else {
    header("Location: index.php?status=none");
}
exit();
?>