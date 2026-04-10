<?php
session_start();
include 'koneksi.php';

// Proteksi Admin/Petugas
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit;
}

// Ambil data siswa
$query = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Siswa | SMK Bhakti Insani</title>
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

        @media (max-width: 1023px) {
            .sidebar-premium { transform: translateX(-100%); }
            .sidebar-premium.active { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-slate-50">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[40] hidden lg:hidden transition-opacity"></div>

    <aside id="sidebar" class="w-72 h-screen sidebar-premium fixed left-0 top-0 p-6 flex flex-col z-50 lg:translate-x-0">
        <div class="mb-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="logo.png" alt="Logo" class="w-10 h-10 object-contain"> 
                <div class="flex flex-col">
                    <h1 class="text-xs font-black text-slate-400 uppercase tracking-tighter leading-none">SMK BHAKTI</h1>
                    <h1 class="text-lg font-black text-blue-600 tracking-tighter leading-tight">INSANI.</h1>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden w-8 h-8 flex items-center justify-center bg-slate-100 rounded-lg text-slate-500">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="admin_dashboard.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all group">
                <i class="fa-solid fa-house text-sm group-hover:text-blue-500"></i> Dashboard
            </a>
            <a href="data_pengaduan.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all group">
                <i class="fa-solid fa-layer-group text-sm group-hover:text-blue-500"></i> Pengaduan Masuk
            </a>
            <a href="data_siswa.php" class="flex items-center gap-4 p-4 bg-white border border-slate-100 shadow-sm text-blue-600 rounded-2xl font-bold transition-all">
                <i class="fa-solid fa-user-group text-sm"></i> Data Siswa
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-100">
            <div class="flex items-center gap-3 mb-6 p-2 bg-slate-50 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                     <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-black text-slate-800 truncate"><?= $_SESSION['nama'] ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><?= $_SESSION['role'] ?></p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center justify-center gap-2 p-4 bg-red-50 text-red-500 font-black rounded-2xl hover:bg-red-500 hover:text-white transition-all text-[10px] tracking-widest uppercase shadow-sm">
                <i class="fa-solid fa-power-off"></i> Logout System
            </a>
        </div>
    </aside>

    <main class="lg:ml-72 min-h-screen transition-all duration-300">
        
        <div class="flex items-center justify-between p-4 md:p-8 sticky top-0 bg-white/80 backdrop-blur-md z-30 border-b border-slate-100 lg:border-none lg:bg-transparent">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-600">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full text-[10px] font-black tracking-widest uppercase animate__animated animate__fadeInLeft">
                    <i class="fa-solid fa-circle-check text-[8px]"></i> Sistem Pengaduan Sarana Sekolah
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="hidden md:block text-[10px] font-black text-slate-400 uppercase tracking-widest">SMK Bhakti Insani Bogor</span>
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-blue-600 overflow-hidden">
                    <img src="logo.png" alt="Logo" class="w-6 h-6 object-contain">
                </div>
            </div>
        </div>

        <div class="p-6 md:px-12 md:pb-12 max-w-7xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
                <div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-slate-800 tracking-tighter">Master <span class="gradient-text">Data Siswa.</span></h2>
                    <p class="text-slate-400 font-semibold mt-2 text-sm">Kelola informasi database siswa SMK Bhakti Insani.</p>
                </div>

                <div class="relative w-full md:w-80 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 transition-colors group-focus-within:text-blue-500"></i>
                    <input type="text" id="searchInput" onkeyup="searchStudent()" placeholder="Cari NIS atau Nama..." 
                        class="w-full pl-12 pr-4 py-4 bg-white rounded-2xl border border-slate-100 shadow-sm focus:ring-4 focus:ring-blue-50 outline-none font-bold text-xs text-slate-600 transition-all">
                </div>
            </header>

            <div class="glass-card rounded-[2.5rem] bg-white overflow-hidden animate__animated animate__fadeInUp">
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[800px]">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/30">
                                <th class="px-8 py-6">No.</th>
                                <th class="px-6 py-6">Informasi Siswa</th>
                                <th class="px-6 py-6">NIS (ID)</th>
                                <th class="px-6 py-6">Status Akun</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php 
                            $no = 1;
                            while($s = mysqli_fetch_assoc($query)) : 
                            ?>
                            <tr class="student-row border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6 font-black text-slate-300 group-hover:text-blue-500 transition-colors"><?= $no++ ?>.</td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <p class="font-extrabold text-slate-800 uppercase tracking-tight"><?= $s['nama'] ?></p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Siswa Terdaftar</p>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-[10px] font-black border border-blue-100 tracking-widest"><?= $s['nis'] ?></span>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-50 text-green-600 rounded-full border border-green-100">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                        <span class="text-[9px] font-black uppercase">Aktif</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="hapus_siswa.php?nis=<?= $s['nis'] ?>" 
                                           onclick="return confirm('Yakin ingin menghapus data siswa ini?')" 
                                           class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-red-400 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center group/btn">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="emptyState" class="hidden p-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-user-slash text-2xl text-slate-200"></i>
                    </div>
                    <p class="text-slate-400 font-bold italic">Data siswa tidak ditemukan...</p>
                </div>
            </div>
            
            <footer class="mt-8 text-center">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em]">© 2026 SMK Bhakti Insani - E-Aspirasi System</p>
            </footer>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
        }

        function searchStudent() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            const empty = document.getElementById('emptyState');
            let found = 0;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if(text.includes(filter)) {
                    row.style.display = '';
                    found++;
                } else {
                    row.style.display = 'none';
                }
            });

            empty.style.display = (found === 0) ? 'block' : 'none';
        }
    </script>
</body>
</html>