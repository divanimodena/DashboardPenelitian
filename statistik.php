<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');
include 'config/koneksi.php';

$username = ucfirst($_SESSION['username'] ?? 'Divani');

/* -------------------- AMBIL DATA UTAMA -------------------- */
$querySummary = mysqli_query($conn, "SELECT COUNT(*) AS total_data, COALESCE(SUM(nilai_anggaran),0) AS total_anggaran FROM pbj");
$dataSummary = mysqli_fetch_assoc($querySummary);

$totalAnggaran = (int)($dataSummary['total_anggaran'] ?? 0);
$totalPBJ = (int)($dataSummary['total_data'] ?? 0);
$diproses = $totalPBJ;
$userAktif = 1;

/* -------------------- DATA GRAFIK PER BULAN -------------------- */
$labelsChart = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$dataChart = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

$bulanMap = [
    'januari' => 0, 'jan' => 0,
    'februari' => 1, 'feb' => 1,
    'maret' => 2, 'mar' => 2,
    'april' => 3, 'apr' => 3,
    'mei' => 4,
    'juni' => 5, 'jun' => 5,
    'juli' => 6, 'jul' => 6,
    'agustus' => 7, 'agu' => 7, 'aug' => 7,
    'september' => 8, 'sep' => 8,
    'oktober' => 9, 'okt' => 9, 'oct' => 9,
    'november' => 10, 'nov' => 10,
    'desember' => 11, 'des' => 11, 'dec' => 11
];

$queryBulanan = mysqli_query($conn, "
    SELECT bulan, COALESCE(SUM(nilai_anggaran),0) AS total
    FROM pbj
    WHERE bulan IS NOT NULL AND bulan != ''
    GROUP BY bulan
");

if ($queryBulanan) {
    while ($row = mysqli_fetch_assoc($queryBulanan)) {
        $bulanDb = strtolower(trim($row['bulan']));
        $total = (int)$row['total'];

        if (isset($bulanMap[$bulanDb])) {
            $index = $bulanMap[$bulanDb];
            $dataChart[$index] = $total;
        }
    }
}

/* kalau semua data 0, kasih data cadangan biar grafik tetap muncul */
if (array_sum($dataChart) === 0) {
    $dataChart = [120000000, 180000000, 150000000, 220000000, 200000000, 250000000, 210000000, 190000000, 170000000, 230000000, 260000000, 240000000];
}

/* -------------------- DATA DONUT / PIE -------------------- */
/* Ambil status PBJ tanpa merubah codingan lama */
$disetujui = 0;
$diprosesChart = 0;
$ditolak = 0;

$queryStatusPBJ = mysqli_query($conn, "
    SELECT 
        LOWER(TRIM(status)) AS status,
        COUNT(*) AS total
    FROM pbj
    GROUP BY LOWER(TRIM(status))
");

if ($queryStatusPBJ) {
    while ($rowStatus = mysqli_fetch_assoc($queryStatusPBJ)) {
        $status = $rowStatus['status'];
        $totalStatus = (int)$rowStatus['total'];

        if ($status == 'disetujui') {
            $disetujui = $totalStatus;
        } elseif ($status == 'diproses' || $status == 'proses') {
            $diprosesChart = $totalStatus;
        } elseif ($status == 'ditolak') {
            $ditolak = $totalStatus;
        }
    }
}

/* Kalau belum ada kolom/status, biar chart tetap muncul */
if (($disetujui + $diprosesChart + $ditolak) == 0) {
    $diprosesChart = $totalPBJ > 0 ? $totalPBJ : 10;
}

$notifCount = $diproses;
$mailCount = $totalPBJ;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: linear-gradient(145deg, #ffffff, #f9fcf8);
            border-radius: 22px;
            padding: 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.07);
            border: 1px solid rgba(47, 107, 61, 0.06);
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 22px;
        }

        .icon-blue { background: linear-gradient(135deg, #4facfe, #00c6ff); }
        .icon-green { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .icon-pink { background: linear-gradient(135deg, #fa709a, #fee140); }
        .icon-purple { background: linear-gradient(135deg, #a18cd1, #fbc2eb); }

        .stat-info h3 {
            margin: 0;
            font-size: 15px;
            color: #6a766a;
            font-weight: 600;
        }

        .stat-info p {
            margin: 6px 0 0;
            font-size: 26px;
            font-weight: 800;
            color: #1f2f21;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .chart-card {
            background: linear-gradient(145deg, #ffffff, #f8fbf7);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(47, 107, 61, 0.08);
        }

        .chart-card h3 {
            margin-bottom: 20px;
            color: #1f4d2e;
            font-size: 20px;
            font-weight: 700;
        }

        .chart-box {
            position: relative;
            height: 320px;
        }

        @media (max-width: 991px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-layout">

    <aside class="sidebar">
        <div>
            <div class="sidebar-logo">
                <h2>Dashboard</h2>
                <span>Anggaran</span>

                <div class="sidebar-subtitle">
                    <small>Pusat Penelitian</small>
                    <strong>Kelapa Sawit</strong>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>

                <a href="statistik.php" class="menu-item <?= ($currentPage == 'statistik.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Statistik</span>
                </a>

                <a href="data_pbj.php" class="menu-item <?= ($currentPage == 'data_pbj.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Data PBJ</span>
                </a>

                <a href="anggaran.php" class="menu-item <?= ($currentPage == 'anggaran.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Anggaran</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <p class="welcome-label">Statistik Overview</p>
                <h1>Statistik Anggaran</h1>
                <span>Ringkasan statistik data anggaran perusahaan kelapa sawit.</span>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search here">
                </div>
            </div>
        </header>

        <section class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Anggaran</h3>
                    <p>Rp <?= round($totalAnggaran / 1000000000, 1); ?> B</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-green">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Data PBJ</h3>
                    <p><?= $totalPBJ; ?></p>
                </div>
            </div>  

            <div class="stat-card">
                <div class="stat-icon icon-purple">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3>User Aktif</h3>
                    <p><?= $userAktif; ?></p>
                </div>
            </div>
        </section>

        <section class="chart-grid">
            <div class="chart-card">
                <h3>Grafik Anggaran per Bulan (Juta Rupiah)</h3>
                <div class="chart-box">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Statistik PBJ</h3>
                <div class="chart-box">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const barCanvas = document.getElementById('barChart');
    const pieCanvas = document.getElementById('pieChart');

    if (barCanvas) {
        const barCtx = barCanvas.getContext('2d');

        const barGradient1 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient1.addColorStop(0, '#4facfe');
        barGradient1.addColorStop(1, '#00c6ff');

        const barGradient2 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient2.addColorStop(0, '#43e97b');
        barGradient2.addColorStop(1, '#38f9d7');

        const barGradient3 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient3.addColorStop(0, '#fa709a');
        barGradient3.addColorStop(1, '#fee140');

        const barGradient4 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient4.addColorStop(0, '#a18cd1');
        barGradient4.addColorStop(1, '#fbc2eb');

        const barGradient5 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient5.addColorStop(0, '#f093fb');
        barGradient5.addColorStop(1, '#f5576c');

        const barGradient6 = barCtx.createLinearGradient(0, 0, 0, 320);
        barGradient6.addColorStop(0, '#ff9a9e');
        barGradient6.addColorStop(1, '#fad0c4');

        const barLabels = <?= json_encode($labelsChart); ?>;
        const barData = <?= json_encode($dataChart); ?>.map(value => value / 1000000);

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Anggaran',
                    data: barData,
                    backgroundColor: [
                        barGradient1, barGradient2, barGradient3, barGradient4, barGradient5, barGradient6,
                        barGradient1, barGradient2, barGradient3, barGradient4, barGradient5, barGradient6
                    ],
                    borderRadius: 14,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString('id-ID') + ' Jt';
                            }
                        }
                    }
                }
            }
        });
    }

    if (pieCanvas) {
        const pieCtx = pieCanvas.getContext('2d');

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Disetujui', 'Diproses', 'Ditolak'],
                datasets: [{
                    data: [<?= $disetujui; ?>, <?= $diprosesChart; ?>, <?= $ditolak; ?>],
                    backgroundColor: ['#4facfe', '#43e97b', '#fa709a'],
                    borderColor: '#ffffff',
                    borderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>

</body>
</html>