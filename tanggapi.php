<?php
session_start();
include 'koneksi.php';

// Ambil ID dari URL
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data aspirasi yang mau ditanggapi
$query = mysqli_query($conn, "SELECT * FROM input_aspirasi WHERE id_pelaporan = '$id'");
$data = mysqli_fetch_assoc($query);

// Cek apakah sudah ada tanggapan sebelumnya di tabel aspirasi
$cek_tanggapan = mysqli_query($conn, "SELECT * FROM aspirasi WHERE id_pelaporan = '$id'");
$tanggapan_lama = mysqli_fetch_assoc($cek_tanggapan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Tanggapan | Admin E-Aspirasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-8">

    <div class="max-w-4xl mx-auto animate__animated animate__fadeIn">
        <a href="data_pengaduan.php" class="group text-blue-600 font-bold text-sm mb-6 inline-flex items-center hover:gap-2 transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke List
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Informasi Laporan</h3>
                
                <div class="relative group mb-6">
                    <img src="img/<?= $data['foto'] ?: 'default.png' ?>" class="w-full h-56 object-cover rounded-3xl border-4 border-slate-50 shadow-inner">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-bold text-slate-800 shadow-sm">
                        <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i> <?= $data['lokasi'] ?>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                        <p class="text-sm text-slate-700 leading-relaxed italic">"<?= $data['ket'] ?>"</p>
                    </div>
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Waktu Laporan</p>
                            <p class="text-xs font-bold text-slate-700">Terdaftar di Sistem</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-6">Proses Laporan</h3>
                
                <form action="" method="POST" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Update Status</label>
                        <div class="relative">
                            <select name="status" class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-sm appearance-none transition-all">
                                <option value="Menunggu" <?= ($tanggapan_lama['status'] ?? '') == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="Proses" <?= ($tanggapan_lama['status'] ?? '') == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                <option value="Selesai" <?= ($tanggapan_lama['status'] ?? '') == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-5 top-5 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Tanggapan / Jawaban</label>
                        <textarea name="feedback" rows="5" placeholder="Berikan instruksi atau jawaban ke siswa..." 
                        class="w-full p-5 bg-slate-50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 text-sm leading-relaxed transition-all resize-none"><?= $tanggapan_lama['feedback'] ?? '' ?></textarea>
                    </div>

                    <button type="submit" name="submit_tanggapan" class="group w-full py-4 bg-slate-900 text-white rounded-2xl font-black shadow-lg shadow-slate-900/20 hover:bg-blue-600 hover:shadow-blue-600/30 transition-all active:scale-95 uppercase tracking-widest text-[10px] flex items-center justify-center gap-2">
                        Simpan Perubahan <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['submit_tanggapan'])) {
        $status = $_POST['status'];
        $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

        // Cek apakah data sudah ada di tabel aspirasi
        $cek = mysqli_query($conn, "SELECT * FROM aspirasi WHERE id_pelaporan = '$id'");
        
        if (mysqli_num_rows($cek) > 0) {
            $sql = "UPDATE aspirasi SET status = '$status', feedback = '$feedback' WHERE id_pelaporan = '$id'";
        } else {
            $sql = "INSERT INTO aspirasi (id_pelaporan, status, feedback) VALUES ('$id', '$status', '$feedback')";
        }

        if (mysqli_query($conn, $sql)) {
            echo "
            <script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Tanggapan sudah diperbarui di dashboard siswa.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    iconColor: '#2563eb',
                    customClass: {
                        popup: 'rounded-[2.5rem]',
                        title: 'font-extrabold text-slate-800',
                        htmlContainer: 'text-slate-500 font-medium'
                    }
                }).then(() => {
                    window.location.href = 'data_pengaduan.php';
                });
            </script>";
        } else {
            $error_db = mysqli_error($conn);
            echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Simpan',
                    text: 'Error: $error_db',
                    confirmButtonColor: '#2563eb'
                });
            </script>";
        }
    }
    ?>
</body>
</html>