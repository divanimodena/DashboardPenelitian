<?php
session_start();
include '../config/koneksi.php';

$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$username = ucfirst($_SESSION['username'] ?? 'Admin');
$jam = date("H");

// Sapaan Waktu
if ($jam >= 5 && $jam < 12) { $greeting = "Good Morning"; } 
elseif ($jam >= 12 && $jam < 15) { $greeting = "Good Afternoon"; } 
elseif ($jam >= 15 && $jam < 18) { $greeting = "Good Evening"; } 
else { $greeting = "Good Night"; }

// ================= LOGIKA FILTER BULAN =================
$filterBulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$whereClause = "";
if ($filterBulan != '') {
    $whereClause = "WHERE MONTH(tanggal) = '$filterBulan'";
}

// ================= LOGIKA 3 CARD =================
// 1. Total Anggaran (Sementara statis, nanti kita buat fitur editnya)
$totalAnggaranPagu = 5000000000; // Contoh: 5 Miliar

// 2. Total Data & 3. Total Realisasi (Berdasarkan Filter Bulan)
$queryStats = mysqli_query($conn, "SELECT COUNT(*) as jumlah_data, SUM(nilai_realisasi) as total_realisasi FROM data_penelitian $whereClause");
$dataStats = mysqli_fetch_assoc($queryStats);

$totalData = $dataStats['jumlah_data'] ?? 0;
$totalRealisasi = $dataStats['total_realisasi'] ?? 0;

// ================= LOGIKA TABEL KELTI =================
$listKelti = [
    'Bioteknologi dan Bioindustri', 
    'Ilmu Tanah dan Agronomi', 
    'Mekanisasi Pasca Panen dan Konservasi Lingkungan', 
    'Pemuliaan Tanaman', 
    'Proteksi Tanaman', 
    'Sosial Ekonomi', 
    'Kelapa'
];

// Asumsi alokasi anggaran per Kelti dibagi rata (Bisa diganti nanti)
$paguPerKelti = $totalAnggaranPagu / 7; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggaran - PPKS</title>
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Desain Tambahan untuk Kebutuhan Baru */
        .topbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 16px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .stat-icon { width: 60px; height: 60px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
        .bg-blue { background: #2196f3; } .bg-green { background: #4caf50; } .bg-gold { background: #f59e0b; }
        .stat-text p { margin: 0; color: #64748b; font-size: 14px; }
        .stat-text h3 { margin: 5px 0 0; font-size: 24px; color: #1e293b; }
        
        .table-section { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .filter-select { padding: 8px 15px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #475569; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .chart-container { width: 60px; height: 60px; margin: 0 auto; }
        .btn-view { background: #e6f4ea; color: #16a34a; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold; transition: 0.2s; display: inline-block; }
        .btn-view:hover { background: #16a34a; color: white; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar">
            <div>
                <p style="margin:0; color:#64748b;">Dashboard Overview</p>
                <h1 style="margin:5px 0; font-size: 28px; color:#1e293b;"><?= $greeting; ?>, <?= htmlspecialchars($username); ?></h1>
                <span style="color:#94a3b8; font-size: 14px;">Sistem manajemen anggaran operasional perusahaan.</span>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div style="width: 40px; height: 40px; background: #20c997; border-radius: 50%; display: flex; justify-content: center; align-items: center; color: white; font-weight: bold;">
                    <?= strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div>
                    <small style="display:block; color:#94a3b8;">Akun</small>
                    <strong><?= htmlspecialchars($username); ?></strong>
                </div>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-blue"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-text">
                    <p>Total Anggaran Induk</p>
                    <h3>Rp <?= number_format($totalAnggaranPagu / 1000000000, 2); ?> M</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-green"><i class="fa-solid fa-folder-open"></i></div>
                <div class="stat-text">
                    <p>Total Data Penelitian</p>
                    <h3><?= $totalData; ?> Dokumen</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-gold"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="stat-text">
                    <p>Total Realisasi</p>
                    <h3>Rp <?= number_format($totalRealisasi / 1000000, 1); ?> Jt</h3>
                </div>
            </div>
        </section>

        <section class="table-section">
            <div class="table-header">
                <h3>Serapan Anggaran per Kelti</h3>
                
                <form action="" method="GET">
                    <select name="bulan" class="filter-select" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        <?php 
                        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        foreach ($namaBulan as $index => $nama) {
                            $angkaBulan = $index + 1;
                            $selected = ($filterBulan == $angkaBulan) ? 'selected' : '';
                            echo "<option value='$angkaBulan' $selected>$nama</option>";
                        }
                        ?>
                    </select>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Nama Kelti</th>
                        <th width="25%">Realisasi / Pagu</th>
                        <th width="20%" style="text-align: center;">Grafik Serapan</th>
                        <th width="15%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($listKelti as $kelti_name): 
                        // Ambil total realisasi per kelti berdasarkan filter bulan
                        $qKelti = mysqli_query($conn, "SELECT SUM(nilai_realisasi) as real_kelti FROM data_penelitian WHERE kelti = '$kelti_name' " . ($filterBulan != '' ? "AND MONTH(tanggal) = '$filterBulan'" : ""));
                        $dKelti = mysqli_fetch_assoc($qKelti);
                        $realisasiKelti = $dKelti['real_kelti'] ?? 0;
                        
                        // Hindari minus jika realisasi lebih besar dari pagu (opsional)
                        $sisaKelti = max(0, $paguPerKelti - $realisasiKelti);
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $kelti_name; ?></strong></td>
                        <td>
                            <span style="color: #f59e0b; font-weight:bold;">Rp <?= number_format($realisasiKelti, 0, ',', '.'); ?></span><br>
                            <small style="color: #94a3b8;">dari Rp <?= number_format($paguPerKelti, 0, ',', '.'); ?></small>
                        </td>
                        <td>
                            <div class="chart-container">
                                <canvas id="chart_<?= $no; ?>"></canvas>
                            </div>
                            <script>
                                new Chart(document.getElementById('chart_<?= $no; ?>'), {
                                    type: 'doughnut',
                                    data: {
                                        labels: ['Terpakai', 'Sisa Anggaran'],
                                        datasets: [{
                                            data: [<?= $realisasiKelti ?>, <?= $sisaKelti ?>],
                                            backgroundColor: ['#f59e0b', '#22c55e'], // Oranye & Hijau
                                            borderWidth: 0,
                                            cutout: '70%'
                                        }]
                                    },
                                    options: { plugins: { legend: { display: false }, tooltip: { enabled: true } }, maintainAspectRatio: false }
                                });
                            </script>
                        </td>
                        <td style="text-align: center;">
                            <a href="detail_kelti.php?kelti=<?= urlencode($kelti_name); ?>" class="btn-view">View Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

    </main>
</div>

</body>
</html>     