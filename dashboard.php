<?php
session_start();
include 'config/koneksi.php';

$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$username = ucfirst($_SESSION['username'] ?? 'Divani');
$userLogin = $username;

$jam = date("H");
if ($jam >= 5 && $jam < 12) {
    $greeting = "Good Morning";
} elseif ($jam >= 12 && $jam < 15) {
    $greeting = "Good Afternoon";
} elseif ($jam >= 15 && $jam < 18) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

/* ambil aktivitas terbaru dari tabel aktivitas */
mysqli_query($conn, "DELETE FROM aktivitas WHERE waktu < NOW() - INTERVAL 1 MINUTE");
$aktivitasQuery = mysqli_query($conn, "SELECT * FROM aktivitas ORDER BY waktu DESC LIMIT 5");

/* total anggaran dan jumlah PBJ */
$query = mysqli_query($conn, "SELECT SUM(nilai_anggaran) as total, COUNT(*) as jumlah FROM pbj");
$data = mysqli_fetch_assoc($query);

$totalAnggaran = $data['total'] ?? 0;
$totalPBJ = $data['jumlah'] ?? 0;

$permintaanDiproses = 4;

$notifCount = $permintaanDiproses;
$mailCount = $totalPBJ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggaran</title>
    <link rel="stylesheet" href="assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

            <nav class="sidebar-menu" id="sidebarMenu">
                <a href="dashboard.php" class="menu-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>" data-search="home dashboard">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>

                <a href="statistik.php" class="menu-item <?= ($currentPage == 'statistik.php') ? 'active' : '' ?>" data-search="statistik grafik chart data">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Statistik</span>
                </a>

                <a href="data_pbj.php" class="menu-item <?= ($currentPage == 'data_pbj.php') ? 'active' : '' ?>" data-search="data pbj pengadaan">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Data PBJ</span>
                </a>

                <a href="anggaran.php" class="menu-item <?= ($currentPage == 'anggaran.php') ? 'active' : '' ?>" data-search="anggaran budget dana">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Anggaran</span>
                </a>

                <?php if (($_SESSION['role'] ?? 'user') === 'admin') : ?>
<a href="pengguna.php" class="menu-item <?= ($currentPage == 'pengguna.php') ? 'active' : '' ?>" data-search="pengguna user akun">
    <i class="fa-solid fa-user-group"></i>
    <span>Pengguna</span>
</a>
<?php endif; ?>

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
                <p class="welcome-label">Dashboard Overview</p>
                <h1><?= $greeting; ?>, <?= htmlspecialchars($username); ?></h1>
                <span>Selamat datang di sistem anggaran perusahaan kelapa sawit.</span>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="dashboardSearch" placeholder="Search here">
                </div>

                <div class="top-actions">
                    <div class="action-dropdown">
                        <button type="button" class="icon-btn" id="notifBtn">
                            <i class="fa-regular fa-bell"></i>
                        </button>

                        <div class="dropdown-box" id="notifDropdown">
                            <div class="dropdown-header">Notifikasi</div>
                            <div class="dropdown-item">Pengajuan anggaran baru</div>
                            <div class="dropdown-item">Data PBJ diperbarui</div>
                            <div class="dropdown-item">Reminder laporan</div>
                        </div>
                    </div>

                    <div class="action-dropdown">
                        <button type="button" class="icon-btn" id="mailBtn">
                            <i class="fa-regular fa-envelope"></i>
                        </button>

                        <div class="dropdown-box" id="mailDropdown">
                            <div class="dropdown-header">Pesan</div>
                            <div class="dropdown-item">Pesan dari Admin</div>
                            <div class="dropdown-item">Pesan dari Tim</div>
                        </div>
                    </div>

                    <?php if ($currentPage == 'dashboard.php') : ?>
                        <div class="profile-chip">
                            <div class="profile-avatar">
                                <?= strtoupper(substr($username, 0, 1)); ?>
                            </div>
                            <div class="profile-info">
                                <small>Account</small>
                                <span><?= htmlspecialchars($username); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <section class="stats-grid" id="statsGrid">
            <a href="anggaran.php" class="stat-card link-card" data-search="total anggaran budget dana">
                <div class="stat-icon green">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="stat-text">
                    <p>Total Anggaran</p>
                    <h3>Rp <?= round($totalAnggaran / 1000000000, 1); ?> B</h3>
                    <span>+12% dibanding bulan lalu</span>
                </div>
            </a>

            <a href="data_pbj.php" class="stat-card link-card" data-search="data pbj pengadaan">
                <div class="stat-icon olive">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div class="stat-text">
                    <p>Total Data PBJ</p>
                    <h3><?= $totalPBJ; ?></h3>
                    <span>Data berhasil direkap</span>
                </div>
            </a>

            <a href="statistik.php" class="stat-card link-card" data-search="permintaan diproses proses">
                <div class="stat-icon gold">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div class="stat-text">
                    <p>Permintaan Diproses</p>
                    <h3><?= $permintaanDiproses; ?></h3>
                    <span>Proses berjalan minggu ini</span>
                </div>
            </a>
        </section>

        <div class="bottom-grid">

            <div class="activity-card">
                <div class="card-header">
                    <h3>Aktivitas Terbaru</h3>
                    <span><?= date('d M Y'); ?></span>
                </div>

                <div class="activity-list">
                    <?php /*
                    <div class="activity-item">
                        <div class="activity-avatar">📊</div>
                        <div>
                            <strong>Tambah Anggaran</strong>
                            <p>Divani menambahkan data anggaran</p>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-avatar">📁</div>
                        <div>
                            <strong>Data PBJ</strong>
                            <p>Data PBJ berhasil direkap</p>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-avatar">👤</div>
                        <div>
                            <strong>Pengguna</strong>
                            <p>Pengguna baru ditambahkan</p>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-avatar">⏳</div>
                        <div>
                            <strong>Permintaan</strong>
                            <p>Permintaan sedang diproses</p>
                        </div>
                    </div>
                    */ ?>

                    <?php if ($aktivitasQuery && mysqli_num_rows($aktivitasQuery) > 0): ?>
   <?php while ($row = mysqli_fetch_assoc($aktivitasQuery)) : ?>
    <div class="activity-item" data-waktu="<?= $row['waktu']; ?>">
            <div class="activity-avatar">
                <?= htmlspecialchars($row['ikon']); ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($row['judul']); ?></strong>
                <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                <small style="color:#888;">
                    <?php
                    $waktu = strtotime($row['waktu']);
                    $selisih = time() - $waktu;

                    if ($selisih < 60) {
                        echo "Baru saja";
                    } elseif ($selisih < 3600) {
                        echo floor($selisih / 60) . " menit lalu";
                    } elseif ($selisih < 86400) {
                        echo floor($selisih / 3600) . " jam lalu";
                    } else {
                        echo date('d M Y H:i', $waktu);
                    }
                    ?>
                </small>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="activity-item">
        <div class="activity-avatar">📌</div>
        <div>
            <strong>Belum ada aktivitas</strong>
            <p>Aktivitas akan muncul di sini</p>
        </div>
    </div>
<?php endif; ?>

<div class="activity-item">
    <div class="activity-avatar">👤</div>
    <div>
        <strong>Pengguna</strong>
        <p>Pengguna baru ditambahkan</p>
    </div>
</div>

<div class="activity-item">
    <div class="activity-avatar">⏳</div>
    <div>
        <strong>Permintaan</strong>
        <p>Permintaan sedang diproses</p>
    </div>
</div>
                </div>
            </div>

            <div class="quick-card">
                <div class="card-header">
                    <h3>Aksi Cepat</h3>
                </div>
   <div class="quick-actions">
    <a href="tambah_anggaran.php" class="action-btn">
        <i class="fa-solid fa-plus"></i> Tambah Anggaran
    </a>

    <a href="tambah_pbj.php" class="action-btn">
        <i class="fa-solid fa-database"></i> Data PBJ
    </a>

    <?php if (($_SESSION['role'] ?? 'user') === 'admin') : ?>
        <a href="tambah_pengguna.php" class="action-btn">
            <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
        </a>
    <?php endif; ?>

    <a href="laporan.php" class="action-btn">
        <i class="fa-solid fa-file-lines"></i> Laporan Penelitian
    </a>
</div>
</div>
            </div>
        </div>
    </main>
</div>

<script>
const searchInput = document.getElementById('dashboardSearch');

searchInput.addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase().trim();

    const menuItems = document.querySelectorAll('.menu-item');
    const cards = document.querySelectorAll('.link-card');

    menuItems.forEach(item => {
        const text = (item.dataset.search || '').toLowerCase();
        item.style.display = (keyword === '' || text.includes(keyword)) ? 'flex' : 'none';
    });

    cards.forEach(card => {
        const text = (card.dataset.search || '').toLowerCase();
        card.style.display = (keyword === '' || text.includes(keyword)) ? 'flex' : 'none';
    });
});
</script>
</body>
</html>