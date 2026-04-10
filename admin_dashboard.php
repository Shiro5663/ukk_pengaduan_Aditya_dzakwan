<?php
session_start();
include 'koneksi.php';

// Proteksi
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit;
}

// 1. QUERY HITUNG STATISTIK 
$total = mysqli_num_rows(mysqli_query($conn, "SELECT id_pelaporan FROM input_aspirasi"));
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT a.id_pelaporan FROM aspirasi a RIGHT JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan WHERE a.status IS NULL OR a.status = 'Menunggu'"));
$selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_pelaporan FROM aspirasi WHERE status = 'Selesai'"));

// 2. QUERY AMBIL 5 ASPIRASI TERBARU
$query_terbaru = mysqli_query($conn, "SELECT i.*, s.nama, a.status FROM input_aspirasi i 
    JOIN siswa s ON i.nis = s.nis 
    LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan 
    ORDER BY i.id_pelaporan DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Admin | E-Aspirasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.15) 0, transparent 50%), 
                        #f8fafc;
            min-height: 100vh;
        }

        .sidebar-premium {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }

        .gradient-text {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Mobile Logic */
        @media (max-width: 1023px) {
            .sidebar-premium {
                transform: translateX(-100%);
            }
            .sidebar-premium.active {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-slate-50">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[40] hidden lg:hidden transition-opacity"></div>

    <aside id="sidebar" class="w-72 h-screen sidebar-premium fixed left-0 top-0 p-6 flex flex-col z-50 lg:translate-x-0">
        <div class="mb-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-paper-plane text-white italic"></i>
                </div>
                <h1 class="text-xl font-black text-slate-800 tracking-tighter">ASPIRA<span class="text-blue-600">SI.</span></h1>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 flex items-center justify-center bg-slate-100 rounded-lg text-slate-500">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="admin_dashboard.php" class="flex items-center gap-4 p-4 bg-white border border-slate-100 shadow-sm text-blue-600 rounded-2xl font-bold transition-all">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="data_pengaduan.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all group">
                <i class="fa-solid fa-layer-group group-hover:text-blue-500"></i> Pengaduan Masuk
            </a>
            <a href="data_siswa.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all group">
                <i class="fa-solid fa-user-group group-hover:text-blue-500"></i> Data Siswa
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-100">
            <div class="flex items-center gap-3 mb-6 px-2">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-600 border-2 border-white shrink-0">
                    <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-black text-slate-800 truncate"><?= $_SESSION['nama'] ?? 'Admin' ?></p>
                    <p class="text-[9px] text-slate-400 font-bold tracking-widest uppercase">Admin System</p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center justify-center gap-2 p-4 bg-red-50 text-red-500 font-black rounded-2xl hover:bg-red-500 hover:text-white transition-all text-[10px] tracking-widest uppercase">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </aside>

    <main class="lg:ml-72 min-h-screen transition-all duration-300">
        
        <div class="lg:hidden flex items-center justify-between p-4 sticky top-0 bg-white/80 backdrop-blur-md z-30 border-b border-slate-100">
            <button onclick="toggleSidebar()" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-600">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <h1 class="text-lg font-black text-slate-800">ASPIRA<span class="text-blue-600">SI.</span></h1>
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold">
                 <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
            </div>
        </div>

        <div class="p-6 md:p-10 max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Overview <span class="gradient-text">Statistik.</span></h2>
                    <p class="text-slate-400 font-semibold mt-1 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day text-blue-400"></i> <span id="real-date">Loading date...</span>
                    </p>
                </div>
                
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="hidden sm:flex px-6 py-3 bg-white rounded-2xl shadow-sm border border-slate-100 items-center gap-3">
                        <i class="fa-regular fa-clock text-blue-500"></i>
                        <span id="real-clock" class="text-lg font-black text-slate-800 tracking-tighter">00:00:00</span>
                    </div>
                    <button class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 text-slate-400 relative ml-auto md:ml-0">
                        <i class="fa-solid fa-bell"></i>
                        <?php if($pending > 0): ?>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 border-2 border-white rounded-full text-[9px] text-white font-black flex items-center justify-center">
                            <?= $pending ?>
                        </span>
                        <?php endif; ?>
                    </button>
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                <div class="glass-card p-6 rounded-[2rem] bg-white border-l-4 border-l-blue-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Masuk</p>
                    <h3 class="text-4xl font-black text-slate-800 mt-2 italic"><?= $total ?></h3>
                </div>

                <div class="glass-card p-6 rounded-[2rem] bg-white border-l-4 border-l-amber-500">
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Butuh Respon</p>
                    <h3 class="text-4xl font-black text-slate-800 mt-2 italic"><?= $pending ?></h3>
                </div>

                <div class="glass-card p-6 rounded-[2rem] bg-white border-l-4 border-l-emerald-500 sm:col-span-2 lg:col-span-1">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Selesai</p>
                    <h3 class="text-4xl font-black text-slate-800 mt-2 italic"><?= $selesai ?></h3>
                </div>
            </div>

            <div class="glass-card rounded-[2rem] bg-white overflow-hidden">
                <div class="p-6 md:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h4 class="text-xl font-black text-slate-800 italic">Recent <span class="text-blue-600">Activity.</span></h4>
                    <a href="data_pengaduan.php" class="w-full sm:w-auto px-6 py-3 bg-slate-800 text-white text-center rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">View All</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="px-8 pb-4">Siswa</th>
                                <th class="px-6 pb-4">Lokasi</th>
                                <th class="px-6 pb-4">Status</th>
                                <th class="px-8 pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php while($row = mysqli_fetch_assoc($query_terbaru)) : 
                                $st = $row['status'] ?? 'Menunggu';
                                $badge = "text-blue-500 bg-blue-50";
                                if($st == 'Selesai') $badge = "text-emerald-500 bg-emerald-50";
                                if($st == 'Proses') $badge = "text-amber-500 bg-amber-50";
                            ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-all">
                                <td class="px-8 py-5">
                                    <p class="font-bold text-slate-800"><?= $row['nama'] ?></p>
                                    <p class="text-[10px] text-slate-400">NIS: <?= $row['nis'] ?></p>
                                </td>
                                <td class="px-6 py-5 text-slate-500 font-medium">
                                    <?= $row['lokasi'] ?>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $badge ?>"><?= $st ?></span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button onclick="openModal('<?= $row['lokasi'] ?>', '<?= addslashes($row['ket']) ?>', '<?= $row['foto'] ?>')" 
                                            class="w-8 h-8 bg-slate-100 rounded-lg text-slate-400 hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl z-[100] hidden items-center justify-center p-4">
        <div class="bg-white max-w-lg w-full rounded-[2.5rem] p-6 shadow-2xl overflow-hidden scale-95 transition-all duration-300" id="modalContent">
            <div class="flex justify-between items-center mb-6">
                <h2 id="m-lokasi" class="text-xl font-black text-slate-800 italic"></h2>
                <button onclick="closeModal()" class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-500 hover:text-white transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <img id="m-foto" src="" class="w-full h-48 object-cover rounded-2xl mb-4 border border-slate-100 shadow-sm">
            
            <div class="bg-slate-50 p-4 rounded-2xl mb-6">
                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Keterangan:</p>
                <p id="m-ket" class="text-slate-600 text-sm leading-relaxed italic"></p>
            </div>

            <button onclick="closeModal()" class="w-full py-3 bg-slate-800 text-white rounded-xl font-black text-[10px] uppercase tracking-widest">Tutup</button>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('hidden');
            
            if(sidebar.classList.contains('active')) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        }

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            if(document.getElementById('real-clock')) document.getElementById('real-clock').innerText = `${h}:${m}:${s}`;
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('real-date').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        function openModal(lokasi, ket, foto) {
            document.getElementById('m-lokasi').innerText = lokasi;
            document.getElementById('m-ket').innerText = '"' + ket + '"';
            document.getElementById('m-foto').src = foto ? 'img/' + foto : 'img/default.png';
            document.getElementById('modalDetail').classList.remove('hidden');
            document.getElementById('modalDetail').classList.add('flex');
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('scale-95');
                document.getElementById('modalContent').classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            document.getElementById('modalContent').classList.remove('scale-100');
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => {
                document.getElementById('modalDetail').classList.remove('flex');
                document.getElementById('modalDetail').classList.add('hidden');
            }, 200);
        }
    </script>
</body>
</html>