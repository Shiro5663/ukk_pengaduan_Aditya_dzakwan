<?php
include 'koneksi.php';
$id = $_GET['id'];

// Ambil info foto dulu biar bisa dihapus juga dari folder
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM input_aspirasi WHERE id_pelaporan = '$id'"));
if($data['foto'] != "" && file_exists("img/".$data['foto'])){
    unlink("img/".$data['foto']); // Hapus file dari folder
}

$query = mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id'");

if($query){
    echo "<script>alert('Laporan dibatalkan!'); window.location='index.php';</script>";
}
?>