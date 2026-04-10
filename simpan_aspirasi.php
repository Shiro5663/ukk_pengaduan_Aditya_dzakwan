<?php
include 'koneksi.php';

// Ambil data dari form
$nis = $_POST['nis'];
$id_kategori = $_POST['id_kategori'];
$lokasi = $_POST['lokasi'];
$ket = $_POST['ket'];

// 1. CEK FOLDER: Buat folder 'img' secara otomatis jika belum ada
if (!file_exists('img')) {
    mkdir('img', 0777, true);
}

// Inisialisasi variabel foto
$fotobaru = NULL;

// 2. PROSES UPLOAD (Jika ada file yang diunggah)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    // Rename foto biar unik: 09042026_namafile.jpg
    $fotobaru = date('dmYHis') . '_' . str_replace(' ', '_', $foto);
    $path = "img/" . $fotobaru;

    // Pindahkan file
    if (!move_uploaded_file($tmp, $path)) {
        // Jika gagal pindah, set null lagi biar query gak error
        $fotobaru = NULL;
    }
}

// 3. QUERY SQL: Sekarang variabel $query dijamin ada isinya
// Pake NULL di SQL kalau fotonya kosong
$query = "INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, foto) 
          VALUES ('$nis', '$id_kategori', '$lokasi', '$ket', " . ($fotobaru ? "'$fotobaru'" : "NULL") . ")";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Laporan berhasil dikirim!'); window.location='index.php';</script>";
} else {
    echo "Error Database: " . mysqli_error($conn);
}
?>