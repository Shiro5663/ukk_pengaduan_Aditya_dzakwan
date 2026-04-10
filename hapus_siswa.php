<?php
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Hapus...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body>

<?php
if (isset($_GET['nis'])) {
    $nis = mysqli_real_escape_string($conn, $_GET['nis']);

    // STEP 1: Hapus dulu semua aspirasi yang pernah dibuat siswa ini
    // Biar gak bentrok sama Foreign Key
    $hapus_aspirasi = mysqli_query($conn, "DELETE FROM input_aspirasi WHERE nis = '$nis'");

    if ($hapus_aspirasi) {
        // STEP 2: Baru deh hapus data siswanya
        $query = "DELETE FROM siswa WHERE nis = '$nis'";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                Swal.fire({
                    title: 'Terhapus!',
                    text: 'Siswa dan semua aspirasinya berhasil dibersihkan.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = 'admin_dashboard.php';
                });
            </script>";
        } else {
            echo "<script>Swal.fire('Gagal!', 'Error Siswa: " . mysqli_error($conn) . "', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('Gagal!', 'Error Aspirasi: " . mysqli_error($conn) . "', 'error');</script>";
    }
} else {
    header("Location: admin_dashboard.php");
}
?>
</body>
</html>