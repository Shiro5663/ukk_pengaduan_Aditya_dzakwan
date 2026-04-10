<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: login.php");
    exit();
}

$nis = $_SESSION['nis'];

// Ambil Statistik (Sama kayak logika lo)
$q_total = mysqli_query($conn, "SELECT id_pelaporan FROM input_aspirasi WHERE nis = '$nis'");
$jml_laporan = ($q_total) ? mysqli_num_rows($q_total) : 0;

$jml_proses = 0; $jml_selesai = 0;
$q_stat = mysqli_query($conn, "SELECT status FROM aspirasi a JOIN input_aspirasi i ON a.id_pelaporan = i.id_pelaporan WHERE i.nis = '$nis'");
if($q_stat) {
    while($row = mysqli_fetch_assoc($q_stat)) {
        if($row['status'] == 'Proses') $jml_proses++;
        if($row['status'] == 'Selesai') $jml_selesai++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa | E-Aspirasi</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.1) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 50%), 
                        #f8fafc;
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
            border-radius: 2.5rem;
        }

        .stat-card {
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .btn-premium {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .gradient-text {
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .input-premium {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 1.2rem;
            transition: all 0.3s;
        }

        .input-premium:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-center mb-16 gap-8 animate__animated animate__fadeIn">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <i class="fa-solid fa-paper-plane text-white italic"></i>
                    </div>
                    <h1 class="text-3xl font-black tracking-tighter text-slate-800 uppercase">Aspira<span class="text-blue-600">si.</span></h1>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] ml-1">Panel Siswa SD AMYN</p>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex gap-3 bg-white/50 p-2 rounded-3xl border border-white shadow-sm">
                    <div class="px-5 py-2 text-center">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</p>
                        <p class="text-lg font-bold text-slate-800"><?= $jml_laporan ?></p>
                    </div>
                    <div class="w-px h-8 bg-slate-200 my-auto"></div>
                    <div class="px-5 py-2 text-center">
                        <p class="text-[8px] font-black text-amber-500 uppercase tracking-widest">Proses</p>
                        <p class="text-lg font-bold text-amber-500"><?= $jml_proses ?></p>
                    </div>
                    <div class="w-px h-8 bg-slate-200 my-auto"></div>
                    <div class="px-5 py-2 text-center">
                        <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">Selesai</p>
                        <p class="text-lg font-bold text-emerald-500"><?= $jml_selesai ?></p>
                    </div>
                </div>
                <a href="logout.php" id="btnLogout" class="w-14 h-14 bg-white text-red-500 rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 hover:bg-red-500 hover:text-white transition-all group">
                    <i class="fa-solid fa-power-off group-hover:rotate-90 transition-transform"></i>
                </a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4" data-aos="fade-up">
                <div class="glass-card p-10 sticky top-10">
                    <div class="mb-8">
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Sampaikan <span class="gradient-text">Suaramu.</span></h2>
                        <p class="text-slate-400 text-xs font-semibold mt-1">Laporanmu membantu sekolah lebih baik.</p>
                    </div>
                    
                    <form id="formAspirasi" onsubmit="event.preventDefault(); kirimForm();" class="space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori Laporan</label>
                            <select id="f_kategori" required class="w-full p-4 input-premium text-sm font-bold text-slate-600 outline-none appearance-none">
                                <option value="" disabled selected>Pilih Kategori...</option>
                                <?php 
                                $kat = mysqli_query($conn, "SELECT * FROM kategori");
                                while($k = mysqli_fetch_assoc($kat)) echo "<option value='".$k['id_kategori']."'>".$k['ket_kategori']."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Titik Lokasi</label>
                            <input type="text" id="f_lokasi" required placeholder="Contoh: Kamar Mandi Lt. 2" class="w-full p-4 input-premium text-sm font-bold text-slate-600 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Isi Aspirasi</label>
                            <textarea id="f_ket" required rows="4" placeholder="Jelaskan secara detail..." class="w-full p-4 input-premium text-sm font-bold text-slate-600 outline-none resize-none"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Lampiran Foto</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-3xl p-6 bg-slate-50/50 text-center hover:border-blue-400 transition-all cursor-pointer relative">
                                <input type="file" id="f_foto" class="absolute inset-0 opacity-0 cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-300 mb-2"></i>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Klik untuk upload foto</p>
                            </div>
                        </div>
                        <button type="submit" id="btnKirim" class="w-full py-5 btn-premium text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-[1.02] active:scale-95">
                            Kirim Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8" data-aos="fade-up" data-aos-delay="100">
                <form action="hapus_masal.php" method="POST" id="formHapus">
                    <div class="glass-card p-10 min-h-[600px]">
                        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-10 gap-6">
                            <div>
                                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Riwayat <span class="gradient-text">Aspirasi.</span></h2>
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <button type="button" onclick="filterStatus('all')" class="px-5 py-2 text-[9px] font-black uppercase tracking-widest bg-white border border-slate-100 rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-sm">Semua</button>
                                    <button type="button" onclick="filterStatus('Menunggu')" class="px-5 py-2 text-[9px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 border border-blue-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all">Waiting</button>
                                    <button type="button" onclick="filterStatus('Selesai')" class="px-5 py-2 text-[9px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-600 hover:text-white transition-all">Finished</button>
                                </div>
                            </div>
                            
                            <button type="submit" id="btnHapusMasal" disabled class="px-6 py-3 bg-red-50 text-red-500 border border-red-100 rounded-2xl text-[10px] font-black uppercase tracking-widest disabled:opacity-30 hover:bg-red-500 hover:text-white transition-all">
                                <i class="fa-solid fa-trash-can mr-2"></i> Hapus Terpilih
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-slate-300 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                                        <th class="pb-6 text-center w-12"><input type="checkbox" id="checkAll" class="w-4 h-4 accent-blue-600"></th>
                                        <th class="pb-6 text-left pl-4">Laporan & Lokasi</th>
                                        <th class="pb-6 text-center">Status</th>
                                        <th class="pb-6 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = mysqli_query($conn, "SELECT i.*, a.status, a.feedback FROM input_aspirasi i LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan WHERE i.nis = '$nis' ORDER BY i.id_pelaporan DESC");
                                    while($d = mysqli_fetch_assoc($res)) {
                                        $st = $d['status'] ?: 'Menunggu';
                                        $badge = ($st == 'Selesai') ? 'bg-emerald-50 text-emerald-500' : (($st == 'Proses') ? 'bg-amber-50 text-amber-500' : 'bg-blue-50 text-blue-500');
                                    ?>
                                    <tr class="border-b border-slate-50/50 hover:bg-white/40 transition-all table-row-data">
                                        <td class="py-6 text-center">
                                            <?php if($st == 'Menunggu'): ?>
                                                <input type="checkbox" name="id_pilih[]" value="<?= $d['id_pelaporan'] ?>" class="w-4 h-4 accent-blue-600 checkbox-item">
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-6 pl-4">
                                            <div class="flex items-center gap-5">
                                                <div class="relative group">
                                                    <img src="img/<?= $d['foto'] ?: 'default.png' ?>" class="w-16 h-16 object-cover rounded-2xl shadow-sm border-2 border-white group-hover:scale-110 transition-transform">
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-black text-slate-800 uppercase text-xs tracking-tight"><?= htmlspecialchars($d['lokasi']) ?></p>
                                                    <p class="text-[11px] text-slate-400 font-medium italic mt-1 truncate w-40 md:w-64">"<?= htmlspecialchars($d['ket']) ?>"</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 text-center">
                                            <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest <?= $badge ?>"><?= $st ?></span>
                                        </td>
                                        <td class="py-6 text-center">
                                            <button type="button" onclick="showDetail('<?= htmlspecialchars($d['lokasi']) ?>', '<?= htmlspecialchars(addslashes($d['ket'])) ?>', '<?= $d['foto'] ?>', '<?= htmlspecialchars(addslashes($d['feedback'] ?: 'Belum ada tanggapan petugas.')) ?>')" class="w-10 h-10 bg-white border border-slate-100 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center mx-auto">
                                                <i class="fa-solid fa-eye-low-vision text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xl z-[99] hidden items-center justify-center p-6 animate__animated animate__fadeIn">
        <div class="bg-white/90 max-w-xl w-full rounded-[3rem] p-10 shadow-2xl scale-95 transition-all border border-white" id="modalContent">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 id="m-lokasi" class="text-3xl font-black text-slate-800 uppercase tracking-tighter"></h2>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mt-1 italic">Detail Aspirasi Saya</p>
                </div>
                <button onclick="closeModal()" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-500 hover:text-white transition-all shadow-inner">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <img id="m-foto" src="" class="w-full h-64 object-cover rounded-[2.5rem] mb-8 shadow-2xl border-4 border-white">
            
            <div class="space-y-4 mb-8">
                <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Laporan Saya</p>
                    <p id="m-ket" class="text-slate-600 font-semibold leading-relaxed italic"></p>
                </div>
                <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100">
                    <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] mb-2">Feedback Sekolah</p>
                    <p id="m-feedback" class="text-blue-700 font-bold text-sm italic"></p>
                </div>
            </div>

            <button onclick="closeModal()" class="w-full py-5 bg-slate-800 text-white rounded-[1.5rem] font-black hover:bg-blue-600 transition-all shadow-xl uppercase tracking-widest text-[10px]">Kembali ke Dashboard</button>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        // AJAX SIMPAN (Improved UI feedback)
        function kirimForm() {
            const btn = document.getElementById('btnKirim');
            const formData = new FormData();
            formData.append('id_kategori', document.getElementById('f_kategori').value);
            formData.append('lokasi', document.getElementById('f_lokasi').value);
            formData.append('ket', document.getElementById('f_ket').value);
            formData.append('foto', document.getElementById('f_foto').files[0]);
            formData.append('nis', '<?= $nis ?>');

            btn.disabled = true; btn.innerHTML = "<i class='fa-solid fa-circle-notch fa-spin mr-2'></i> Memproses...";

            fetch('simpan_aspirasi.php', { method: 'POST', body: formData })
            .then(res => {
                if(res.ok) {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Terkirim!', 
                        text: 'Laporanmu sudah diterima pihak sekolah.', 
                        timer: 2000, 
                        showConfirmButton: false,
                        showClass: { popup: 'animate__animated animate__zoomIn' }
                    })
                    .then(() => window.location.reload());
                }
            });
        }

        // LOGOUT SWEETALERT
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.href;
            Swal.fire({
                title: 'Sudah selesai?',
                text: "Kamu bakal keluar dari panel siswa.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: '<span class="text-xs font-bold uppercase tracking-widest">Ya, Logout</span>',
                cancelButtonText: '<span class="text-xs font-bold uppercase tracking-widest text-slate-400">Batal</span>',
                customClass: { popup: 'rounded-[2rem]' }
            }).then((result) => { if (result.isConfirmed) window.location.href = url; });
        });

        // MODAL FUNCTIONS
        function showDetail(l, k, f, fb) {
            document.getElementById('m-lokasi').innerText = l;
            document.getElementById('m-ket').innerText = '"' + k + '"';
            document.getElementById('m-feedback').innerText = fb;
            document.getElementById('m-foto').src = f ? 'img/'+f : 'img/default.png';
            document.getElementById('modalDetail').classList.replace('hidden', 'flex');
            setTimeout(() => document.getElementById('modalContent').classList.replace('scale-95', 'scale-100'), 10);
        }

        function closeModal() {
            document.getElementById('modalContent').classList.replace('scale-100', 'scale-95');
            setTimeout(() => document.getElementById('modalDetail').classList.replace('flex', 'hidden'), 200);
        }

        // FILTER LOGIC
        function filterStatus(val) {
            document.querySelectorAll('.table-row-data').forEach(row => {
                let badge = row.querySelector('.status-badge') || row.querySelector('span[class*="bg-"]');
                let stTxt = badge.innerText.trim().toLowerCase();
                row.style.display = (val === 'all' || stTxt === val.toLowerCase()) ? 'table-row' : 'none';
            });
        }

        // MULTI DELETE LOGIC
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.checkbox-item');
        const btnHapus = document.getElementById('btnHapusMasal');

        checkAll?.addEventListener('change', function() {
            checkboxes.forEach(c => c.checked = this.checked);
            updateBtn();
        });

        checkboxes.forEach(c => c.addEventListener('change', updateBtn));
        function updateBtn() {
            const count = document.querySelectorAll('.checkbox-item:checked').length;
            btnHapus.disabled = count === 0;
        }
    </script>
</body>
</html>