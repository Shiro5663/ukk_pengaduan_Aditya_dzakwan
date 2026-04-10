<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman (Opsional, sesuaikan logic lu)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit;
}

// Ambil NIS dari URL
if (!isset($_GET['nis'])) {
    header("Location: data_siswa.php");
    exit;
}

$nis = mysqli_real_escape_string($conn, $_GET['nis']);

// Ambil data siswa lama
$query = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
$data = mysqli_fetch_assoc($query);

// Variabel untuk SweetAlert
$status_update = '';
$error_msg = '';

// Proses Update
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    // Password tidak di-hash (sesuai request lu sebelumnya), kosongkan mysqli_real_escape biar tanda baca aman
    $password_baru = $_POST['password']; 

    if (!empty($password_baru)) {
        // Jika password diisi, update nama & password
        $sql = "UPDATE siswa SET nama = '$nama', password = '$password_baru' WHERE nis = '$nis'";
    } else {
        // Jika password kosong, update nama saja
        $sql = "UPDATE siswa SET nama = '$nama' WHERE nis = '$nis'";
    }

    if (mysqli_query($conn, $sql)) {
        $status_update = 'sukses';
    } else {
        $status_update = 'gagal';
        $error_msg = mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa | E-Aspirasi Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0, transparent 50%), 
                        radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.1) 0, transparent 50%),
                        #f8fafc;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .input-premium:focus {
            background: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 md:p-8">

    <?php if ($status_update == 'sukses'): ?>
        <script>
            Swal.fire({
                title: 'Data Berhasil Diperbarui!',
                text: 'Perubahan pada siswa <?= $nama ?> sudah disimpan.',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500, // Hilang dalam 1.5 detik
                timerProgressBar: true,
                background: '#ffffff',
                iconColor: '#2563eb', // Warna biru biar match
                customClass: {
                    popup: 'rounded-[2rem]',
                    title: 'font-black text-slate-800 tracking-tight',
                    htmlContainer: 'text-slate-500 font-medium'
                },
                // Efek masuk pake Animate.css
                showClass: { popup: 'animate__animated animate__zoomIn' },
                // Efek keluar
                hideClass: { popup: 'animate__animated animate__fadeOut' }
            }).then(() => {
                window.location.href = 'data_siswa.php'; // Ganti ke halaman tabel siswa lu
            });
        </script>
    <?php elseif ($status_update == 'gagal'): ?>
        <script>
            Swal.fire({
                title: 'Waduh, Gagal Update!',
                text: 'Error Database: <?= $error_msg ?>',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: '<span class="text-xs font-bold uppercase tracking-widest px-4">Mengerti</span>',
                customClass: { popup: 'rounded-[2rem]' }
            });
        </script>
    <?php endif; ?>

    <div class="max-w-xl w-full animate__animated animate__fadeIn">
        
        <a href="data_siswa.php" class="inline-flex items-center gap-3 text-slate-500 font-bold text-sm mb-8 hover:text-blue-600 transition-all group">
            <div class="w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </div>
            Kembali ke Database
        </a>

        <div class="glass-card rounded-[3rem] p-10 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
            
            <header class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-100/50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-4 border-2 border-white shadow-inner">
                    <i class="fa-solid fa-user-pen text-2xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter uppercase">Edit <span class="text-blue-600">Siswa.</span></h2>
                <p class="text-slate-400 text-sm font-semibold mt-1">Perbarui informasi akun siswa dengan aman</p>
            </header>

            <form action="" method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-4">NIS (Nomor Induk Siswa)</label>
                    <input type="text" value="<?= $data['nis'] ?? '' ?>" readonly 
                           class="w-full p-5 rounded-[1.5rem] input-premium font-bold text-slate-400 cursor-not-allowed uppercase tracking-widest text-xs">
                    <p class="text-[9px] text-amber-500 font-bold mt-2 ml-4 italic">* NIS adalah kunci utama, tidak dapat diubah.</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-4">Nama Lengkap Siswa</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="nama" value="<?= $data['nama'] ?? '' ?>" required placeholder="Masukkan Nama Lengkap"
                               class="w-full p-5 pl-14 rounded-[1.5rem] input-premium font-extrabold text-slate-800 placeholder:text-slate-300">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-4">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="password" placeholder="Kosongkan jika tidak ingin ganti"
                               class="w-full p-5 pl-14 rounded-[1.5rem] input-premium font-extrabold text-slate-800 placeholder:text-slate-300">
                    </div>
                    <p class="text-[9px] text-slate-400 font-medium mt-2 ml-4 italic">* Password tidak akan di-hash/acak (Sesuai request lu).</p>
                </div>

                <div class="pt-4">
                    <button type="submit" name="update" 
                            class="w-full py-5 bg-slate-800 text-white rounded-[1.8rem] font-black hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 uppercase tracking-widest text-xs flex items-center justify-center gap-3 active:scale-95">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>