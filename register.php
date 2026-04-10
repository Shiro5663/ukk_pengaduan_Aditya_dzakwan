<?php
session_start();
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | E-Aspirasi SD AMYN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 2.5rem;
        }

        input:focus { 
            transform: scale(1.01); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .btn-premium {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            transition: all 0.3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.5);
        }
    </style>
</head>
<body>
    <div class="w-full max-w-lg p-6 animate__animated animate__zoomIn">
        <div class="glass p-10 shadow-2xl">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl mx-auto flex items-center justify-center shadow-lg mb-4 transform rotate-6 hover:rotate-0 transition-all">
                    <i class="fa-solid fa-user-plus text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight italic uppercase">Create Account.</h1>
                <p class="text-slate-500 text-sm mt-1">Daftar akses E-Aspirasi SD AMYN</p>
            </div>

            <form action="" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <input type="number" name="nis" placeholder="NIS (Angka)" required 
                        class="w-full pl-5 pr-5 py-4 rounded-2xl border border-slate-100 outline-none focus:border-blue-500 transition-all font-semibold text-sm bg-white">
                    </div>
                    <div class="relative">
                        <input type="text" name="nama" placeholder="Nama Lengkap" required 
                        class="w-full pl-5 pr-5 py-4 rounded-2xl border border-slate-100 outline-none focus:border-blue-500 transition-all font-semibold text-sm bg-white">
                    </div>
                </div>
                <div class="relative">
                    <input type="password" name="password" placeholder="Buat Password" required 
                    class="w-full pl-5 pr-5 py-4 rounded-2xl border border-slate-100 outline-none focus:border-blue-500 transition-all font-semibold text-sm bg-white">
                </div>
                <button type="submit" name="register" class="btn-premium w-full py-4 text-white rounded-2xl font-bold shadow-lg transition-all active:scale-95 uppercase tracking-widest text-xs">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-slate-200/50">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Sudah punya akun?</span>
                <a href="login.php" class="text-xs font-black text-blue-600 hover:underline ml-1 uppercase tracking-widest">Login Dashboard</a>
            </div>
        </div>
    </div>

    <?php
    if(isset($_POST['register'])) {
        $nis = $_POST['nis'];
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Validasi: Harus angka!
        if(!is_numeric($nis)) {
            echo "<script>Swal.fire({icon:'error', title:'Error!', text:'NIS harus berupa angka!'});</script>";
        } else {
            // Cek NIS Duplicate
            $cek_nis = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
            if(mysqli_num_rows($cek_nis) > 0) {
                echo "<script>Swal.fire({icon:'warning', title:'NIS Terdaftar', text:'Gunakan NIS lain atau silakan login.'});</script>";
            } else {
                $sql = "INSERT INTO siswa (nis, nama, password, role) VALUES ('$nis', '$nama', '$password', 'siswa')";
                if(mysqli_query($conn, $sql)) {
                    echo "<script>Swal.fire({icon:'success', title:'Berhasil!', text:'Akun siap, silakan login'}).then(()=>window.location.href='login.php');</script>";
                } else {
                    // Kalau masih error, SweetAlert bakal nangkep biar nggak muncul layar item lagi
                    $error_msg = mysqli_error($conn);
                    echo "<script>Swal.fire('Error Database!', '$error_msg', 'error');</script>";
                }
            }
        }
    }
    ?>
</body>
</html>