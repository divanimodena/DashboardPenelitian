<?php
session_start();
include '../config/koneksi.php';

// Variabel ini yang akan dibaca oleh sidebar.php untuk menentukan menu mana yang 'active'
$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

$username = ucfirst($_SESSION['username'] ?? 'User');
$jam = date("H");

// Logika Waktu/Sapaan
if ($jam >= 5 && $jam < 12) {
    $greeting = "Good Morning";
} elseif ($jam >= 12 && $jam < 15) {
    $greeting = "Good Afternoon";
} elseif ($jam >= 15 && $jam < 18) {
    $greeting = "Good Evening";
} else {
    $greeting = "Good Night";
}

/* 1. Ambil Aktivitas Terbaru */
mysqli_query($conn, "DELETE FROM aktivitas WHERE waktu < NOW() - INTERVAL 1 MINUTE");
$aktivitasQuery = mysqli_query($conn, "SELECT * FROM aktivitas ORDER BY waktu DESC LIMIT 5");

/* 2. Total Anggaran, Jumlah PBJ, dan Rata-rata */
$queryStats = mysqli_query($conn, "SELECT SUM(nilai_anggaran) as total, COUNT(*) as jumlah, AVG(nilai_anggaran) as rata FROM pbj");
$dataStats = mysqli_fetch_assoc($queryStats);

$totalAnggaran = $dataStats['total'] ?? 0;
$totalPBJ = $dataStats['jumlah'] ?? 0;
$rataAnggaran = $dataStats['rata'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggaran - PPKS</title>
    <link rel="stylesheet" href="../Assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* --- MENU SIDEBAR --- */
        .sidebar { display: flex !important; flex-direction: column !important; justify-content: flex-start !important; }
        .sidebar-menu { margin-top: 20px !important; display: flex !important; flex-direction: column !important; gap: 5px !important; flex-grow: 0 !important; }
        .menu-item { margin-bottom: 0 !important; padding: 10px 15px !important; display: flex !important; align-items: center !important; line-height: 1.2 !important; }
        .menu-item i { width: 25px; }
        .sidebar-bottom { margin-top: auto !important; padding-top: 20px; }

        /* --- AKSI CEPAT --- */
        .quick-actions-container { display: flex; flex-direction: column; gap: 12px; margin-top: 15px; }
        .quick-btn-custom { color: white !important; text-decoration: none !important; display: flex !important; align-items: center; padding: 14px; border-radius: 14px; font-weight: 600; border: none !important; transition: opacity 0.2s ease-in-out; }
        .quick-btn-custom:hover { opacity: 0.9; }
        .quick-btn-custom .icon-wrapper { width: 35px; text-align: center; }
        .btn-tambah { background: linear-gradient(135deg, #4facfe, #00f2fe) !important; }
        .btn-data   { background: linear-gradient(135deg, #2c3e50, #4ca1af) !important; }
        .btn-export { background: linear-gradient(135deg, #10b981, #059669) !important; }
        .btn-import { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }

        /* --- KOTAK NOTIFIKASI & PESAN --- */
        .action-dropdown { position: relative !important; }
        .dropdown-box { display: none; position: absolute; top: 45px !important; right: -10px !important; min-width: 250px !important; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 12px; z-index: 1000; overflow: hidden; border: 1px solid #f1f5f9; }
        .dropdown-box.show { display: block; }
        .dropdown-header { padding: 12px 15px; font-weight: bold; border-bottom: 1px solid #f1f5f9; background: #f8fafc; color: #334155; font-size: 14px; }
        .dropdown-item { padding: 12px 15px; font-size: 13px; border-bottom: 1px solid #f8fafc; cursor: pointer; color: #475569; transition: background 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dropdown-item:hover { background: #f1f5f9; }

        /* --- FIX POP-UP TERPOTONG --- */
        .topbar, .topbar-right, .top-actions { overflow: visible !important; }
    </style>
</head>
<body>

<?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
    <div id="custom-toast" class="custom-toast">
        <div class="toast-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="toast-content">
            <strong>Berhasil Tersimpan!</strong>
            <p>Data penelitian baru telah masuk ke dalam sistem.</p>
        </div>
        <button class="toast-close" onclick="closeToast()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <style>
        /* Desain Pesan Kustom */
        .custom-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            background: #ffffff;
            border-left: 6px solid #16a34a; /* Warna hijau PPKS */
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 16px 24px;
            gap: 16px;
            z-index: 9999;
            /* Animasi masuk dan keluar otomatis */
            animation: slideIn 0.5s ease-out forwards, fadeOut 0.5s ease-in 4s forwards;
        }
        .toast-icon { color: #16a34a; font-size: 28px; }
        .toast-content strong { color: #1f2937; display: block; font-size: 16px; font-weight: 600; }
        .toast-content p { margin: 0; color: #6b7280; font-size: 13px; margin-top: 4px; }
        .toast-close { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 18px; margin-left: 10px; transition: 0.3s; }
        .toast-close:hover { color: #1f2937; }

        @keyframes slideIn { 
            from { transform: translateX(120%); opacity: 0; } 
            to { transform: translateX(0); opacity: 1; } 
        }
        @keyframes fadeOut { 
            from { transform: translateX(0); opacity: 1; } 
            to { transform: translateX(120%); opacity: 0; } 
        }
    </style>

    <script>
        // Fungsi untuk menutup manual dan membersihkan URL
        function closeToast() {
            document.getElementById('custom-toast').style.display = 'none';
            // Menghapus ?status=sukses dari URL agar jika di-refresh pesannya tidak muncul lagi
            window.history.replaceState(null, null, window.location.pathname);
        }

        // Otomatis membersihkan URL setelah animasi fadeOut selesai (4.5 detik)
        setTimeout(() => {
            if(document.getElementById('custom-toast')) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        }, 4500);
    </script>
    <?php endif; ?>

<div class="dashboard-layout">
    
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        
        <header class="topbar">
            <div class="topbar-left">
                <p class="welcome-label">Dashboard Overview</p>
                <h1><?= $greeting; ?>, <?= htmlspecialchars($username); ?></h1>
                <span>Sistem manajemen anggaran operasional perusahaan.</span>
            </div>

            <div class="topbar-right">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="dashboardSearch" placeholder="Cari fitur...">
                </div>
                
                <div class="top-actions">
                    
                    <div class="action-dropdown">
                        <button class="icon-btn" id="notifBtn"><i class="fa-regular fa-bell"></i></button>
                        <div class="dropdown-box" id="notifDropdown">
                            <div class="dropdown-header">Notifikasi Terbaru</div>
                            <?php 
                            $notifQuery = mysqli_query($conn, "SELECT * FROM aktivitas ORDER BY waktu DESC LIMIT 3");
                            if ($notifQuery && mysqli_num_rows($notifQuery) > 0): 
                                while ($notif = mysqli_fetch_assoc($notifQuery)):
                            ?>
                                <div class="dropdown-item">
                                    <?= htmlspecialchars($notif['ikon']); ?> <?= htmlspecialchars($notif['judul']); ?>
                                </div>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <div class="dropdown-item" style="color: #94a3b8; text-align: center;">Belum ada notifikasi</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="action-dropdown">
                        <button class="icon-btn" id="mailBtn"><i class="fa-regular fa-envelope"></i></button>
                        <div class="dropdown-box" id="mailDropdown">
                            <div class="dropdown-header">Kotak Masuk</div>
                            <div class="dropdown-item">👤 Admin: Mohon cek data terbaru.</div>
                            <div class="dropdown-item">👤 Sistem: Backup berhasil.</div>
                        </div>
                    </div>

                    <div class="profile-chip">
                        <div class="profile-avatar"><?= strtoupper(substr($username, 0, 1)); ?></div>
                        <div class="profile-info">
                            <small>Akun</small>
                            <span><?= htmlspecialchars($username); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card link-card" data-search="anggaran dana total">
                <div class="stat-icon green"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-text">
                    <p>Total Anggaran</p>
                    <h3>Rp <?= number_format($totalAnggaran / 1000000000, 2); ?> M</h3>
                    <span>Seluruh data masuk</span>
                </div>
            </div>
            <div class="stat-card link-card" data-search="data pbj dokumen">
                <div class="stat-icon olive"><i class="fa-solid fa-folder-open"></i></div>
                <div class="stat-text">
                    <p>Total Data PBJ</p>
                    <h3><?= $totalPBJ; ?> Dokumen</h3>
                    <span>Data tersimpan di sistem</span>
                </div>
            </div>
            <div class="stat-card link-card" data-search="rata rata anggaran">
                <div class="stat-icon gold"><i class="fa-solid fa-chart-pie"></i></div>
                <div class="stat-text">
                    <p>Rata-rata Anggaran</p>
                    <h3>Rp <?= number_format($rataAnggaran / 1000000, 1); ?> Jt</h3>
                    <span>Rata-rata per pengajuan</span>
                </div>
            </div>
        </section>

        <div class="bottom-grid">
            <div class="activity-card">
                <div class="card-header">
                    <h3>Aktivitas Terbaru</h3>
                    <span><?= date('d M Y'); ?></span>
                </div>
                <div class="activity-list">
                    <?php if ($aktivitasQuery && mysqli_num_rows($aktivitasQuery) > 0): ?>
                        <?php 
                        mysqli_data_seek($aktivitasQuery, 0); // Reset pointer query
                        while ($row = mysqli_fetch_assoc($aktivitasQuery)) : 
                        ?>
                            <div class="activity-item">
                                <div class="activity-avatar"><?= htmlspecialchars($row['ikon']); ?></div>
                                <div>
                                    <strong><?= htmlspecialchars($row['judul']); ?></strong>
                                    <p><?= htmlspecialchars($row['deskripsi']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-avatar">📌</div>
                            <div>
                                <strong>Belum ada aktivitas</strong>
                                <p>Sistem siap menerima input baru.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="quick-card">
                <div class="card-header">
                    <h3>Aksi Cepat</h3>
                </div>
                <div class="quick-actions-container">
                    <a href="tambah_pbj.php" class="quick-btn-custom btn-tambah">
                        <div class="icon-wrapper"><i class="fa-solid fa-plus-circle"></i></div>
                        <span>Tambah Anggaran</span>
                    </a>
                    <a href="data_pbj.php" class="quick-btn-custom btn-data">
                        <div class="icon-wrapper"><i class="fa-solid fa-database"></i></div>
                        <span>Data PBJ</span>
                    </a>
                    <a href="export_excel.php" class="quick-btn-custom btn-export">
                        <div class="icon-wrapper"><i class="fa-solid fa-file-export"></i></div>
                        <span>Export Laporan</span>
                    </a>
                    <a href="import_pbj.php" class="quick-btn-custom btn-import">
                        <div class="icon-wrapper"><i class="fa-solid fa-file-import"></i></div>
                        <span>Import Data</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // 1. Fitur Pencarian
    document.getElementById('dashboardSearch').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.link-card');
        cards.forEach(card => {
            const text = (card.dataset.search || '').toLowerCase();
            card.style.display = (keyword === '' || text.includes(keyword)) ? 'flex' : 'none';
        });
    });

    // 2. Dropdown Interaksi
    const notifBtn = document.getElementById('notifBtn');
    const mailBtn = document.getElementById('mailBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const mailDropdown = document.getElementById('mailDropdown');

    if(notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
            if(mailDropdown) mailDropdown.classList.remove('show'); 
        });
    }

    if(mailBtn && mailDropdown) {
        mailBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mailDropdown.classList.toggle('show');
            if(notifDropdown) notifDropdown.classList.remove('show'); 
        });
    }

    document.addEventListener('click', function() {
        if(notifDropdown) notifDropdown.classList.remove('show');
        if(mailDropdown) mailDropdown.classList.remove('show');
    });
</script>

</body>
</html>