<?php
session_start();

// Aktifkan error reporting untuk debugging selama pengerjaan UKK
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Proteksi halaman: Hanya admin yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | E-Aspirasi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { 
            background: #0f172a; 
            background-image: radial-gradient(circle at top right, #1e293b, #0f172a); 
            color: white; 
            min-height: 100vh; 
        }
        .glass-card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 24px; 
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.37); 
        }
    </style>
</head>
<body class="p-4 md:p-8">
    
    <div class="flex justify-between items-center mb-8" data-aos="fade-down">
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tighter uppercase text-blue-400">Admin Panel</h1>
            <p class="text-gray-400 text-xs md:text-sm">Manajemen Pengaduan Sarana Sekolah - Paket 3</p>
        </div>
        <a href="logout.php" class="px-5 py-2 bg-red-500/20 hover:bg-red-600 text-white text-xs font-bold rounded-full transition-all border border-red-500/50">LOGOUT</a>
    </div>

    <div class="glass-card p-4 md:p-8" data-aos="zoom-in">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 border-b border-white/10 uppercase text-[10px] md:text-xs tracking-widest">
                        <th class="pb-4 px-2">Pelapor (NIS)</th>
                        <th class="pb-4 px-2">Kategori</th>
                        <th class="pb-4 px-2">Lokasi</th>
                        <th class="pb-4 px-2 w-1/3">Keterangan</th>
                        <th class="pb-4 px-2 text-center">Aksi & Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php
                    // Query untuk mengambil data gabungan sesuai Gambar Kerja
                    $sql = "SELECT i.id_pelaporan, i.nis, i.lokasi, i.ket, k.ket_kategori, a.status 
                            FROM input_aspirasi i 
                            INNER JOIN kategori k ON i.id_kategori = k.id_kategori 
                            LEFT JOIN aspirasi a ON i.id_kategori = a.id_kategori";
                    
                    $res = mysqli_query($conn, $sql);

                    if ($res && mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)) {
                    ?>
                    <tr class="hover:bg-white/5 transition-all group text-xs md:text-sm">
                        <td class="py-4 px-2 font-mono text-blue-300"><?= htmlspecialchars($row['nis']) ?></td>
                        <td class="py-4 px-2">
                            <span class="bg-blue-500/20 text-blue-200 px-2 py-1 rounded-md border border-blue-500/30 text-[10px]">
                                <?= htmlspecialchars($row['ket_kategori']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-2 text-gray-300"><?= htmlspecialchars($row['lokasi']) ?></td>
                        <td class="py-4 px-2 text-gray-400 italic font-light">"<?= htmlspecialchars($row['ket']) ?>"</td>
                        <td class="py-4 px-2">
                            <form action="update_status.php" method="POST" class="flex items-center justify-center gap-2">
                                <input type="hidden" name="id_aspirasi" value="<?= $row['id_pelaporan'] ?>">
                                <select name="status" class="bg-slate-800 border border-white/20 rounded-lg text-[10px] p-1.5 outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="Menunggu" <?= ($row['status'] == 'Menunggu') ? 'selected' : '' ?> class="text-white">Menunggu</option>
                                    <option value="Proses" <?= ($row['status'] == 'Proses') ? 'selected' : '' ?> class="text-white">Proses</option>
                                    <option value="Selesai" <?= ($row['status'] == 'Selesai') ? 'selected' : '' ?> class="text-white">Selesai</option>
                                </select>
                                <button type="submit" class="p-1.5 bg-blue-600 hover:bg-blue-500 rounded-lg transition-all active:scale-90 shadow-lg shadow-blue-900/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' class='py-10 text-center text-gray-500 italic text-sm'>Belum ada pengaduan yang masuk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>