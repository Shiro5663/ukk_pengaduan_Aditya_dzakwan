<?php
session_start();
include 'koneksi.php';

// Proteksi Admin/Petugas
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit;
}

// Query ambil data lengkap
$query = mysqli_query($conn, "SELECT i.*, s.nama, a.status, a.feedback 
                              FROM input_aspirasi i
                              JOIN siswa s ON i.nis = s.nis
                              LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan 
                              ORDER BY i.id_pelaporan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengaduan | Admin E-Aspirasi</title>
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
            overflow-x: hidden;
        }

        .sidebar-premium {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Responsive Sidebar Logic */
        @media (max-width: 1024px) {
            .sidebar-premium { transform: translateX(-100%); }
            .sidebar-premium.active { transform: translateX(0); }
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

        /* Hide Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[45] hidden lg:hidden"></div>

    <aside id="sidebar" class="w-72 h-screen sidebar-premium fixed left-0 top-0 p-8 flex flex-col z-50 lg:translate-x-0">
        <div class="mb-12 relative">
            <button onclick="toggleSidebar()" class="lg:hidden absolute -right-2 top-0 text-slate-400">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <div class="flex items-center gap-3 justify-center mb-2">
                <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                    <i class="fa-solid fa-paper-plane text-white italic"></i>
                </div>
                <h1 class="text-2xl font-black tracking-tighter text-slate-800">ASPIRA<span class="text-blue-600">SI.</span></h1>
            </div>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.3em] text-center">Management Tool</p>
        </div>

        <nav class="flex-1 space-y-3">
            <a href="admin_dashboard.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all">
                <i class="fa-solid fa-house text-sm"></i> Dashboard
            </a>
            <a href="data_pengaduan.php" class="flex items-center gap-4 p-4 bg-white border border-slate-100 shadow-sm text-blue-600 rounded-2xl font-bold transition-all">
                <i class="fa-solid fa-layer-group text-sm"></i> Pengaduan Masuk
            </a>
            <a href="data_siswa.php" class="flex items-center gap-4 p-4 text-slate-500 hover:bg-white/50 rounded-2xl font-semibold transition-all">
                <i class="fa-solid fa-user-group text-sm"></i> Data Siswa
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-100">
            <a href="logout.php" class="flex items-center justify-center gap-2 p-4 bg-red-50 text-red-500 font-black rounded-2xl transition-all hover:bg-red-500 hover:text-white uppercase text-[10px] tracking-widest">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 lg:ml-72 p-6 md:p-12 w-full transition-all">
        
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div class="animate__animated animate__fadeInLeft">
                <div class="flex items-center gap-4 lg:hidden mb-4">
                    <button onclick="toggleSidebar()" class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-600">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <span class="font-black text-slate-800">MENU</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Database <span class="gradient-text">Aspirasi.</span></h2>
                <p class="text-slate-400 font-semibold mt-2 text-sm italic">Kelola laporan dan berikan feedback terbaik.</p>
            </div>
            
            <div class="w-full md:w-auto overflow-x-auto no-scrollbar pb-2 md:pb-0">
                <div class="flex gap-1 bg-slate-100/50 backdrop-blur-md p-1.5 rounded-[1.8rem] border border-white shadow-inner min-w-max">
                    <button onclick="filterTable('all', this)" class="px-6 py-2.5 text-[10px] font-black uppercase rounded-[1.2rem] bg-white shadow-sm transition-all text-blue-600">All</button>
                    <button onclick="filterTable('Menunggu', this)" class="px-6 py-2.5 text-[10px] font-black uppercase text-blue-500 rounded-[1.2rem] hover:bg-white transition-all">Waiting</button>
                    <button onclick="filterTable('Proses', this)" class="px-6 py-2.5 text-[10px] font-black uppercase text-amber-500 rounded-[1.2rem] hover:bg-white transition-all">On Process</button>
                    <button onclick="filterTable('Selesai', this)" class="px-6 py-2.5 text-[10px] font-black uppercase text-emerald-500 rounded-[1.2rem] hover:bg-white transition-all">Finished</button>
                </div>
            </div>
        </header>

        <div class="glass-card p-4 md:p-10 rounded-[2rem] md:rounded-[3.5rem] animate__animated animate__fadeInUp overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[800px]" id="tabelPengaduan">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] border-b border-slate-50">
                            <th class="pb-6 pl-2">Siswa / NIS</th>
                            <th class="pb-6">Lokasi</th>
                            <th class="pb-6">Cuplikan Laporan</th>
                            <th class="pb-6 text-center">Status</th>
                            <th class="pb-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php while($d = mysqli_fetch_assoc($query)) : 
                            $st = $d['status'] ?: 'Menunggu';
                            $badge = ($st == 'Selesai') ? 'bg-emerald-50 text-emerald-500' : (($st == 'Proses') ? 'bg-amber-50 text-amber-500 shadow-sm shadow-amber-100' : 'bg-blue-50 text-blue-500');
                        ?>
                        <tr class="border-b border-slate-50/50 hover:bg-white/40 transition-all table-row" data-status="<?= $st ?>">
                            <td class="py-6 pl-2">
                                <p class="font-extrabold text-slate-800"><?= $d['nama'] ?></p>
                                <p class="text-[10px] text-slate-400 font-bold tracking-widest uppercase"><?= $d['nis'] ?></p>
                            </td>
                            <td class="py-6 font-bold text-slate-500 uppercase text-xs"><?= $d['lokasi'] ?></td>
                            <td class="py-6 max-w-[200px] truncate italic text-slate-400 font-medium">"<?= $d['ket'] ?>"</td>
                            <td class="py-6 text-center">
                                <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest <?= $badge ?>"><?= $st ?></span>
                            </td>
                            <td class="py-6 text-center">
                                <div class="flex justify-center gap-2 md:gap-3">
                                    <button type="button" 
                                        onclick="showDetail('<?= $d['lokasi'] ?>', '<?= addslashes($d['ket']) ?>', '<?= $d['foto'] ?>', '<?= addslashes($d['feedback'] ?: 'Belum ada tanggapan.') ?>')" 
                                        class="w-10 h-10 bg-white border border-slate-100 rounded-xl text-slate-400 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <a href="tanggapi.php?id=<?= $d['id_pelaporan'] ?>" 
                                       class="px-5 py-2.5 bg-slate-800 text-white rounded-xl text-[10px] font-black hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200 uppercase tracking-widest flex items-center">
                                        Respon
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl z-[99] hidden items-center justify-center p-4 md:p-6 animate__animated animate__fadeIn">
        <div class="bg-white/90 max-w-xl w-full rounded-[2.5rem] md:rounded-[3rem] p-6 md:p-10 shadow-2xl scale-95 transition-all border border-white overflow-y-auto max-h-[90vh]" id="modalContent">
            <div class="flex justify-between items-start mb-6 md:mb-8">
                <div>
                    <h2 id="m-lokasi" class="text-2xl md:text-3xl font-black text-slate-800 uppercase tracking-tighter"></h2>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest mt-1 italic">Detail Aspirasi Siswa</p>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-500 hover:text-white transition-all shadow-inner">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <img id="m-foto" src="" class="w-full h-48 md:h-64 object-cover rounded-[2rem] mb-6 md:mb-8 shadow-2xl border-4 border-white">
            
            <div class="space-y-4 mb-8">
                <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Pesan Laporan</p>
                    <p id="m-ket" class="text-slate-600 font-semibold leading-relaxed italic text-sm md:text-base"></p>
                </div>
                <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100">
                    <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] mb-2">Tanggapan Terkirim</p>
                    <p id="m-feedback" class="text-blue-700 font-bold text-xs md:text-sm italic"></p>
                </div>
            </div>

            <button onclick="closeModal()" class="w-full py-5 bg-slate-800 text-white rounded-[1.5rem] font-black hover:bg-blue-600 transition-all shadow-xl uppercase tracking-widest text-[10px]">Tutup Jendela Detail</button>
        </div>
    </div>

    <script>
        // Responsive Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('hidden');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : 'auto';
        }

        // Modal Logic (Tetap Seperti Punya Lu)
        function showDetail(lokasi, ket, foto, feedback) {
            document.getElementById('m-lokasi').innerText = lokasi;
            document.getElementById('m-ket').innerText = '"' + ket + '"';
            document.getElementById('m-feedback').innerText = feedback;
            document.getElementById('m-foto').src = foto ? 'img/' + foto : 'img/default.png';
            
            const modal = document.getElementById('modalDetail');
            const content = document.getElementById('modalContent');
            
            modal.classList.replace('hidden', 'flex');
            setTimeout(() => {
                content.classList.replace('scale-95', 'scale-100');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('modalDetail');
            const content = document.getElementById('modalContent');
            
            content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                modal.classList.replace('flex', 'hidden');
            }, 200);
        }

        // Filter Logic (Fixed Visual Feedback)
        function filterTable(status, btn) {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (status === 'all' || rowStatus === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Update UI Active Button
            const buttons = btn.parentElement.querySelectorAll('button');
            buttons.forEach(b => {
                b.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
                b.classList.add('hover:bg-white');
            });
            btn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
        }
    </script>
</body>
</html>