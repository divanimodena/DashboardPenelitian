<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include '../config/koneksi.php';

/* -------------------- LOGIKA SUMMARY UTAMA -------------------- */
$querySummary = mysqli_query($conn, "SELECT COUNT(*) AS total_data, SUM(nilai_anggaran) AS total_anggaran FROM pbj");
$dataSum = mysqli_fetch_assoc($querySummary);

// Sisa Saldo (Mekanisme Debet: Total Anggaran - Yang Sudah Disetujui)
$queryPaid = mysqli_query($conn, "SELECT SUM(nilai_anggaran) AS total_keluar FROM pbj");$dataPaid = mysqli_fetch_assoc($queryPaid);
$sisaSaldo = ($dataSum['total_anggaran'] ?? 0) - ($dataPaid['total_keluar'] ?? 0);

/* -------------------- DATA DRILL-DOWN KELTI -------------------- */
// Mengambil rekap total per Kelti
$queryKelti = mysqli_query($conn, "SELECT kelti, SUM(nilai_anggaran) AS total_kelti, COUNT(*) AS jumlah_judul FROM pbj GROUP BY kelti");

// Cek apakah ada Kelti yang sedang diklik (Selected)
$keltiSelected = isset($_GET['view_kelti']) ? mysqli_real_escape_string($conn, $_GET['view_kelti']) : null;
$detailData = [];

if ($keltiSelected) {
    $queryDetail = mysqli_query($conn, "SELECT judul, nilai_anggaran, status FROM pbj WHERE kelti = '$keltiSelected'");
    while ($row = mysqli_fetch_assoc($queryDetail)) {
        $detailData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Summary Kelti</title>
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .stats-cards { display: flex; gap: 15px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 18px; padding: 15px 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.05); border: 1px solid #eee; min-width: 280px; }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; justify-content: center; align-items: center; color: white; font-size: 20px; }
        .icon-green { background: #2f7d32; } .icon-gold { background: #ffb300; }
        .stat-info h3 { margin: 0; font-size: 13px; color: #666; }
        .stat-info p { margin: 2px 0 0; font-size: 19px; font-weight: 700; color: #333; }

        .drill-down-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card-table { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .card-table h2 { font-size: 18px; color: #1f4d2e; margin-bottom: 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #888; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #f9f9f9; font-size: 14px; }
        
        .row-kelti { cursor: pointer; transition: 0.2s; }
        .row-kelti:hover { background: #f1f8f1; }
        .row-active { background: #e8f5e9; border-left: 4px solid #2f7d32; }
        
        .badge { padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .badge-paid { background: #e8f5e9; color: #2e7d32; }
        .badge-pending { background: #fff8e1; color: #f57f17; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div>
            <div class="sidebar-logo"><h2>Dashboard</h2><span>Kelapa Sawit</span></div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item"><i class="fa-solid fa-house"></i><span>Home</span></a>
                <a href="summary.php" class="menu-item active"><i class="fa-solid fa-chart-pie"></i><span>Summary</span></a>
                <a href="data_pbj.php" class="menu-item"><i class="fa-solid fa-folder-open"></i><span>Data PBJ</span></a>
            </nav>
        </div>
        <div class="sidebar-bottom"><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></div>
    </aside>

    <main class="main-content">
        <header class="topbar" style="margin-bottom: 25px;">
            <div class="topbar-left">
                <h1>Analisis Anggaran Kelti</h1>
                <span>Klik pada nama Kelti untuk melihat rincian penelitian.</span>
            </div>
        </header>

        <section class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-info">
                    <h3>Sisa Saldo Kas Digital</h3>
                    <p>Rp <?= number_format($sisaSaldo, 0, ',', '.'); ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-gold"><i class="fa-solid fa-layer-group"></i></div>
                <div class="stat-info">
                    <h3>Total Judul Penelitian</h3>
                    <p><?= $dataSum['total_data']; ?> Dokumen</p>
                </div>
            </div>
        </section>

        <section class="drill-down-container">
            <div class="card-table">
                <h2>Rekap per Kelompok Keahlian</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Kelompok Keahlian (Kelti)</th>
                            <th>Total Anggaran</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($k = mysqli_fetch_assoc($queryKelti)): ?>
                        <tr class="row-kelti <?= ($keltiSelected == $k['kelti']) ? 'row-active' : '' ?>" 
                            onclick="window.location='summary.php?view_kelti=<?= urlencode($k['kelti']) ?>'">
                            <td><strong><?= $k['kelti'] ?></strong><br><small><?= $k['jumlah_judul'] ?> Judul</small></td>
                            <td>Rp <?= number_format($k['total_kelti'], 0, ',', '.') ?></td>
                            <td><i class="fa-solid fa-chevron-right" style="color: #ccc;"></i></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-table">
                <h2>Rincian Judul: <?= $keltiSelected ?: '<span style="color:#ccc;">Pilih Kelti...</span>' ?></h2>
                <?php if($keltiSelected): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Judul Penelitian</th>
                            <th>Anggaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($detailData as $d): ?>
                        <tr>
                            <td><?= $d['judul'] ?></td>
                            <td>Rp <?= number_format($d['nilai_anggaran'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= ($d['status'] == 'Disetujui' || $d['status'] == 'Paid') ? 'badge-paid' : 'badge-pending' ?>">
                                    <?= $d['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px 0; color: #999;">
                        <i class="fa-solid fa-mouse-pointer" style="font-size: 40px; margin-bottom: 10px;"></i>
                        <p>Silakan klik salah satu Kelti di sebelah kiri<br>untuk melihat rincian anggaran.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

</body>
</html>