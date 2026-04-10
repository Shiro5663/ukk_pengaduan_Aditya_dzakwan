<?php
include 'koneksi.php';

$id_aspirasi = $_POST['id_aspirasi'];
$status = $_POST['status'];
// Feedback bisa ditambah jika kamu membuat input teks di halaman admin [cite: 78]

$query = "UPDATE aspirasi SET status = '$status' WHERE id_aspirasi = '$id_aspirasi'";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Status berhasil diperbarui!'); window.location='dashboard_admin.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>