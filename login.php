<?php
// 1. Session start wajib di paling atas sebelum kode apapun
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

// 2. CEK PROTEKSI REDIRECT (Biar gak muter-muter/infinite loop)
if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'petugas') {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-Aspirasi Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(15px);
            border-radius: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md p-6 animate__animated animate__backInDown">
        <div class="glass-card p-10 md:p-12">
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-blue-600 rounded-3xl mx-auto flex items-center justify-center shadow-2xl mb-6 transform rotate-6 hover:rotate-0 transition-all duration-500">
                    <i class="fa-solid fa-fingerprint text-white text-4xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tighter italic">WELCOME BACK<span class="text-blue-600">.</span></h1>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.4em] mt-2">Portal Aspirasi Siswa</p>
            </div>

            <form action="" method="POST" class="space-y-6">
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="nis" required placeholder="NIS Anda" 
                    class="w-full pl-12 pr-4 py-4 bg-white/50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/20 transition-all font-semibold text-slate-700">
                </div>

                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" required placeholder="Password" 
                    class="w-full pl-12 pr-4 py-4 bg-white/50 border border-slate-100 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/20 transition-all font-semibold text-slate-700">
                </div>

                <button type="submit" name="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-bold shadow-xl hover:shadow-blue-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                    <span>AUTHENTICATE</span>
                    <i class="fa-solid fa-bolt"></i>
                </button>
            </form>

            <div class="mt-10 text-center pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Belum punya akun?</p>
                <a href="register.php" class="text-sm font-black text-blue-600 hover:text-indigo-600 transition-all inline-block mt-2 underline decoration-2 underline-offset-4">DAFTAR DISINI</a>
            </div>
        </div>
    </div>

    <?php
    if(isset($_POST['submit'])) {
        $nis = mysqli_real_escape_string($conn, $_POST['nis']);
        $password = $_POST['password'];
        
        $query = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
        $user = mysqli_fetch_assoc($query);

        // 3. LOGIKA LOGIN SINKRON (Pakai password_verify)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['nis'] = $user['nis'];
            $_SESSION['nama'] = $user['nama'];

            $target = ($user['role'] == 'admin' || $user['role'] == 'petugas') ? 'admin_dashboard.php' : 'index.php';

            echo "<script>
                Swal.fire({
                    icon: 'success', 
                    title: 'LOGIN BERHASIL!', 
                    text: 'Halo " . $user['nama'] . ", mengalihkan ke dashboard...',
                    showConfirmButton: false, 
                    timer: 1500,
                    timerProgressBar: true
                }).then(() => { 
                    window.location.href = '$target'; 
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error', 
                    title: 'AUTHENTICATION FAILED', 
                    text: 'NIS atau Password lu salah, coba cek lagi!',
                    confirmButtonColor: '#2563eb'
                });
            </script>";
        }
    }
    ?>
</body>
</html>