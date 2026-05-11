<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// 1. CEK SESI LOGIN
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

// 2. DETEKTOR ADMIN
$role_user = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';
$isAdmin = ($role_user == 'admin' || $role_user == 'administrator' || $role_user == 'superadmin');


// 3. LOGIKA HAPUS DATA (HANYA ADMIN)
if (isset($_GET['hapus_langsung']) && $isAdmin) {
    $kode_hapus = mysqli_real_escape_string($conn, trim($_GET['hapus_langsung']));
    $delete = mysqli_query($conn, "DELETE FROM pbj WHERE kode_riset = '$kode_hapus'");
    if($delete) { header("Location: data_pbj.php?msg=hapus"); exit; }
}

if (isset($_GET['sapu_bersih']) && $isAdmin) {
    $sapu = mysqli_query($conn, "DELETE FROM pbj WHERE kode_riset = '' OR kode_riset IS NULL");
    if($sapu) { header("Location: data_pbj.php?msg=hapus"); exit; }
}

// 4. AMBIL DATA DARI DATABASE
$result = mysqli_query($conn, "SELECT * FROM pbj ORDER BY kode_riset ASC");

$totalDataPBJ = 0; 
$totalDiproses = 0; 
$totalDisetujui = 0;
$rowsPBJ = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rowsPBJ[] = $row;
        $totalDataPBJ++;
        $statusSekarang = isset($row['status']) ? $row['status'] : 'Diproses';
        if ($statusSekarang == 'Diproses') { $totalDiproses++; } 
        elseif ($statusSekarang == 'Disetujui') { $totalDisetujui++; }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data PBJ - Pusat Penelitian Kelapa Sawit</title>
    <link rel="stylesheet" href="../assets/css/dashboard_modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* --- Reset & Layout Dasar --- */
        * { transition: none !important; transform: none !important; animation: none !important; }
        .page-card { background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(248,251,247,0.92)); border: 1px solid rgba(47, 107, 61, 0.08); border-radius: 28px; padding: 24px; }
        .top-action { display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
        .btn-group { display: flex; gap: 10px; }
        .add-btn { display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border-radius: 12px; padding: 10px 16px; color: white; font-weight: 700; font-size: 13px; }
        
        /* --- Komponen Search & Notifikasi --- */
        .search-box { position: relative; width: 300px; flex-grow: 1; max-width: 350px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .search-box input { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 14px; outline: none; }
        .notif { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .sukses { background: #dcfce7; color: #166534; }
        
        /* --- Tabel Data --- */
        .tabel-pbj { width: 100%; border-collapse: collapse; background: #fff; border-radius: 15px; overflow: hidden; }
        .tabel-pbj th { background: #2f7d32; color: white; padding: 15px; font-size: 14px; }
        .tabel-pbj td { padding: 15px; border-bottom: 1px solid #f3f4f6; font-size: 13px; vertical-align: middle; color: #374151; }
        
        /* --- Menu Sidebar --- */
        .sidebar { display: flex !important; flex-direction: column !important; justify-content: flex-start !important; padding-top: 20px !important; }
        .sidebar-menu { display: flex !important; flex-direction: column !important; gap: 5px !important; margin-top: 20px !important; flex-grow: 0 !important; }
        .menu-item { margin-bottom: 0 !important; padding: 10px 15px !important; display: flex !important; align-items: center !important; text-decoration: none !important;  z-index: 999; }
        .sidebar-bottom { margin-top: auto !important; padding-bottom: 20px !important; }

        /* --- Modal Hapus --- */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000; }
        .modal-overlay.show { display: flex; }
        .modal-box { background: white; padding: 30px; border-radius: 16px; text-align: center; max-width: 350px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .modal-box h3 { margin-top: 0; color: #111827; font-size: 20px; }
        .modal-box p { color: #6b7280; font-size: 14px; line-height: 1.5; }
        .modal-actions { display: flex; justify-content: center; gap: 12px; margin-top: 25px; }
        .btn-modal-hapus { background: #ef4444; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-modal-batal { background: #e5e7eb; color: #374151; padding: 10px 20px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar">
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
                <i class="fa-solid fa-house"></i><span>Home</span>
            </a>
            <a href="summary.php" class="menu-item <?= ($currentPage == 'summary.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-column"></i><span>Ringkasan</span>
            </a>
            <a href="data_pbj.php" class="menu-item active">
                <i class="fa-solid fa-folder-open"></i><span>Data PBJ</span>
            </a>
            
            <?php if ($isAdmin) : ?>

            <?php endif; ?>
        </nav>

        <div class="sidebar-bottom">
            <a href="../logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <p class="welcome-label">PBJ Overview</p>
                <h1>Manajemen Data</h1>
            </div>
        </header>

        <section class="page-card">
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="notif sukses" id="notifBox">
                    <?= ($_GET['msg'] == 'hapus') ? '✅ Data berhasil dihapus permanen!' : '✅ Operasi berhasil dilakukan' ?>
                </div>
            <?php endif; ?>

            <div class="top-action">
                <?php if ($isAdmin) : ?>
                <div class="btn-group">
                    <a href="tambah_pbj.php" class="add-btn" style="background: #4facfe;">+ Tambah</a>
                    <a href="export_excel.php" class="add-btn" style="background: #10b981;">Export Excel</a>
                    <a href="import_pbj.php" class="add-btn" style="background: #f59e0b;">Import Excel</a>
                    <a href="data_pbj.php?sapu_bersih=1" onclick="return confirm('Yakin ingin menghapus semua data kosong?');" class="add-btn" style="background: #ef4444;">🧹 Hapus Data Kosong</a>
                </div>
                <?php else: ?>
                <div>
                    <p style="color: #6b7280; font-size: 14px;"><i>Mode Lihat (Read-Only)</i></p>
                </div>
                <?php endif; ?>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Cari data..." onkeyup="searchTable()">
                </div>
            </div>

            <div class="table-wrap">
                <table class="tabel-pbj" id="mainTable" style="table-layout: fixed; width: 100%; word-wrap: break-word;">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 13%;">Kode Riset</th>
                            <th style="width: 14%;">Kelti</th>
                            <th style="width: 43%;">Judul Penelitian</th> 
                            <th style="width: 11%;">Anggaran</th>        
                            <th style="width: 6%;">Tahun</th>
                            <?php if ($isAdmin) : ?>
                            <th style="width: 8%;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        foreach ($rowsPBJ as $row) : 
                            $kode_riset = htmlspecialchars($row['kode_riset'] ?? '', ENT_QUOTES);
                        ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++ ?></td>
                                <td style="text-align: center;"><strong><?= $kode_riset ?></strong></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row['kelti'] ?? '') ?></td>
                                <td style="text-align: center; padding-right: 15px;"><?= htmlspecialchars($row['judul_penelitian'] ?? '-') ?></td>
                                <td style="white-space: nowrap;">
                                    Rp <?= number_format((float)($row['nilai_anggaran'] ?? 0), 0, ',', '.'); ?>
                                </td>
                                <td style="text-align: center;"><?= htmlspecialchars($row['tahun'] ?? '') ?></td>
                                
                                <?php if ($isAdmin) : ?>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <a href="edit_pbj.php?kode=<?= urlencode($kode_riset) ?>" class="edit" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; background: #3b82f6; color: white; text-decoration: none;">Edit</a>
                                        <button type="button" onclick="bukaModalHapus('<?= $kode_riset ?>')" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; background: #ef4444; color: white; border: none; cursor: pointer;">Hapus</button>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        </section>
    </main>
</div>

<?php if ($isAdmin) : ?>
<div id="modalHapusElegan" class="modal-overlay">
    <div class="modal-box">
        <h3>Hapus Data?</h3>
        <p>Apakah Anda yakin ingin menghapus data dengan Kode Riset: <br><strong id="teksKodeRiset" style="color:#ef4444;"></strong>?</p>
        <div class="modal-actions">
            <a href="#" id="linkEksekusiHapus" class="btn-modal-hapus">Ya, Hapus</a>
            <button type="button" class="btn-modal-batal" onclick="tutupModalHapus()">Batal</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // Fungsi Menampilkan Modal Hapus
    function bukaModalHapus(kode_riset) {
        document.getElementById('teksKodeRiset').innerText = kode_riset;
        document.getElementById('linkEksekusiHapus').href = 'data_pbj.php?hapus_langsung=' + encodeURIComponent(kode_riset);
        document.getElementById('modalHapusElegan').classList.add('show');
    }
    
    // Fungsi Menutup Modal Hapus
    function tutupModalHapus() {
        document.getElementById('modalHapusElegan').classList.remove('show');
    }

    // Fungsi Live Search pada Tabel
    function searchTable() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let rows = document.querySelectorAll('#mainTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
        });
    }
    
    // Auto-hide Notifikasi setelah 3 detik
    setTimeout(() => { 
        let notif = document.getElementById('notifBox');
        if(notif) {
            notif.style.display = 'none'; 
        }
    }, 3000);
</script>

</body>
</html>