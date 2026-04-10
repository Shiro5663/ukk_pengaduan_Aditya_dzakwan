<?php
/**
 * File Koneksi Database
 * Sesuai dengan Tugas Praktik UKK Paket 3 [cite: 3, 27]
 */

// Konfigurasi Database
$host = "localhost";
$user = "root";      // Default user Laragon/XAMPP
$pass = "";          // Default password Laragon/XAMPP kosong
$db   = "db_pengaduan_sarana"; // Sesuai dengan nama database di phpMyAdmin kamu

// Membuat Koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi (Bagian dari Debugging & Dokumentasi) [cite: 56, 89]
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

/** * Gunakan variabel $conn ini di file:
 * 1. auth.php
 * 2. proses_register.php
 * 3. simpan_aspirasi.php
 * 4. dashboard_admin.php
 */
?>