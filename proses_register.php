<?php
include 'koneksi.php';

// Ambil data dari form register
$nis = $_POST['nis'];
$kelas = $_POST['kelas'];
$password = $_POST['password'];

// Enkripsi password biar aman (Best Practice)
$password_aman = password_hash($password, PASSWORD_DEFAULT);

// Masukkan ke database sesuai tabel Siswa [cite: 68, 69]
$query = "INSERT INTO siswa (nis, kelas, password) VALUES ('$nis', '$kelas', '$password_aman')";

if (mysqli_query($conn, $query)) {
    echo "<script>
            alert('Registrasi Berhasil! Silahkan Login.');
            window.location='login.php';
          </script>";
} else {
    echo "Gagal Registrasi: " . mysqli_error($conn);
}
?>