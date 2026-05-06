<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

function rupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}

$query = mysqli_query($conn, "SELECT * FROM anggaran ORDER BY id DESC");

$totalDataQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM anggaran");
$totalData = mysqli_fetch_assoc($totalDataQuery)['total'] ?? 0;

$totalAnggaranQuery = mysqli_query($conn, "SELECT SUM(nilai_anggaran) AS total FROM anggaran");
$totalAnggaran = mysqli_fetch_assoc($totalAnggaranQuery)['total'] ?? 0;

$anggaranTerbesarQuery = mysqli_query($conn, "SELECT MAX(nilai_anggaran) AS total FROM anggaran");
$anggaranTerbesar = mysqli_fetch_assoc($anggaranTerbesarQuery)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Anggaran</title>

    <link rel="stylesheet" href="Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title h1 {
            color: #1f5c2e;
            font-size: 34px;
            margin-bottom: 6px;
        }

        .page-title p {
            color: #6b7280;
        }

        .search-input {
            width: 320px;
            padding: 14px 18px;
            border-radius: 18px;
            border: 1px solid #d7e2d7;
            outline: none;
            font-size: 14px;
        }

        .table-wrap {
            margin-top: 22px;
            border-radius: 22px;
            overflow: auto;
            background: white;
            max-height: 520px;
        }

        .anggaran-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .anggaran-table th {
            background: #2f7d32;
            color: white;
            padding: 16px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .anggaran-table td {
            padding: 16px;
            text-align: center;
            border-bottom: 1px solid #edf1ed;
            color: #243024;
        }

        .anggaran-table tr:nth-child(even) {
            background: #f8fbf8;
        }

        .summary-grid {
            margin-bottom: 22px;
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
<?php if (($_SESSION['role'] ?? 'user') === 'admin') : ?>

<a href="pengguna.php" class="menu-item <?= ($currentPage == 'pengguna.php') ? 'active' : '' ?>">
    <i class="fa-solid fa-users"></i>
    <span>Pengguna</span>
</a>

<?php endif; ?>
            </nav>
        </div>

        <div class="sidebar-bottom">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                Logout
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-card">

            <div class="page-title">
                <div>
                    <h1>Daftar Anggaran</h1>
                    <p>Data anggaran otomatis diambil dari Data PBJ.</p>
                </div>

                <input 
                    type="text" 
                    id="searchInput" 
                    class="search-input" 
                    placeholder="Cari kelti atau tahun..."
                    onkeyup="searchTable()"
                >
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <p>Total Data</p>
                    <h3><?= $totalData; ?></h3>
                </div>

                <div class="summary-card">
                    <p>Total Anggaran</p>
                    <h3><?= rupiah($totalAnggaran); ?></h3>
                </div>

                <div class="summary-card">
                    <p>Anggaran Terbesar</p>
                    <h3><?= rupiah($anggaranTerbesar); ?></h3>
                </div>
            </div>

            <div class="table-wrap">
                <table class="anggaran-table" id="anggaranTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelti</th>
                            <th>Nilai Anggaran</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($query && mysqli_num_rows($query) > 0) : ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['kelti']); ?></td>
                                    <td><?= rupiah($row['nilai_anggaran']); ?></td>
                                    <td><?= htmlspecialchars($row['tahun']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4">Belum ada data anggaran</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</div>

<script>
function searchTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#anggaranTable tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>